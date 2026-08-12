<?php

if (!function_exists('displayNotificationTime')) {
    function displayNotificationTime($timestamp)
    {
        $time_ago = strtotime($timestamp);
        $current_time = time();
        $time_difference = $current_time - $time_ago;
        $minutes = round($time_difference / 60);
        $hours = round($time_difference / 3600);
        $seconds = round($time_difference);

        if ($seconds < 60) {
            return $seconds <= 1 ? "1 second ago" : "$seconds seconds ago";
        } elseif ($minutes < 60) {
            return $minutes <= 1 ? "1 minute ago" : "$minutes minutes ago";
        } elseif ($hours < 24) {
            return $hours <= 1 ? "1 hour ago" : "$hours hours ago";
        } else {
            return date("d M \a\\t H:i", $time_ago);
        }
    }
}

if (!function_exists('_numFormate')) {
    function _numFormate($number, $digit = 2)
    {
        return number_format((float)$number, $digit);
    }
}

if (!function_exists('countWords')) {
    function countWords($string)
    {
        preg_match_all('/\p{L}+/u', $string, $matches);
        return count($matches[0]);
    }
}

if (!function_exists('limitWords')) {
    function limitWords($string, $limit)
    {
        $words = preg_split('/\s+/u', $string);
        return implode(' ', array_slice($words, 0, $limit));
    }
}

if (!function_exists('getArrayCond')) {
    function getArrayCond($column, $ids)
    {
        if (!is_array($ids)) {
            $ids = $ids->toArray();
        }
        $condition = implode(' OR ', array_map(function ($id) use ($column) {
            return "FIND_IN_SET(" . intval($id) . ", $column)";
        }, $ids));

        return "$condition";
    }
}

if (!function_exists('get_names')) {
    function get_names($data, $ids)
    {
        if (getType($ids) != "array") {
            $ids = explode(',', $ids);
        }
        $str = [];
        foreach ($ids as $id) {
            $str[] = getArrayData($data, $id);
        }
        return implode(',', $str);
    }
}

if (!function_exists('getArrayData')) {
    function getArrayData($datas, $key)
    {
        return isset($datas[$key]) ? $datas[$key] : '';
    }
}

if (!function_exists('_print')) {
    function _print($data, $exit = 0)
    {
        echo "<pre>";
        print_r($data);
        echo "</pre>";
        if (!$exit) exit;
    }
}

