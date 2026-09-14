<?php

defined('BASEPATH') or exit('No direct script access allowed');

if (!function_exists('ckm_format_money')) {
    /**
     * Bulletproof Currency Formatter
     */
    function ckm_format_money($amount)
    {
        $amount = (float)$amount;
        if (function_exists('get_base_currency') && function_exists('app_format_money')) {
            try {
                $currency = get_base_currency();
                if ($currency) {
                    return app_format_money($amount, $currency);
                }
            } catch (Exception $e) {
                // Fallback below
            } catch (Throwable $t) {
                // Fallback below
            }
        }
        return '$' . number_format($amount, 2);
    }
}
