<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\ChatController;
use App\Http\Controllers\Controller;
use App\Models\ChatConversation;
use App\Models\ChatMessage;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class LiveChatController extends Controller
{
    // Inbox page
    public function index()
    {
        $this->touchHeartbeat();
        $status = DB::table('chat_agent_status')->where('id', 1)->first();

        return view('admin.live-chat', [
            'title'          => 'Live Chat',
            'appear_offline' => $status ? (bool) $status->appear_offline : false,
        ]);
    }

    // JSON: conversation list (polled by the inbox)
    public function conversations(Request $request)
    {
        $this->touchHeartbeat();

        $conversations = ChatConversation::orderByRaw('COALESCE(last_message_at, created_at) DESC')
            ->limit(100)
            ->get()
            ->map(function ($c) {
                $last = ChatMessage::where('conversation_id', $c->id)->orderByDesc('id')->first();
                $unread = ChatMessage::where('conversation_id', $c->id)
                    ->where('sender', 'visitor')->where('read_by_admin', false)->count();

                return [
                    'id'           => $c->id,
                    'name'         => $c->name ?: 'Guest',
                    'email'        => $c->email,
                    'country'      => $c->country,
                    'country_code' => $c->country_code,
                    'ip'           => $c->ip_address,
                    'status'       => $c->status,
                    'unread'       => $unread,
                    'snippet'      => $last ? mb_strimwidth($last->body, 0, 40, '…') : '',
                    'time'         => optional($c->last_message_at ?: $c->created_at)->format('M d, H:i'),
                ];
            });

        return response()->json([
            'conversations'  => $conversations,
            'total_unread'   => ChatMessage::where('sender', 'visitor')->where('read_by_admin', false)->count(),
        ]);
    }

    // JSON: messages for one conversation (marks visitor messages as read)
    public function messages(Request $request, $id)
    {
        $this->touchHeartbeat();
        $conversation = ChatConversation::findOrFail($id);

        ChatMessage::where('conversation_id', $conversation->id)
            ->where('sender', 'visitor')->where('read_by_admin', false)
            ->update(['read_by_admin' => true]);

        $after = (int) $request->input('after', 0);
        $messages = ChatMessage::where('conversation_id', $conversation->id)
            ->where('id', '>', $after)
            ->orderBy('id')->get()
            ->map(fn ($m) => [
                'id'     => $m->id,
                'sender' => $m->sender,
                'body'   => $m->body,
                'time'   => $m->created_at->format('M d, H:i'),
            ]);

        return response()->json([
            'conversation' => [
                'id'           => $conversation->id,
                'name'         => $conversation->name ?: 'Guest',
                'email'        => $conversation->email,
                'country'      => $conversation->country,
                'country_code' => $conversation->country_code,
                'ip'           => $conversation->ip_address,
                'page'         => $conversation->page_url,
                'status'       => $conversation->status,
            ],
            'messages' => $messages,
        ]);
    }

    // Admin sends a reply
    public function reply(Request $request, $id)
    {
        $data = $request->validate(['body' => 'required|string|max:2000']);
        $conversation = ChatConversation::findOrFail($id);

        $message = ChatMessage::create([
            'conversation_id' => $conversation->id,
            'sender'          => 'admin',
            'body'            => $data['body'],
            'read_by_admin'   => true,
            'read_by_visitor' => false,
        ]);

        $conversation->last_message_at = now();
        $conversation->status = 'open';
        $conversation->save();
        $this->touchHeartbeat();

        return response()->json([
            'ok'      => true,
            'message' => [
                'id'     => $message->id,
                'sender' => 'admin',
                'body'   => $message->body,
                'time'   => $message->created_at->format('M d, H:i'),
            ],
        ]);
    }

    public function close($id)
    {
        $conversation = ChatConversation::findOrFail($id);
        $conversation->status = 'closed';
        $conversation->save();
        return response()->json(['ok' => true]);
    }

    // Heartbeat + optional "appear offline" toggle
    public function heartbeat(Request $request)
    {
        $this->touchHeartbeat();

        if ($request->has('appear_offline')) {
            DB::table('chat_agent_status')->where('id', 1)->update([
                'appear_offline' => filter_var($request->appear_offline, FILTER_VALIDATE_BOOLEAN),
                'updated_at'     => now(),
            ]);
        }

        return response()->json(['ok' => true, 'agent_online' => ChatController::agentOnline()]);
    }

    private function touchHeartbeat(): void
    {
        DB::table('chat_agent_status')->where('id', 1)->update([
            'last_seen_at' => now(),
            'updated_at'   => now(),
        ]);
    }
}
