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

        $count = DB::table('transactions')
            ->where('invoice_number', 'LIKE', "{$prefix}/{$yearMonth}/%")
            ->count();

        $nextNumber = $count + 1;

        do {
            $sequentialNumber = str_pad($nextNumber, 4, '0', STR_PAD_LEFT);
            $invoiceNumber = "{$prefix}/{$yearMonth}/{$sequentialNumber}";

            $exists = DB::table('transactions')
                ->where('invoice_number', $invoiceNumber)
                ->exists();

            if (!$exists) {
                break;
            }

            $nextNumber++;
        } while (true);

        return $invoiceNumber;
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
