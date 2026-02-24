<?php

namespace App\Services;

use App\Models\ClientCode;

class CodeGeneratorService
{
    /**
     * Generate code based on PREFIX and INSTANSI.
     * Format: <PREFIX>-<INSTANSI_SINGKAT>-<NO_URUT_2DIGIT>
     */
    public function generateClientCode(ClientCode $client): string
    {
        // Increment the counter safely
        $client->increment('counter');

        $counterFormatted = str_pad($client->counter, 2, '0', STR_PAD_LEFT);

        return "{$client->prefix}-{$client->instansi_singkat}-{$counterFormatted}";
    }
}
