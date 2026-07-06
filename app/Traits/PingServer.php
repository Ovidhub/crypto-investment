<?php

namespace App\Traits;

use App\Models\Settings;
use Illuminate\Support\Facades\Http;
use App\Models\SettingsCont;
use Illuminate\Http\Client\Response;

/**
 * ============================================================================
 * DEPRECATED - PingServer Trait
 * ============================================================================
 * 
 * This trait has been PERMANENTLY DISABLED for security reasons.
 * All external API calls have been blocked to prevent data leakage.
 * 
 * STATUS: Deprecated as of Jan 22, 2026
 * REASON: Security vulnerability - external communication without proper controls
 * 
 * This trait is kept for backward compatibility only.
 * All methods return mock success responses.
 * 
 * DO NOT RE-ENABLE external API functionality.
 * ============================================================================
 */
trait PingServer
{
    public function callServer($action, $url, $data = [])
    {
        // Disabled external server ping
        return response()->json(['status' => true, 'message' => 'Server ping disabled']);
    }


    public function fetctApi(string $url, array $data = [], string $method = 'GET'): Response
    {
        // Disabled external API calls
        return new Response(new \GuzzleHttp\Psr7\Response(
            200,
            ['Content-Type' => 'application/json'],
            json_encode(['status' => true, 'message' => 'External API disabled', 'data' => []])
        ));
    }

    public function backWithResponse(Response $response): array
    {
        $info = json_decode($response);

        if ($response->successful()) {
            if ($info->error) {
                $type = 'message';
            }
            if (!$info->error) {
                $type = 'success';
            }
            $message = $info->message;
        }

        if ($response->failed()) {
            $type = 'message';
            $message = $info->message;
        }

        return [
            'type' => $type,
            'message' => $message
        ];
    }
}