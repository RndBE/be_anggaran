<?php

namespace App\Services;

use App\Models\Policy;
use Exception;

class ReimbursementPolicyService
{
    public function validateTransport(string $transportDescription): bool
    {
        $desc = strtolower($transportDescription);
        $rejectedTypes = ['kereta', 'pesawat', 'bus', 'kapal', 'angkot'];

        foreach ($rejectedTypes as $rejected) {
            if (str_contains($desc, $rejected)) {
                return false;
            }
        }
        return true;
    }

    public function validateReceipt(string $receiptType): bool
    {
        if (strtolower($receiptType) === 'qris_screenshot') {
            return false;
        }
        return true;
    }

    public function splitHotelCost(float $amount, bool $isWeekend = false): array
    {
        $maxWeekday = Policy::where('key', 'HOTEL_WEEKDAY_MAX')->value('value') ?? 400000;
        $maxWeekend = Policy::where('key', 'HOTEL_WEEKEND_MAX')->value('value') ?? 500000;

        $limit = $isWeekend ? $maxWeekend : $maxWeekday;

        if ($amount <= $limit) {
            return [
                'company' => $amount,
                'employee' => 0
            ];
        }

        return [
            'company' => $limit,
            'employee' => $amount - $limit
        ];
    }

    public function validateMealCustomer(float $amount): bool
    {
        $limit = Policy::where('key', 'MEAL_CUSTOMER_MAX')->value('value') ?? 250000;
        return $amount <= $limit;
    }
}
