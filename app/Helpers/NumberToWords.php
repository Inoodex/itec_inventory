<?php

if (!function_exists('numberToWords')) {
    function numberToWords($number)
    {
        $ones = array("", "One", "Two", "Three", "Four", "Five", "Six", "Seven", "Eight", "Nine");
        $tens = array("", "", "Twenty", "Thirty", "Forty", "Fifty", "Sixty", "Seventy", "Eighty", "Ninety");
        $teens = array("Ten", "Eleven", "Twelve", "Thirteen", "Fourteen", "Fifteen", "Sixteen", "Seventeen", "Eighteen", "Nineteen");

        if ($number == 0) return "Zero";

        $words = "";
        $number = (float)$number;

        // Handle lakhs
        if ($number >= 100000) {
            $lakhs = floor($number / 100000);
            $words .= numberToWords($lakhs) . " Lakh ";
            $number %= 100000;
        }

        // Handle thousands
        if ($number >= 1000) {
            $thousands = floor($number / 1000);
            $words .= numberToWords($thousands) . " Thousand ";
            $number %= 1000;
        }

        // Handle hundreds
        if ($number >= 100) {
            $hundreds = floor($number / 100);
            $words .= $ones[$hundreds] . " Hundred ";
            $number %= 100;
        }

        // Handle tens and ones
        if ($number >= 20) {
            $tensDigit = floor($number / 10);
            $words .= $tens[$tensDigit] . " ";
            $number %= 10;
        } elseif ($number >= 10) {
            $words .= $teens[$number - 10] . " ";
            $number = 0;
        }

        if ($number > 0) {
            $words .= $ones[$number] . " ";
        }

        return trim($words);
    }
}
