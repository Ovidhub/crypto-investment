<?php

namespace App\Http\Controllers;

use App\Models\ChatConversation;
use App\Models\ChatMessage;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;

class ChatController extends Controller
{
    // Agent is online when the inbox heartbeat is fresh and they haven't set "appear offline".
    public static function agentOnline(): bool
    {
        $status = DB::table('chat_agent_status')->where('id', 1)->first();
        if (!$status) {
            return false;
        }
        if ($status->appear_offline) {
            return false;
        }
        if (!$status->last_seen_at) {
            return false;
        }
        return strtotime($status->last_seen_at) >= (time() - 90);
    }

    // Visitor sends a message (creates the conversation on first message).
    public function message(Request $request)
    {
        $data = $request->validate([
            'token' => 'nullable|string|max:64',
            'name'  => 'nullable|string|max:100',
            'email' => 'nullable|email|max:150',
            'body'  => 'required|string|max:2000',
            'page'  => 'nullable|string|max:255',
        ]);

        $conversation = null;
        if (!empty($data['token'])) {
            $conversation = ChatConversation::where('token', $data['token'])->first();
        }

        if (!$conversation) {
            $ip = $this->clientIp($request);
            $geo = $this->lookupCountry($ip);
            $conversation = ChatConversation::create([
                'token'        => Str::random(40),
                'name'         => $data['name'] ?? null,
                'email'        => $data['email'] ?? null,
                'user_id'      => Auth::check() ? Auth::id() : null,
                'ip_address'   => $ip,
                'country'      => $geo['country'] ?? null,
                'country_code' => $geo['country_code'] ?? null,
                'page_url'     => $data['page'] ?? null,
                'status'       => 'open',
            ]);
        } else {
            // keep name/email/status fresh
            $conversation->fill([
                'name'   => $conversation->name ?: ($data['name'] ?? null),
                'email'  => $conversation->email ?: ($data['email'] ?? null),
                'status' => 'open',
            ])->save();
        }

        $message = ChatMessage::create([
            'conversation_id' => $conversation->id,
            'sender'          => 'visitor',
            'body'            => $data['body'],
            'read_by_visitor' => true,
            'read_by_admin'   => false,
        ]);

        $conversation->last_message_at = now();
        $conversation->save();

        return response()->json([
            'ok'      => true,
            'token'   => $conversation->token,
            'message' => $this->formatMessage($message),
            'agent_online' => self::agentOnline(),
        ]);
    }

    // Visitor polls for new messages since a given id.
    public function poll(Request $request)
    {
        $request->validate([
            'token' => 'required|string|max:64',
            'after' => 'nullable|integer',
        ]);

        $conversation = ChatConversation::where('token', $request->token)->first();
        if (!$conversation) {
            return response()->json(['ok' => false, 'agent_online' => self::agentOnline(), 'messages' => []]);
        }

        $after = (int) $request->input('after', 0);
        $messages = ChatMessage::where('conversation_id', $conversation->id)
            ->where('id', '>', $after)
            ->orderBy('id')
            ->get();

        // mark admin messages as seen by the visitor
        ChatMessage::where('conversation_id', $conversation->id)
            ->where('sender', 'admin')
            ->where('read_by_visitor', false)
            ->update(['read_by_visitor' => true]);

        return response()->json([
            'ok'           => true,
            'agent_online' => self::agentOnline(),
            'messages'     => $messages->map(fn ($m) => $this->formatMessage($m)),
        ]);
    }

    public function status()
    {
        return response()->json(['agent_online' => self::agentOnline()]);
    }

    // Best-effort real client IP (handles Cloudflare / reverse proxies on shared hosting).
    private function clientIp(Request $request): ?string
    {
        foreach (['HTTP_CF_CONNECTING_IP', 'HTTP_X_FORWARDED_FOR', 'HTTP_X_REAL_IP'] as $h) {
            if (!empty($_SERVER[$h])) {
                $ip = trim(explode(',', $_SERVER[$h])[0]);
                if (filter_var($ip, FILTER_VALIDATE_IP)) {
                    return $ip;
                }
            }
        }
        return $request->ip();
    }

    // Resolve country from IP via a free geo service. Fails quietly (returns nulls).
    private function lookupCountry(?string $ip): array
    {
        if (!$ip || !filter_var($ip, FILTER_VALIDATE_IP, FILTER_FLAG_NO_PRIV_RANGE | FILTER_FLAG_NO_RES_RANGE)) {
            return ['country' => null, 'country_code' => null];
        }
        try {
            $ctx = stream_context_create(['http' => ['timeout' => 3, 'ignore_errors' => true]]);
            $raw = @file_get_contents("http://ip-api.com/json/{$ip}?fields=status,country,countryCode", false, $ctx);
            if ($raw) {
                $j = json_decode($raw, true);
                if (($j['status'] ?? '') === 'success') {
                    return ['country' => $j['country'] ?? null, 'country_code' => $j['countryCode'] ?? null];
                }
            }
        } catch (\Throwable $e) {
            \Log::warning('Chat geo lookup failed: ' . $e->getMessage());
        }
        return ['country' => null, 'country_code' => null];
    }

    private function formatMessage(ChatMessage $m): array
    {
        return [
            'id'     => $m->id,
            'sender' => $m->sender,
            'body'   => $m->body,
            'time'   => $m->created_at->format('H:i'),
        ];
    }
}
