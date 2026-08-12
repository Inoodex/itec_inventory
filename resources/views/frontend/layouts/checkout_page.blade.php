<div id="content" class="container" role="main">
      <div id="main">
        <div id="container-main" class="col-sm-24">
        <div class="woocommerce-NoticeGroup woocommerce-NoticeGroup-checkout" style="display:none;">
            <ul class="woocommerce-error">
                <li>আপনার নাম দিন</li>
                <li>আপনার ফোন নাম্বার ইংরেজিতে দিন</li>
                <li>আপনার ইমেইল অ্যাড্রেস দিন</li>
                <li>আপনার পূর্ণ ঠিকানা দিন</li>
                <li>আপনার এরিয়া/উপজেলা দিন</li>
            </ul>
        </div>
        @if(!cartCount())
          <div class="woocommerce">
            <div class="wc-empty-cart-message">
              <div class="cart-empty woocommerce-info">আপনার শপিং ব্যাগ এখন খালি</div>
            </div>
          </div>
        @else
          <div class="main-content">
            <article id="post-8" class="post-8 page type-page status-publish hentry">
              <div class="entry-content-post">
                <div class="woocommerce">
                  <div class="woocommerce-notices-wrapper"></div>
                  <div class="woocommerce-notices-wrapper"></div>
                  <div id="extra-add-product-popup-login" style="z-index: 999999; display: none;" class="modal fade" tabindex="-1" role="dialog" aria-hidden="true">
                    <div class="modal-dialog body-wrapper" role="document">
                      <div class="modal-content">
                        <div class="modal-header">
                          <span class="title">লগইন</span>
                          <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                            <span aria-hidden="true">×</span>
                          </button>
                        </div>
                        <div class="modal-body">
                          <form style="margin:0" method="post" class="login" id="js_login">
                            <div class="result1"></div>
                            <input type="hidden" value="8b3d235bd0" name="_wpnonce_phoe_login_pop_form" id="wpnonce_phoe_login_pop_form">
                            <p class="form-row form-row-wide">
                              <label for="username">ইমেইল <span class="required">*</span>
                              </label>
                              <input type="text" class="input-text" name="username" id="username" value="">
                            </p>
                            <p class="form-row form-row-wide">
                              <label for="password">পাসওয়ার্ড <span class="required">*</span>
                              </label>
                              <input style="max-width:100%" class="input-text" type="password" name="password" id="password">
                            </p>
                            <p class="form-row">
                              <input type="hidden" id="_wpnonce" name="_wpnonce" value="fd684f83cf">
                              <input type="hidden" id="wp_http_referer1" name="_wp_http_referer" value="">
                            </p>
                            <div class="loader1" style="display:none;"></div>
                            <input type="submit" class="button" name="login" value="সাবমিট" id="login1">
                            <label for="rememberme" class="inline"> &nbsp;&nbsp; <input style="margin-top:-2px" name="rememberme" type="checkbox" id="rememberme" value="forever"> পাসওয়ার্ড মনে রাখুন </label>
                            <p class="lost_password" style="margin-top:20px;">
                              <a style="color:#f23534" href="javascript:void(0)">পাসওয়ার্ড ভুলে গিয়েছেন?</a>
                            </p>
                          </form>
                        </div>
                      </div>
                    </div>
                  </div>
                 
                  <form  method="post" class="checkout woocommerce-checkout" action="{{route('please_order')}}" enctype="multipart/form-data">
                    @csrf
                    <div style="margin-top:20px" class="col2-set" id="customer_details">
                      <div class="col-1">
                        <div class="woocommerce-billing-fields">
                          @guest()
                          <p class="form-row form-row-wide woocommerce-validated">
                            <input onclick="createNewAccount(this)" class="input-checkbox" id="createaccount" type="checkbox" name="createaccount" value="1" data-gtm-form-interact-field-id="0">
                            <label for="createaccount" class="checkbox">নতুন অ্যাকাউন্ট তৈরি করতে টিক দিন। অ্যাকাউন্ট তৈরি করে আপনি পরবর্তীতে দ্রুত কেনাকাটা করতে পারবেন এবং সবগুলি অর্ডারের সম্পর্কে বিস্তারিত দেখতে পারবেন অথবা ইতিমধ্যে অ্যাকাউন্ট তৈরি করে থাকলে 
                              <a href="{{route('my_account')}}"  class="header_login checkout_login cboxElement">লগইন করুন</a>
                              <a href="javascript:void(0)" style="display:none;" onclick="openLoginPopup()" class="header_login checkout_login cboxElement">লগইন করুন</a>
                            </label>
                          </p>
                          @endguest

                          <div id="create_account" style="display: none;">
                            <p>পাসওয়ার্ড দিয়ে এই অর্ডারটি সম্পন্ন করুন তাতে নতুন অ্যাকাউন্ট তৈরি হবে। পরবর্তী অর্ডারে ইমেইল অ্যাড্রেস এবং পাসওয়ার্ড দিয়ে লগইন করতে পারবেন।</p>
                            <p class="form-row validate-required woocommerce-invalid woocommerce-invalid-required-field" id="account_password_field" data-priority="">
                              <label for="account_password" class="">পাসওয়ার্ড দিন&nbsp; <abbr class="required" title="required">*</abbr>
                              </label>
                              <span class="woocommerce-input-wrapper password-input">
                                <input type="password" class="input-text " name="account_password" id="account_password" placeholder="পাসওয়ার্ড" value="" autocomplete="new-password">
                                <span class="show-password-input"></span>
                              </span>
                            </p>
                            <div class="clear"></div>
                          </div>
                          <p class="form-row form-row-wide validate-required" id="billing_first_name_field" data-priority="10">
                            <label for="billing_first_name" class="">নাম&nbsp; <abbr class="required" title="required">*</abbr>
                            </label>
                            <span class="woocommerce-input-wrapper">
                              <input type="text" class="input-text " name="billing_first_name" id="billing_first_name" placeholder="" value="{{$user?$user->name:''}}" autocomplete="given-name" required>
                            </span>
                          </p>
                          <p class="form-row form-row-1st validate-required validate-phone" style="width:50%;" id="billing_phone_field" data-priority="12">
                            <label for="billing_phone" class="">ফোন নাম্বার&nbsp; <abbr class="required" title="required">*</abbr>
                            </label>
                            <span class="woocommerce-input-wrapper">
                              <input type="tel" class="input-text " name="billing_phone" id="billing_phone" placeholder="" value="{{$user?$user->phone:''}}" autocomplete="tel" required>
                            </span>
                          </p>
                          <p class="form-row form-row-2nd" style="width:50%;" id="billing_alternative_phone_field" data-priority="13">
                            <label for="billing_alternative_phone" class="">জরুরী ফোন&nbsp; <span class="optional">(optional)</span>
                            </label>
                            <span class="woocommerce-input-wrapper">
                              <input type="text" class="input-text " name="billing_alternative_phone" id="billing_alternative_phone" placeholder="যদি থাকে" value="">
                            </span>
                          </p>
                          <p class="form-row form-row-wide validate-required validate-email" id="billing_email_field" data-priority="14">
                            <label for="billing_email" class="">ইমেইল&nbsp; <abbr class="required" title="required">*</abbr>
                            </label>
                            <span class="woocommerce-input-wrapper">
                              <input type="email" class="input-text " name="billing_email" id="billing_email" placeholder="" value="{{$user?$user->email:''}}" autocomplete="email username" required>
                            </span>
                          </p>
                          <p class="form-row form-row-wide address-field validate-required validate-state" id="billing_state_field" data-priority="15">
                            <label for="billing_state" class="">জেলা&nbsp; <abbr class="required" title="required">*</abbr>
                            </label>
                            <span class="woocommerce-input-wrapper">
                              <select onchange="changeDistrict(this.value,'bill_', event)" name="billing_state" id="" class="state_select select2-hidden-accessible" autocomplete="address-level1" data-placeholder="শহর" data-input-classes="" data-label="জেলা" tabindex="-1" aria-hidden="true">
                                <option value="">Select an option…</option>
                                @foreach ($districts as $district)
                                  <option value="{{$district->id}}">{{$district->name}}</option>
                                @endforeach
                              </select>
                            </span>
                          </p>
                          <p class="form-row form-row-wide validate-required" id="billing_area_field" data-priority="16">
                            <label for="billing_area" class="">এরিয়া/উপজেলা&nbsp; <abbr class="required" title="required">*</abbr>
                            </label>
                            <span class="woocommerce-input-wrapper">
                              <select name="billing_area" id="billing_area" class="select" data-placeholder="" required>
                                <option>Select an option…</option>
                                @foreach ($areas as $area)
                                  <option class="bill_dist bill_dist_{{$area->district_id}}" style="display:none;" value="{{$area->id}}">{{$area->name}}</option>
                                @endforeach
                              </select>
                            </span>
                          </p>
                          <p class="form-row form-row-wide address-field validate-required" id="billing_address_1_field" data-priority="50">
                            <label for="billing_address_1" class="">ঠিকানা&nbsp; <abbr class="required" title="required">*</abbr>
                            </label>
                            <span class="woocommerce-input-wrapper">
                              <textarea name="billing_address_1" class="input-text " id="billing_address_1" placeholder="আপনার পূর্ণ ঠিকানা দিন" rows="2" cols="5" autocomplete="address-line1" required></textarea>
                            </span>
                          </p>
                        </div>
                        <div class="woocommerce-shipping-fields">
                          <p class="form-row notes" id="order_comments_field" data-priority="">
                            <label for="order_comments" class="">অন্যান্য তথ্য&nbsp; <span class="optional">(optional)</span>
                            </label>
                            <span class="woocommerce-input-wrapper">
                              <textarea name="order_comments" class="input-text " id="order_comments" placeholder="অর্ডার বা পণ্য ডেলিভারি সংক্রান্ত আরো কোনো তথ্য থাকলে দিন" rows="2" cols="5"></textarea>
                            </span>
                          </p>
                          <p class="form-row form-row" id="ship_to_office_field" style="display:none;" data-priority="10">
                            <span class="woocommerce-input-wrapper">
                              <label class="checkbox " autocomplete="ship-to-office">
                                <input type="checkbox" class="input-checkbox custom-checkbox" name="ship_to_office" id="ship_to_office" value="1"> ডেলিভারি অ্যাড্রেসটি কি কোন অফিসের ঠিকানা?&nbsp; <span class="optional">(optional)</span>
                              </label>
                            </span>
                          </p>
                          <p class="form-row form-row update_totals_on_change" style="display:none;" id="gift_paper_field" data-priority="10">
                            <span class="woocommerce-input-wrapper">
                              <label class="checkbox " autocomplete="gift-paper">
                                <input type="checkbox" class="input-checkbox custom-checkbox" name="gift_paper" id="gift_paper" value="1"> ডেলিভারি পার্সেলটি কি গিফট পেপারে মুড়িয়ে পাঠাতে চান? অতিরিক্ত ৩০ টাকা যুক্ত হবে।&nbsp; <span class="optional">(optional)</span>
                              </label>
                            </span>
                          </p>
                          <h4 class="form-row woocommerce-validated" style="" id="ship-to-different-address">
                            <label for="ship-to-different-address-checkbox" class="checkbox">পণ্য অন্য জায়গায় পাঠানোর ঠিকানা? 
                              <input onclick="deliveryOnOtherAddress(this)" id="ship-to-different-address-checkbox" class="input-checkbox" type="checkbox" name="ship_to_different_address" value="1">
                            </label>
                          </h4>
                          <div class="shipping_address" id="shipping_address_section" style="display:none">
                            <p class="form-row form-row-wide validate-required" id="shipping_first_name_field" data-priority="10">
                              <label for="shipping_first_name" class="">নাম&nbsp; <abbr class="required" title="required">*</abbr>
                              </label>
                              <span class="woocommerce-input-wrapper">
                                <input type="text" class="input-text " name="shipping_first_name" id="shipping_first_name" placeholder="" value="" autocomplete="given-name">
                              </span>
                            </p>
                            <p class="form-row form-row-1st validate-required" style="width:50%;" id="shipping_phone_field" data-priority="12">
                              <label for="shipping_phone" class="">ফোন&nbsp; <abbr class="required" title="required">*</abbr>
                              </label>
                              <span class="woocommerce-input-wrapper">
                                <input type="text" class="input-text " name="shipping_phone" id="shipping_phone" placeholder="" value="">
                              </span>
                            </p>
                            <p class="form-row form-row-2nd" style="width:50%;" id="shipping_alternative_phone_field" data-priority="13">
                              <label for="shipping_alternative_phone" class="">জরুরী ফোন&nbsp; <span class="optional">(optional)</span>
                              </label>
                              <span class="woocommerce-input-wrapper">
                                <input type="text" class="input-text " name="shipping_alternative_phone" id="shipping_alternative_phone" placeholder="যদি থাকে" value="">
                              </span>
                            </p>
                            <p class="form-row form-row-wide" id="shipping_email_field" data-priority="14">
                              <label for="shipping_email" class="">ইমেইল&nbsp; <span class="optional">(optional)</span>
                              </label>
                              <span class="woocommerce-input-wrapper">
                                <input type="text" class="input-text " name="shipping_email" id="shipping_email" placeholder="" value="">
                              </span>
                            </p>
                            <p class="form-row form-row-wide address-field validate-required validate-state" id="shipping_state_field" data-priority="15">
                              <label for="shipping_state" class="">জেলা&nbsp; <abbr class="required" title="required">*</abbr>
                              </label>
                              <span class="woocommerce-input-wrapper">
                                <select onchange="changeDistrict(this.value,'ship_', event)" name="shipping_state" id="" class="state_select " autocomplete="address-level1" data-placeholder="শহর" data-input-classes="" data-label="জেলা">
                                  <option value="">Select an option…</option>
                                  @foreach ($districts as $district)
                                    <option value="{{$district->id}}">{{$district->name}}</option>
                                  @endforeach
                                </select>
                              </span>
                            </p>
                            <p class="form-row form-row-wide validate-required" id="shipping_area_field" data-priority="16">
                              <label for="shipping_area" class="">এরিয়া/উপজেলা&nbsp; <abbr class="required" title="required">*</abbr>
                              </label>
                              <span class="woocommerce-input-wrapper">
                                <select name="shipping_area" id="shipping_area" class="select" data-placeholder="">
                                  <option selected>Select an option…</option>
                                  @foreach ($areas as $area)
                                    <option class="ship_dist ship_dist_{{$area->district_id}}" style="display:none;" value="{{$area->id}}">{{$area->name}}</option>
                                  @endforeach
                                </select>
                              </span>
                            </p>
                            <p class="form-row form-row-wide address-field validate-required" id="shipping_address_1_field" data-priority="50">
                              <label for="shipping_address_1" class="">ঠিকানা&nbsp; <abbr class="required" title="required">*</abbr>
                              </label>
                              <span class="woocommerce-input-wrapper">
                                <textarea name="shipping_address_1" class="input-text " id="shipping_address_1" placeholder="আপনার পূর্ণ ঠিকানা দিন" rows="2" cols="5" autocomplete="address-line1"></textarea>
                              </span>
                            </p>
                          </div>
                        </div>
                      </div>
                      <div class="col-2">
                        <div id="order_review" class="woocommerce-checkout-review-order">
                          <table style="border-top:solid 1px #d9d9d9" class="shop_table woocommerce-checkout-review-order-table">
                            <tbody>
                            @php
                                $totalPrice = 0;
                                $totalNormalPrice = 0;
                                $deliveryType = getSelectedDeliveryType();
                                $selectedCoupon = getSelectedCoupon();
                                $targetedDeliveryChargeParcentage = targetedDeliveryChargeParcentage();
                            @endphp
                            @foreach (cartItems() as $key => $item)
                                @php
                                    $product = App\Models\Admin\Product::where('id', $item['product_id'])->first();
                                    $productSize = App\Models\Admin\ProductSize::join('sizes','sizes.id','=','product_sizes.size_id')
                                                    ->where('product_sizes.id', $item['size_id'])->select('product_sizes.*','sizes.name')->first();
                                    
                                    $productId = $product->id;
                                    $productName = $product->name;
                                    $productImage = $product->image;
                                    $productImage = $product->image;
                                    $sizeName = '';
                                    $price = 0;
                                    $normalPrice = 0;
                                    $quantity = $item['quantity'];

                                    if($product->is_size == '1' && $productSize && $productSize->product_id == $product->id){
                                        $sizeName = $productSize->name;
                                    }

                                    if($product->is_size == '1' && $product->size_wise_price == '1' && $productSize && $productSize->product_id == $product->id){
                                        $price = isOffer($productSize) ? $productSize->offer_price : $productSize->price;
                                        $normalPrice = $productSize->price;
                                    }else{
                                        $price = isOffer($product) ? $product->offer_price : $product->price;
                                        $normalPrice = $product->price;
                                    }

                                    $totalPrice += $price * $quantity;
                                    $totalNormalPrice += $normalPrice * $quantity;

                                    $is_discount = $normalPrice > $price;
                                    $discount = $normalPrice - $price;
                                @endphp
                                <tr class="cart_item">
                                    <td style="width:15%;text-align:left;padding:20px 13px 5px 13px !important;" class="checkout-product-thumbnail">
                                        <a href="{{asset('frontend/product_images/'.$productImage)}}">
                                            <img width="192" height="254" src="{{asset('frontend/product_images/'.$productImage)}}" class="attachment-woocommerce_thumbnail size-woocommerce_thumbnail" alt="" decoding="async" loading="lazy">
                                        </a>
                                    </td>
                                    <td style="text-align:left;padding:20px 13px 5px 13px !important" class="product-name">{{$productName}} 
                                        <strong class="product-quantity">× {{$quantity}}</strong>
                                        <br>
                                        <span class="woocommerce-Price-amount amount">
                                            <bdi>{{$price * $quantity}}&nbsp; <span class="woocommerce-Price-currencySymbol">৳</span></bdi>
                                        </span> 
                                        @if($is_discount) 
                                        &nbsp; 
                                        <del>
                                            <span class="woocommerce-Price-amount amount">
                                                <bdi>{{$normalPrice * $quantity}}&nbsp; <span class="woocommerce-Price-currencySymbol">৳</span></bdi>
                                            </span>
                                        </del>
                                        @endif
                                    </td>
                                </tr>
                              @endforeach
                            </tbody>
                            <tfoot>
                              <tr>
                                <td colspan="3">
                                  <table style="margin-top: 30px;" class="order-price-summary">
                                    <tbody>
                                      <tr class="cart-subtotal">
                                        <td style="height:30px">
                                          <span style="color:#333333">দাম: </span>
                                        </td>
                                        <td style="text-align:right">
                                          <span class="woocommerce-Price-amount amount">
                                            <bdi>{{$totalNormalPrice}}&nbsp; <span class="woocommerce-Price-currencySymbol">৳</span>
                                            </bdi>
                                          </span>
                                        </td>
                                      </tr>
                                      <tr class="cart-subtotal">
                                        <td style="height:30px">
                                          <span style="color:#333333">ছাড়:</span>
                                        </td>
                                        <td style="text-align:right">− <span class="woocommerce-Price-amount amount">
                                            <bdi>{{$totalNormalPrice - $totalPrice}}&nbsp; <span class="woocommerce-Price-currencySymbol">৳</span>
                                            </bdi>
                                          </span>
                                        </td>
                                      </tr>
                                      <tr class="cart-subtotal">
                                        <td style="height:30px">
                                          <span style="color:#333333">মোট: </span>
                                        </td>
                                        <td style="text-align:right">
                                          <span class="woocommerce-Price-amount amount">
                                            <bdi>{{$totalPrice}}&nbsp; <span class="woocommerce-Price-currencySymbol">৳</span>
                                            </bdi>
                                          </span>
                                        </td>
                                      </tr>
                                      @if($selectedCoupon['response_code'] == '3')
                                        <tr class="cart-subtotal">
                                            <td style="height:30px">
                                            <span style="color:#333333">ডিসকাউন্ট {{$selectedCoupon['coupon']['discount_type'] == '1' ? "({$selectedCoupon['coupon']['discount']} %)" : ''}}:</span>
                                            </td>
                                            <td style="text-align:right"> 
                                                @php
                                                    if($selectedCoupon['coupon']['discount_type'] == '1'){
                                                        $amount = ($selectedCoupon['coupon']['discount'] / 100) * $totalPrice;
                                                    }else{
                                                        $amount = $selectedCoupon['coupon']['discount'];
                                                    }
                                                @endphp
                                                <span class="woocommerce-Price-amount amount">
                                                    <bdi>-{{round($amount)}}&nbsp; 
                                                        <span class="woocommerce-Price-currencySymbol">৳</span>
                                                    </bdi>
                                                </span>
                                            </td>
                                        </tr>
                                        <tr class="cart-subtotal">
                                          <td style="height:30px">
                                            <span style="color:#333333">ডিসকাউন্টের পরে: </span>
                                          </td>
                                          <td style="text-align:right">
                                            <span class="woocommerce-Price-amount amount">
                                              <bdi>{{befourShippingCharge()}}&nbsp; <span class="woocommerce-Price-currencySymbol">৳</span>
                                              </bdi>
                                            </span>
                                          </td>
                                        </tr>
                                      @endif
                                      <tr class="cart-discount coupon-bookmark_299"></tr>
                                      <tr class="woocommerce-shipping-totals shipping">
                                        <th colspan="2" style="border-top:solid 1px #dee2e6;padding-top:20px !important">
                                          <span>শিপিং চার্জ:</span>
                                          <br>
                                          <br>
                                          <ul id="shipping_method" class="woocommerce-shipping-methods">
                                            @foreach(getDeliveryCharge() as $charge)
                                                <li>
                                                    <input onchange="addDeliveryChargeTocart()" type="radio" name="delivery_charge" data-index="0"   class="shipping_method" value="{{$charge->id}}" {{$deliveryType['id'] == $charge->id ? 'checked' : ''}}><label for="shipping_method_0_flat_rate1">{{$charge->name}}: <span class="woocommerce-Price-amount amount"><bdi>{{$charge->amount}}&nbsp; <span class="woocommerce-Price-currencySymbol">৳</span></bdi></span></label>
                                                </li>
                                            @endforeach
                                          </ul>
                                        </th>
                                      </tr>

                                      @if($targetedDeliveryChargeParcentage)
                                        <td colspan="2">
                                            <p style="font-size:12px; color:red; /*background:#f29434;*/ text-align:center;">সর্বনিম্ন {{$targetedDeliveryChargeParcentage->min_amount}} টাকার অর্ডার করলে ডেলিভারি চার্জ {{$targetedDeliveryChargeParcentage->charge_percentage}}% কমে যাবে।</p>
                                        </td>
                                      @endif
                                      
                                      <tr class="order-total">
                                        <td style="border-top: solid 1px #d9d9d9 !important; padding-top: 20px !important; border-bottom: solid 1px #d9d9d9 !important;padding-bottom: 20px !important;color:#333333">সর্বমোট: </td>
                                        <td style="text-align:right;border-top: solid 1px #d9d9d9 !important;padding-top: 20px !important; border-bottom: solid 1px #d9d9d9 !important;padding-bottom: 20px !important;">
                                          <strong>
                                            <span class="woocommerce-Price-amount amount">
                                              <bdi>{{getTotalcartValueWithAll()}}&nbsp; <span class="woocommerce-Price-currencySymbol">৳</span>
                                              </bdi>
                                            </span>
                                          </strong>
                                        </td>
                                      </tr>
                                    </tbody>
                                  </table>
                                </td>
                              </tr>
                            </tfoot>
                          </table>
                          <div id="payment" class="woocommerce-checkout-payment">
                            <ul class="payment_methods methods">
                                @foreach(getPaymentMethods() as $key => $method)
                                    <li class="payment_method_{{$method['name']}}">
                                        <input onclick="selectPaymentMethod('{{$method['id']}}')" id="payment_method_{{$method['name']}}" type="radio" class="input-radio" name="payment_method" value="{{$method['id']}}" data-order_button_text="" {{$key==1 ? 'checked' : ''}}>
                                        <label for="payment_method_{{$method['name']}}"> {{$method['name']}} 
                                            @if($method['image'] != "")
                                                <img src="{{asset('frontend/images/'.$method['image'])}}" alt="{{$method['name']}}">
                                            @endif
                                        </label>
                                        <div id="payment_method_{{$method['id']}}" class="payment_box payment_method" style="display:none;">
                                            <p>{{$method['remarks']}}</p>
                                        </div>
                                    </li>
                                @endforeach
                            </ul>
                            <div class="form-row place-order">
                              <noscript>Since your browser does not support JavaScript, or it is disabled, please ensure you click the <em>Update Totals</em> button before placing your order. You may be charged more than the amount stated above if you fail to do so. <br />
                                <input type="submit" class="button alt" name="woocommerce_checkout_update_totals" value="Update totals" />
                              </noscript>
                              <input type="hidden" id="_wpnonce" name="_wpnonce" value="64787956ae">
                              <input type="hidden" name="_wp_http_referer" value="/?wc-ajax=update_order_review">
                              <p class="form-row terms" style="display:none;">
                                <input type="checkbox" class="input-checkbox" name="terms" checked="" id="terms">
                                <label for="terms" class="checkbox">
                                  <a href="javascript:void(0)" target="_blank">শর্তাবলী</a> জেনে অর্ডার করছি </label>
                              </p>
                              @if(isPermitedForOrder())
                                <button type="submit" class="button alt" name="woocommerce_checkout_place_order" id="place_order" data-value="অর্ডার সম্পন্ন করুন">অর্ডার সম্পন্ন করুন</button>
                              @else
                                <button type="submit" class="button alt" id="place_order" style="background:#e8c6a3 !important; cursor: not-allowed !important;" disabled>অর্ডার সম্পন্ন করুন</button>
                              @endif
                            </div>
                            <div class="clear"></div>
                          </div>
                        </div>
                      </div>
                    </div>
                  </form>
                  <script type="text/javascript">
                    // jQuery(document).ready(function() {
                    //   "use strict";
                    //   jQuery(document).on('change', 'input.checkout-method', function(event) {
                    //     if (jQuery(this).val() == 'account' && jQuery(this).is(":checked")) {
                    //       jQuery('.accordion-createaccount').removeClass('hidden');
                    //       jQuery('#collapse-login-regis').find('input.next_co_btn').attr('rel', 'accordion-account');
                    //       jQuery('._old_counter').addClass('hidden');
                    //       jQuery('._new_counter').removeClass('hidden');
                    //     } else {
                    //       jQuery('.accordion-createaccount').addClass('hidden');
                    //       jQuery('#collapse-login-regis').find('input.next_co_btn').attr('rel', 'accordion-billing');
                    //       jQuery('._old_counter').removeClass('hidden');
                    //       jQuery('._new_counter').addClass('hidden');
                    //     }
                    //   });
                    //   jQuery('input.checkout-method').trigger('change');
                    //   jQuery(document).on('click', '.next_co_btn', function() {
                    //     var _next_id = '#' + jQuery(this).attr('rel');
                    //     jQuery('.accordion-group').not(_next_id).find('.accordion-body').each(function(index, value) {
                    //       if (jQuery(value).hasClass('in')) jQuery(value).siblings('.accordion-heading').children('a.accordion-toggle').trigger('click');
                    //     });
                    //     if (!jQuery(_next_id).find('.accordion-body').hasClass('in')) {
                    //       jQuery(_next_id).find('.accordion-body').siblings('.accordion-heading').children('a.accordion-toggle').trigger('click');
                    //     }
                    //   });
                    // });
                  </script>
                </div>
              </div>
              <!-- .entry-content -->
              <footer class="entry-meta"></footer>
              <!-- .entry-meta -->
            </article>
            <!-- #post -->
          </div>
        @endif
        </div>
      </div>
</div>