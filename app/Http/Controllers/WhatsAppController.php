<?php

namespace App\Http\Controllers;

use App\Services\WhatsAppService;
use Illuminate\Http\Request;

class WhatsAppController extends Controller
{
    public function __construct(protected WhatsAppService $wa)
    {
    }

    /** Dashboard halaman koneksi WhatsApp — tidak ada blocking call, Alpine.js poll sendiri */
    public function index()
    {
        $session = config('whatsapp.session', 'beacon');

        return view('settings.whatsapp', [
            'session' => $session,
            'status' => 'LOADING',  // Alpine poll langsung saat init()
            'statusData' => [],
            'qrImageUrl' => $this->wa->qrImageUrl($session),
            'baseUrl' => config('whatsapp.base_url'),
        ]);
    }

    /** AJAX — polling status (dipanggil setiap beberapa detik dari frontend) */
    public function status()
    {
        $session = config('whatsapp.session', 'beacon');
        $result = $this->wa->getStatus($session);
        return response()->json($result['data'] ?? ['status' => 'UNKNOWN']);
    }

    /** Start / inisialisasi session */
    public function start()
    {
        $session = config('whatsapp.session', 'beacon');
        $result = $this->wa->startSession($session);

        return response()->json($result);
    }

    /** Terminate / putus session */
    public function terminate()
    {
        $session = config('whatsapp.session', 'beacon');
        $result = $this->wa->terminateSession($session);

        return response()->json($result);
    }

    /** Test kirim pesan (untuk debugging) */
    public function testSend(Request $request)
    {
        $request->validate([
            'phone' => 'required|string',
            'message' => 'required|string|max:1000',
        ]);

        $session = config('whatsapp.session', 'beacon');
        // Format: 6281234567890@c.us
        $to = preg_replace('/\D/', '', $request->phone) . '@c.us';

        $result = $this->wa->sendMessage($to, $request->message, $session);

        return response()->json($result);
    }
}
