<?php

use App\Models\Admin\Coupon;
use App\Models\Admin\DelivaryCharge;
use App\Models\Admin\DeliveryPercentage;
use App\Models\Product;
use App\Models\Admin\ProductSize;
use Illuminate\Support\Facades\Session;

if (!function_exists('cartDetails')) {
    function cartDetails()
    {
        return Session::get('cart_details', []);
    }
}

if (!function_exists('removecartDetails')) {
    function removecartDetails()
    {
        return Session::put('cart_details', []);
    }
}

if (!function_exists('getDeliveryTypeById')) {
    function getDeliveryTypeById($id)
    {
        return DelivaryCharge::where('id', $id)->first();
    }
}

if (!function_exists('getSelectedDeliveryType')) {
    function getSelectedDeliveryType()
    {
        $details = cartDetails();
        $deliveryType = getDeliveryTypeById(isset($details['delivery_type']) ? $details['delivery_type'] : null);
        $percent = deliveryChargeParcentage();
        $amount = $deliveryType ? $deliveryType->amount : 0;
        return [
            'id' => $deliveryType ? $deliveryType->id : null,
            'amount' => round($amount - ($percent / 100) * $amount),
        ];
    }
}

if (!function_exists('cartItems')) {
    function cartItems()
    {
        return Session::get('cart', []);
    }
}

if (!function_exists('cartCount')) {
    function cartCount()
    {
        return count(cartItems());
    }
}

if (!function_exists('clearCart')) {
    function clearCart()
    {
        Session::put('cart', []);
        Session::put('cart_details', []);
    }
}

if (!function_exists('getTotalcartValue')) {
    function getTotalcartValue()
    {
        $cart = cartItems();
        $currectPrice = 0;
        foreach ($cart as $item) {
            $pro = Product::where('id', $item['product_id'])->first();
            $proSize = ProductSize::where('id', $item['size_id'])->first();
            if ($pro && !($pro->is_size == '1' && !$proSize) && (!$proSize || ($proSize && $pro->id == $proSize->product_id))) {
                if ($pro->is_size == '1' && $pro->size_wise_price == '1') {
                    if (isOffer($proSize)) $currectPrice += $proSize->offer_price * $item['quantity'];
                    else $currectPrice += $proSize->price * $item['quantity'];
                } else {
                    if (isOffer($pro)) $currectPrice += $pro->offer_price * $item['quantity'];
                    else $currectPrice += $pro->price * $item['quantity'];
                }
            }
        }
        return $currectPrice;
    }
}

if (!function_exists('befourShippingCharge')) {
    function befourShippingCharge()
    {
        $total = getTotalcartValue();
        $couponDetails = getSelectedCoupon();
        $discount = 0;
        if ($couponDetails['response_code'] == '3') {
            if ($couponDetails['coupon']['discount_type'] == '1') {
                $discount = ($couponDetails['coupon']['discount'] / 100) * $total;
            } else {
                $discount = $couponDetails['coupon']['discount'];
            }
        }
        return $total - round($discount);
    }
}

if (!function_exists('getTotalcartValueWithAll')) {
    function getTotalcartValueWithAll()
    {
        $afterDicount = befourShippingCharge();
        $deliveryType = getSelectedDeliveryType();
        return $afterDicount + $deliveryType['amount'];
    }
}

if (!function_exists('cartMiniView')) {
    function cartMiniView()
    {
        return view('frontend.layouts.mini_cart');
    }
}

if (!function_exists('cartView')) {
    function cartView()
    {
        return view('frontend.layouts.cart');
    }
}

if (!function_exists('isPermitedForOrder')) {
    function isPermitedForOrder()
    {
        $deliveryType = getSelectedDeliveryType();
        return $deliveryType['id'] > 0;
    }
}

if (!function_exists('checkCoupon')) {
    function checkCoupon($coupon)
    {
        if (!(is_object($coupon) || is_array($coupon))) {
            $coupon = Coupon::where('id', $coupon)->first();
            if (!$coupon) {
                return ['response_code' => 1];
            }
        }
        if (is_object($coupon)) {
            $coupon = $coupon->toArray();
        }
        if (!count($coupon)) {
            return ['response_code' => 1];
        }

        $expiresAtTimestamp = strtotime($coupon['expires_at']);
        $currentTimestamp = time();
        if ($expiresAtTimestamp > $currentTimestamp && $coupon['status'] == '1') {
            return [
                'response_code' => 3,
                'coupon' => $coupon,
            ];
        } else {
            return ['response_code' => 2];
        }
    }
}

if (!function_exists('getCouponDetails')) {
    function getCouponDetails($coupon)
    {
        $coupon_code = $coupon;
        $coupons = Coupon::where('code', $coupon)->get();
        $response = 1;
        $couponData = null;
        foreach ($coupons as $item) {
            $data = checkCoupon($item);
            if ($data['response_code'] >= $response) {
                $response = $data['response_code'];
                if ($response == 3) {
                    if (!$couponData) $couponData = $data['coupon'];
                    else {
                        if ($data['coupon']['discount'] > $couponData['discount']) {
                            $couponData['discount'] = $data['coupon']['discount'];
                        }
                    }
                }
            }
        }
        return [
            'coupon_code' => $coupon_code,
            'response_code' => $response,
            'coupon' => $couponData,
        ];
    }
}

if (!function_exists('getSelectedCoupon')) {
    function getSelectedCoupon()
    {
        $details = cartDetails();
        return getCouponDetails(isset($details['coupon']) ? $details['coupon'] : null);
    }
}

if (!function_exists('deliveryChargeParcentage')) {
    function deliveryChargeParcentage()
    {
        $providedValue = befourShippingCharge();
        $closestMatch = DeliveryPercentage::where('min_amount', '<=', $providedValue)
            ->where('status', '1')
            ->orderBy('min_amount', 'desc')
            ->first();
        return $closestMatch ? $closestMatch->charge_percentage : 0;
    }
}

if (!function_exists('targetedDeliveryChargeParcentage')) {
    function targetedDeliveryChargeParcentage()
    {
        $providedValue = befourShippingCharge();
        return DeliveryPercentage::where('min_amount', '>', $providedValue)
            ->where('status', '1')
            ->orderBy('min_amount', 'asc')
            ->first();
    }
}
