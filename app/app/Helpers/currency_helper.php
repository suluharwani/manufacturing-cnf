<?php
// File: app/Helpers/currency_helper.php

if (!function_exists('formatCurrency')) {
    function formatCurrency($value, $currencyCode = 'USD') {
        if (!is_numeric($value)) {
            return '-';
        }
        
        $currencySymbols = [
            'IDR' => 'Rp',
            'USD' => '$',
            'EUR' => '€',
            'JPY' => '¥',
            'GBP' => '£',
            'SGD' => 'S$',
            'CNY' => '¥',
            'AUD' => 'A$',
            'CAD' => 'C$',
            'CHF' => 'CHF',
            'MYR' => 'RM',
            'THB' => '฿',
            'KRW' => '₩'
        ];
        
        $symbol = $currencySymbols[$currencyCode] ?? $currencyCode;
        
        // Format angka dengan 2 desimal
        $formattedValue = number_format($value, 2, ',', '.');
        
        return $symbol . ' ' . $formattedValue;
    }
}
?>