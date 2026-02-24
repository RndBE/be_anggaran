<?php

return [
    /*
    |--------------------------------------------------------------------------
    | WhatsApp Gateway (wwebjs-api)
    |--------------------------------------------------------------------------
    | Dokumentasi: https://github.com/avoylenko/wwebjs-api
    |
    | BASE_URL  : URL server wwebjs-api (tanpa trailing slash)
    | API_KEY   : Kosongkan jika server tidak menggunakan API key
    | SESSION   : Nama session default
    | TIMEOUT   : Timeout HTTP request dalam detik
    */

    'base_url' => env('WA_BASE_URL', 'http://72.60.78.159:3000'),
    'api_key' => env('WA_API_KEY', ''),
    'session' => env('WA_SESSION', 'beacon'),
    'timeout' => env('WA_TIMEOUT', 10),
];
