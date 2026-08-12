<?php

use App\Models\Brand;
use App\Models\Category;
use App\Models\User;
use App\Models\Admin\Area;
use App\Models\Admin\DelivaryCharge;
use App\Models\Admin\District;
use App\Models\Admin\Publisher;
use App\Models\Admin\Subject;
use App\Models\Admin\Writer;

if (!function_exists('return_library')) {
    function return_library($object, $key_col, $value_col)
    {
        $data = array();
        foreach ($object as $item) {
            $data[$item->$key_col] = $item->$value_col;
        }
        return $data;
    }
}

if (!function_exists('lib_all_category')) {
    function lib_all_category()
    {
        return return_library(Category::where('status', '1')->get(), 'id', 'name');
    }
}

if (!function_exists('lib_category')) {
    function lib_category()
    {
        return return_library(Category::where('for_book_or_product', '2')->where('status', '1')->get(), 'id', 'name');
    }
}

if (!function_exists('lib_book_category')) {
    function lib_book_category()
    {
        return return_library(Category::where('for_book_or_product', '1')->where('status', '1')->get(), 'id', 'name');
    }
}

if (!function_exists('lib_brand')) {
    function lib_brand()
    {
        return return_library(Brand::where('status', '1')->get(), 'id', 'name');
    }
}

if (!function_exists('lib_publisher')) {
    function lib_publisher()
    {
        return return_library(Publisher::where('status', '1')->get(), 'id', 'name');
    }
}

if (!function_exists('lib_writer')) {
    function lib_writer()
    {
        return return_library(Writer::where('status', '1')->get(), 'id', 'name');
    }
}

if (!function_exists('lib_subject')) {
    function lib_subject()
    {
        return return_library(Subject::where('status', '1')->get(), 'id', 'name');
    }
}

if (!function_exists('lib_serviceMan')) {
    function lib_serviceMan()
    {
        return return_library(User::where('status', '1')->get(), 'id', 'name');
    }
}

if (!function_exists('lib_salesMan')) {
    function lib_salesMan()
    {
        return return_library(User::where('status', '1')->get(), 'id', 'name');
    }
}

if (!function_exists('lib_deliveryCharge')) {
    function lib_deliveryCharge()
    {
        return return_library(DelivaryCharge::where('status', '1')->get(), 'id', 'name');
    }
}

if (!function_exists('lib_districts')) {
    function lib_districts()
    {
        return return_library(District::where('status', '1')->get(), 'id', 'name');
    }
}

if (!function_exists('lib_areas')) {
    function lib_areas()
    {
        return return_library(Area::where('status', '1')->get(), 'id', 'name');
    }
}
