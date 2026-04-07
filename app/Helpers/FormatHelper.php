<?php

namespace App\Helpers;

class FormatHelper
{
    public static function formatToIndianCurrency($amount)
    {
        if (!is_numeric($amount)) {
            return '₹0';
        }

        $amount = floatval($amount);
        if ($amount >= 1000 && $amount <100000) { // 1 Crore = 1000
            return '₹' . number_format($amount / 1000,2) . ' K';
        }elseif ($amount >= 10000000) { // 1 Crore = 10,000,000
            return '₹' . number_format($amount / 10000000, 2) . ' Cr';
        } elseif ($amount >= 100000) { // 1 Lakh = 100,000
            return '₹' . number_format($amount / 100000, 2) . ' L';
        } else {
            return '₹' . number_format($amount, 2);
        }
    }
}
