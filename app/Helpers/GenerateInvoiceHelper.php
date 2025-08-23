<?php

namespace App\Helpers;

use Carbon\Carbon;
use DB;

class GenerateInvoiceHelper
{
    /**
     * Generate a unique invoice number with format: INV/YYMM/XXXX
     * Example: INV/2507/0001
     * @return string
     */
    public static function generateInvoiceNumber()
    {
        $now = Carbon::now();
        $yearMonth = $now->format('ym');
        $prefix = 'INV';

        $currentYear = $now->year();
        $currentMonth = $now->month();

        $lastInvoice = DB::table('transactions')
            ->whereYear('created_at', $currentYear)
            ->whereMonth('created_at', $currentMonth)
            ->where('invoice_number', 'LIKE', "{$prefix}/{$yearMonth}/%")
            ->orderBy('invoice_number', 'desc')
            ->first();

        $nextNumber = 1;

        if ($lastInvoice) {
            $parts = explode('/', $lastInvoice->invoice_number);
            if (count($parts) === 3) {
                $lastSequential = (int) $parts[2];
                $nextNumber = $lastSequential + 1;
            }
        }

        $sequentialNumber = str_pad($nextNumber, 4, '0', STR_PAD_LEFT);

        return "{$prefix}/{$yearMonth}/{$sequentialNumber}";
    }

    /**
     * Format the invoice date.
     *
     * @param \DateTime $date
     * @return string
     */
    public static function formatInvoiceDate(\DateTime $date)
    {
        return $date->format('Y-m-d H:i:s');
    }
}
