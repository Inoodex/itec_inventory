
<style>
  /* Container styling */
  .quantity-container-custom {
    display: flex;
    align-items: center;
    border: 1px solid #ccc;
    border-radius: 5px;
    width: 100%;
    max-width:100px !important;
    height: 40px;
    overflow: hidden;
  }

  /* Button styling */
  .quantity-btn-custom {
    width: 25%;
    height: 40px;
    background-color: #f0f0f0;
    border: none;
    font-size: 18px;
    font-weight: bold;
    cursor: pointer;
    display: flex;
    align-items: center;
    justify-content: center;
  }

  /* Input styling */
  .quantity-input-custom {
    width: 50%;
    text-align: center;
    font-size: 14px;
    border: none;
    outline: none;
    padding:0px !important;
    margin:0px !important;
  }

  /* Responsive adjustments */
  @media (max-width: 500px) {
    .quantity-container-custom {
        height: 30px;
    }
    .quantity-btn-custom {
      width: 25%;
      height: 30px;
      font-size: 12px;
    }


    .quantity-input-custom {
      font-size: 12px;
      height: 30px;
      width:50%;
      font-weight:bold;
    }
  }
</style>

<div id="content" class="container" role="main">
  <div id="main">
    <div id="container-main" class="col-sm-24" style="padding: 2px !important;">
      <div class="main-content" style="padding: 2px !important;">
        <article id="post-7" class="post-7 page type-page status-publish hentry">
          <div class="entry-content-post">
            <div class="woocommerce">
                <div class="woocommerce-notices-wrapper">
                    @if(!cartCount())
                    <div class="woocommerce-message">আপনার শপিং ব্যাগ এখন খালি।</div>
                    @endif
                </div>
                @if(cartCount())
                    <div class="col-sm-16" style="padding:0;">
                        <div id="auto-applied-coupons">
                            <div id="auto-applied-coupons"></div>
                        </div>
                        <h1 class="heading-title page-title" style="padding-left:0px;margin-top:5px">আপনার শপিং ব্যাগে {{cartCount()}} টি আইটেম আছে </h1>
                        <form class="woocommerce-cart-form" action="/cart/" method="post">
                            <table style="width:95%" class="shop_table cart woocommerce-cart-form__contents" cellspacing="0">
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
                                        <tr class="cart_item first last">
                                            <td class="product-thumbnail" style="min-width:50px;">
                                                <a href="{{asset('frontend/product_images/'.$productImage)}}">
                                                    <img decoding="async" width="192" height="254" src="{{asset('frontend/product_images/'.$productImage)}}" class="attachment-woocommerce_thumbnail size-woocommerce_thumbnail" alt="">
                                                </a>
                                                
                                            </td>
                                            <td class="product-title" >
                                                <a href="{{ route('productDetails', $productId) }}">{{$productName}}</a>
                                                <span class="wd_product_number">
                                                    <span class="product-quantity" >× {{$quantity}}</span>
                                                </span>
                                                <br>
                                                <span class="woocommerce-Price-amount amount">
                                                    <bdi>
                                                        {{$price * $quantity}}&nbsp; 
                                                        <span class="woocommerce-Price-currencySymbol">৳</span>
                                                    </bdi>
                                                </span>&nbsp;&nbsp;&nbsp;
                                                @if($is_discount) 
                                                <del>
                                                    <span class="woocommerce-Price-amount amount">
                                                        <bdi>
                                                            {{$normalPrice * $quantity}}&nbsp; 
                                                            <span class="woocommerce-Price-currencySymbol">৳</span>
                                                        </bdi>
                                                    </span>
                                                </del>
                                                @endif
                                                <br><br>
                                                <a onclick="if(confirm('Are you sure you want to delete this item?')) removeCartItem('{{$key}}', getCart()); return false;" href="javascript:void(0)" style="color:#000;font-size:18px" title="Remove this item">
                                                    <i class="fa fa-trash-o"></i>
                                                </a>
                                            </td>
                                            <td class="product-quantity" style="padding:0px; min-width: 50px; ">
                                                <div class="quantity-container-custom" style="margin-top:50px !important;">
                                                    <button type="button" onclick="qtyMinus('qty_{{$key}}'); updateCartQty('{{$key}}');" class="quantity-btn-custom">-</button>
                                                    <input onchange="qtyChange('qty_{{$key}}'); updateCartQty('{{$key}}');" id="qty_{{$key}}"  min="1" type="text" value="{{$quantity}}" class="quantity-input-custom" value="1" min="1">
                                                    <button type="button" onclick="qtyPlus('qty_{{$key}}'); updateCartQty('{{$key}}');" class="quantity-btn-custom">+</button>
                                                </div>
                                            </td>
                                        </tr>
                                    @endforeach
                                    <tr style="display:none;">
                                        <td colspan="3" style="text-align:left" class="actions">
                                        <input type="submit" class="button" name="update_cart" value="আপডেট করুন" disabled="">
                                        <a class="button bt_back_to_shop" href="">আরও বই ক্রয় করুন</a>
                                        <input type="hidden" id="woocommerce-cart-nonce" name="woocommerce-cart-nonce" value="98c888ca2e">
                                        <input type="hidden" name="_wp_http_referer" value="/cart?removed_item=1">
                                        </td>
                                    </tr>
                                </tbody>
                            </table>
                        </form>
                    </div>
                    <div class="col-sm-8" style="border-radius:3px;box-shadow:0 1px 1px 0 rgb(66 66 66 / 8%), 0 1px 3px 1px rgb(66 66 66 / 16%);border-top: solid 2px #f29434;padding-top: 10px;">
                        <div class="cart-collaterals">
                        <div class="col-sm-24">
                            <div class="cart_totals">
                                <table cellspacing="0">
                                    <tbody>
                                    <tr class="cart-subtotal">
                                        <th>দাম:</th>
                                        <td>
                                        <span class="woocommerce-Price-amount amount">
                                            <span class="woocommerce-Price-amount amount">
                                            <bdi>{{$totalNormalPrice}}&nbsp; <span class="woocommerce-Price-currencySymbol">৳</span>
                                            </bdi>
                                            </span>
                                            <span></span>
                                        </span>
                                        </td>
                                    </tr>
                                    <tr class="cart-subtotal">
                                        <th>ছাড়:</th>
                                        <td>− <span class="woocommerce-Price-amount amount">
                                            <bdi>{{$totalNormalPrice - $totalPrice}}&nbsp; <span class="woocommerce-Price-currencySymbol">৳</span>
                                            </bdi>
                                        </span>
                                        </td>
                                    </tr>
                                    <tr class="cart-subtotal">
                                        <th style="border-top:solid 1px #dee2e6;padding-top:10px"> মোট: </th>
                                        <td style="border-top:solid 1px #dee2e6;padding-top:10px">
                                        <span class="woocommerce-Price-amount amount">
                                            <bdi>{{$totalPrice}}&nbsp; <span class="woocommerce-Price-currencySymbol">৳</span>
                                            </bdi>
                                        </span>
                                        </td>
                                    </tr>
                                    @if($selectedCoupon['response_code'] == '3')
                                        <tr class="total order-total">
                                            <th style="color:#333">ডিসকাউন্ট {{$selectedCoupon['coupon']['discount_type'] == '1' ? "({$selectedCoupon['coupon']['discount']} %)" : ''}}:</th>
                                            <td>
                                            <strong>
                                                @php
                                                    if($selectedCoupon['coupon']['discount_type'] == '1'){
                                                        $amount = ($selectedCoupon['coupon']['discount'] / 100) * $totalPrice;
                                                    }else{
                                                        $amount = $selectedCoupon['coupon']['discount'];
                                                    }
                                                @endphp
                                                <span class="woocommerce-Price-amount amount">
                                                <bdi>-{{round($amount)}}&nbsp; <span class="woocommerce-Price-currencySymbol">৳</span>
                                                </bdi>
                                                </span>
                                            </strong>
                                            </td>
                                        </tr>
                                        <tr class="cart-subtotal">
                                            <th style="border-top:solid 1px #dee2e6;padding-top:10px"> ডিসকাউন্টের পরে: </th>
                                            <td style="border-top:solid 1px #dee2e6;padding-top:10px">
                                            <span class="woocommerce-Price-amount amount">
                                                <bdi>{{befourShippingCharge()}}&nbsp; <span class="woocommerce-Price-currencySymbol">৳</span>
                                                </bdi>
                                            </span>
                                            </td>
                                        </tr>
                                    @endif
                                    <tr class="woocommerce-shipping-totals shipping">
                                        <th colspan="2" style="border-top:solid 1px #dee2e6;padding-top:20px !important">
                                        <span>শিপিং চার্জ:</span>
                                        <br>
                                        <br>
                                        @php
                                        // dd(getDeliveryCharge());
                                        @endphp
                                        <ul id="shipping_method" class="woocommerce-shipping-methods">
                                            @foreach(getDeliveryCharge() as $charge)
                                                <li>
                                                    <input onchange="addDeliveryChargeTocart()" type="radio" id="delivery_charge{{$charge->id}}" name="delivery_charge" data-charge="{{$charge->charge}}" data-index="0" value="{{$charge->id}}" class="shipping_method" {{$deliveryType['id'] == $charge->id ? 'checked' : ''}}>
                                                    <label for="delivery_charge{{$charge->id}}">{{$charge->name}}: <span class="woocommerce-Price-amount amount"><bdi>{{$charge->amount}}&nbsp; <span class="woocommerce-Price-currencySymbol">৳</span></bdi></span></label>
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
                                    <tr class="total order-total">
                                        <th style="color:#333">সর্বমোট:</th>
                                        <td>
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

                                @if(($selectedCoupon['coupon_code'] == null || $selectedCoupon['coupon_code'] == ''))
                                    <div style="text-align:center;margin-top:0;margin-bottom:10px;font-size:15px padding-top: 10px;">
                                        <a id="spSection" style="font-size: 15px;" onclick="showPromoSection()">প্রমোকোড থাকলে এখানে ক্লিক করুন</a>
                                    </div>
                                @endif

                                <form id="promo_form" style="padding-top: 10px; display: {{($selectedCoupon['coupon_code'] == null || $selectedCoupon['coupon_code'] == '')  ? 'none' : 'block' }};" action="" method="post">
                                    <div class="coupon_wrapper">
                                        <div class="coupon">
                                        <div class="content_coupon">
                                            <input placeholder="প্রমোকোড থাকলে দিন" name="coupon_code" class="input-text" id="coupon_code" value="{{$selectedCoupon['coupon_code']}}">
                                            <input onclick="applyCoupon()" type="button" class="btn" name="apply_coupon" value="প্রয়োগ করুন">
                                        </div>
                                        </div>
                                    </div>
                                </form>

                                @if(isPermitedForOrder())
                                    <div class="wc-proceed-to-checkout" style="margin-top: 10px;">
                                        <a href="{{route('checkout')}}" class="checkout-button button alt wc-forward" > অর্ডার সম্পন্ন করুন</a>
                                    </div>
                                @else
                                    <div class="wc-proceed-to-checkout" style="margin-top: 10px;">
                                        <a href="javascript:void(0)" class="checkout-button button alt wc-forward" style="background:#e8c6a3 !important; cursor: not-allowed !important;" disabled> অর্ডার সম্পন্ন করুন</a>
                                    </div>
                                @endif
                            </div>
                            @if($selectedCoupon['response_code'] < 3 && $selectedCoupon['coupon_code'] != "" && $selectedCoupon['coupon_code'] != null)
                            <p style="font-size:16px; color:red; text-align:center !important; font-waight:bold;">
                                {{$selectedCoupon['response_code']==2 ? 'The coupon is Expired!' : 'The coupon is invalid!'}}
                            </p>
                            @endif
                        </div>
                        </div>
                    </div>
                    <div class="col-sm-24">
                        <div style="margin-top:20px"></div>
                    </div>
                @endif
            </div>
          </div>
          <footer class="entry-meta"></footer>
        </article>
      </div>
    </div>
  </div>
</div>
