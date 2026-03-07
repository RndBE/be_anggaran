<?php

namespace App\Services;

use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

/**
 * Service untuk komunikasi dengan wwebjs-api WhatsApp gateway.
 * Dokumentasi: https://github.com/avoylenko/wwebjs-api
 *
 * Default server : http://72.60.78.159:3000
 * Default session: beacon
 */
class WhatsAppService
{
    protected string $baseUrl;
    protected string $apiKey;
    protected int $timeout;

    public function __construct()
    {
        $this->baseUrl = rtrim(config('whatsapp.base_url', 'http://72.60.78.159:3000'), '/');
        $this->apiKey = config('whatsapp.api_key', '');
        $this->timeout = (int) config('whatsapp.timeout', 10);
    }

    /** Build HTTP client with optional API key header */
    protected function http(): \Illuminate\Http\Client\PendingRequest
    {
        $client = Http::timeout($this->timeout);

        if ($this->apiKey) {
            $client = $client->withHeaders(['x-api-key' => $this->apiKey]);
        }

        return $client;
    }

    /** GET /session/start/:sessionId — inisialisasi session baru */
    public function startSession(string $sessionId = 'beacon'): array
    {
        try {
            $resp = $this->http()->get("{$this->baseUrl}/session/start/{$sessionId}");
            return ['ok' => $resp->successful(), 'data' => $resp->json()];
        } catch (\Throwable $e) {
            Log::error('WA startSession: ' . $e->getMessage());
            return ['ok' => false, 'error' => $e->getMessage()];
        }
    }

    /** GET /session/status/:sessionId — status koneksi */
    public function getStatus(string $sessionId = 'beacon'): array
    {
        try {
            $resp = $this->http()->get("{$this->baseUrl}/session/status/{$sessionId}");
            return ['ok' => $resp->successful(), 'data' => $resp->json() ?? []];
        } catch (\Throwable $e) {
            Log::error('WA getStatus: ' . $e->getMessage());
            return ['ok' => false, 'data' => ['status' => 'UNREACHABLE'], 'error' => $e->getMessage()];
        }
    }

    /** GET /session/qr/:sessionId — QR code dalam base64 / json */
    public function getQr(string $sessionId = 'beacon'): array
    {
        try {
            $resp = $this->http()->get("{$this->baseUrl}/session/qr/{$sessionId}");
            return ['ok' => $resp->successful(), 'data' => $resp->json() ?? []];
        } catch (\Throwable $e) {
            Log::error('WA getQr: ' . $e->getMessage());
            return ['ok' => false, 'data' => [], 'error' => $e->getMessage()];
        }
    }

    /** URL untuk QR image langsung (di-embed di <img>) */
    public function qrImageUrl(string $sessionId = 'beacon'): string
    {
        $key = $this->apiKey ? "?x-api-key={$this->apiKey}" : '';
        return "{$this->baseUrl}/session/qr/{$sessionId}/image{$key}";
    }

    /** DELETE /session/terminate/:sessionId — putus koneksi */
    public function terminateSession(string $sessionId = 'beacon'): array
    {
        try {
            $resp = $this->http()->delete("{$this->baseUrl}/session/terminate/{$sessionId}");
            return ['ok' => $resp->successful(), 'data' => $resp->json() ?? []];
        } catch (\Throwable $e) {
            Log::error('WA terminateSession: ' . $e->getMessage());
            return ['ok' => false, 'error' => $e->getMessage()];
        }
    }

    /**
     * POST /client/sendMessage/:sessionId — kirim pesan teks
     *
     * wwebjs-api expects: { chatId, contentType: "string", content: "..." }
     *
     * @param  string $to      Nomor WA dalam format internasional: 6281234567890@c.us
     * @param  string $message Isi pesan
     */
    public function sendMessage(string $to, string $message, string $sessionId = 'beacon'): array
    {
        try {
            $resp = $this->http()->asJson()->post("{$this->baseUrl}/client/sendMessage/{$sessionId}", [
                'chatId' => $to,
                'contentType' => 'string',
                'content' => $message,
            ]);
            return ['ok' => $resp->successful(), 'data' => $resp->json() ?? []];
        } catch (\Throwable $e) {
            Log::error('WA sendMessage: ' . $e->getMessage());
            return ['ok' => false, 'error' => $e->getMessage()];
        }
    }
}
