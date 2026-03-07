<?php

namespace App\Services;

use App\Models\AuditLog;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Request;

class AuditLogService
{
    /**
     * Catat satu baris audit log.
     *
     * @param string      $action     e.g. 'request.created', 'user.login', 'approval.approved'
     * @param Model|null  $model      Eloquent model yang terlibat (opsional)
     * @param array|null  $changes    Data sebelum/sesudah atau metadata tambahan
     */
    public static function log(string $action, ?Model $model = null, ?array $changes = null): void
    {
        try {
            AuditLog::create([
                'user_id' => Auth::id(),
                'action' => $action,
                'model_type' => $model ? class_basename($model) : null,
                'model_id' => $model?->getKey(),
                'changes' => $changes,
                'ip_address' => Request::ip(),
            ]);
        } catch (\Throwable $e) {
            // Jangan sampai gagal log menyebabkan transaksi utama gagal
            \Illuminate\Support\Facades\Log::warning('AuditLog failed: ' . $e->getMessage());
        }
    }

    /**
     * Helper: log perubahan field before/after dari model yang di-update.
     */
    public static function logChanges(string $action, Model $model, array $before, array $after): void
    {
        // Hanya simpan field yang benar-benar berubah
        $diff = [];
        foreach ($after as $key => $newVal) {
            $oldVal = $before[$key] ?? null;
            if ($oldVal != $newVal) {
                $diff[$key] = ['from' => $oldVal, 'to' => $newVal];
            }
        }

        static::log($action, $model, $diff ?: null);
    }
}