if (!function_exists('country_codes')) {
    function country_codes()
    {
        return [
            '+1' => ['flag' => '🇺🇸', 'code' => '+1', 'name' => 'United States'],
            '+44' => ['flag' => '🇬🇧', 'code' => '+44', 'name' => 'United Kingdom'],
            '+880' => ['flag' => '🇧🇩', 'code' => '+880', 'name' => 'Bangladesh'],
            '+91' => ['flag' => '🇮🇳', 'code' => '+91', 'name' => 'India'],
            '+92' => ['flag' => '🇵🇰', 'code' => '+92', 'name' => 'Pakistan'],
            '+93' => ['flag' => '🇦фом', 'code' => '+93', 'name' => 'Afghanistan'],
            '+94' => ['flag' => '🇱🇰', 'code' => '+94', 'name' => 'Sri Lanka'],
            '+95' => ['flag' => '🇲🇲', 'code' => '+95', 'name' => 'Myanmar'],
            '+86' => ['flag' => '🇨🇳', 'code' => '+86', 'name' => 'China'],
            '+81' => ['flag' => '🇯🇵', 'code' => '+81', 'name' => 'Japan'],
            '+82' => ['flag' => '🇰🇷', 'code' => '+82', 'name' => 'South Korea'],
            '+971' => ['flag' => '🇦🇪', 'code' => '+971', 'name' => 'United Arab Emirates'],
            '+966' => ['flag' => '🇸🇦', 'code' => '+966', 'name' => 'Saudi Arabia'],
            '+20' => ['flag' => '🇪🇬', 'code' => '+20', 'name' => 'Egypt'],
            '+33' => ['flag' => '🇫🇷', 'code' => '+33', 'name' => 'France'],
            '+49' => ['flag' => '🇩🇪', 'code' => '+49', 'name' => 'Germany'],
            '+39' => ['flag' => '🇮🇹', 'code' => '+39', 'name' => 'Italy'],
            '+34' => ['flag' => '🇪🇸', 'code' => '+34', 'name' => 'Spain'],
            '+7' => ['flag' => '🇷🇺', 'code' => '+7', 'name' => 'Russia'],
            '+61' => ['flag' => '🇦🇺', 'code' => '+61', 'name' => 'Australia'],
            '+63' => ['flag' => '🇵🇭', 'code' => '+63', 'name' => 'Philippines'],
            '+234' => ['flag' => '🇳🇬', 'code' => '+234', 'name' => 'Nigeria'],
            '+55' => ['flag' => '🇧🇷', 'code' => '+55', 'name' => 'Brazil'],
            '+27' => ['flag' => '🇿🇦', 'code' => '+27', 'name' => 'South Africa'],
            '+62' => ['flag' => '🇮🇩', 'code' => '+62', 'name' => 'Indonesia'],
            '+60' => ['flag' => '🇲🇾', 'code' => '+60', 'name' => 'Malaysia'],
            '+64' => ['flag' => '🇳🇿', 'code' => '+64', 'name' => 'New Zealand'],
            '+212' => ['flag' => '🇲🇦', 'code' => '+212', 'name' => 'Morocco'],
            '+52' => ['flag' => '🇲🇽', 'code' => '+52', 'name' => 'Mexico'],
            '+356' => ['flag' => '🇲🇹', 'code' => '+356', 'name' => 'Malta'],
        ];
    }
}

if (!function_exists('getBanglish')) {
    function getBanglish($text)
    {
        $map = [
            'অ' => 'o', 'আ' => 'a', 'ই' => 'i', 'ঈ' => 'i', 'উ' => 'u', 'ঊ' => 'u',
            'ঋ' => 'ri', 'এ' => 'e', 'ঐ' => 'oi', 'ও' => 'o', 'ঔ' => 'ou',
            'ক' => 'k', 'খ' => 'kh', 'গ' => 'g', 'ঘ' => 'gh', 'ঙ' => 'ng',
            'চ' => 'ch', 'ছ' => 'ch', 'জ' => 'j', 'ঝ' => 'jh', 'ঞ' => 'n',
            'ট' => 't', 'ঠ' => 'th', 'ড' => 'd', 'ঢ' => 'dh', 'ণ' => 'n',
            'ত' => 't', 'থ' => 'th', 'দ' => 'd', 'ধ' => 'dh', 'ন' => 'n',
            'প' => 'p', 'ফ' => 'f', 'ব' => 'b', 'ভ' => 'bh', 'ম' => 'm',
            'য' => 'y', 'র' => 'r', 'ল' => 'l', 'শ' => 'sh', 'ষ' => 'sh',
            'স' => 's', 'হ' => 'h', 'ড়' => 'r', 'ড়' => 'r', 'ঢ়' => 'r',
            'য়' => 'y', 'য়' => 'y', 'ৎ' => 't', 'ক্ষ' => 'kkh', 'জ্ঞ' => 'gg',
            'ত্র' => 'tr', 'দ্র' => 'dr', 'া' => 'a', 'ি' => 'i', 'ী' => 'i',
            'ু' => 'u', 'ূ' => 'u', 'ৃ' => 'ri', 'ে' => 'e', 'ৈ' => 'oi',
            'ো' => 'o', 'ৌ' => 'ou', 'ং' => 'ng', 'ঃ' => 'h', 'ঁ' => 'n', '্' => '',
        ];
        return strtr($text, $map);
    }
}
