


@if(cartCount())
    <div class="widget_shopping_cart_content">
        <ul class="cart_list product_list_widget ">
            @php
                $totalPrice = 0;
                $totalNormalPrice = 0;
            @endphp
            @foreach (cartItems() as $key => $item)
                @php
                    $product = App\Models\Admin\Product::where('id', $item['product_id'])->first();
                    $productSize = App\Models\Admin\ProductSize::join('sizes','sizes.id','=','product_sizes.size_id')
                                    ->where('product_sizes.id', $item['size_id'])->select('product_sizes.*','sizes.name')->first();
                    
                    $productName = $product->name;
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
                <li class="mini_cart_item">
                    <a onclick="removeCartItem('{{$key}}')" href="javascript:void(0)" class="remove" title="বাতিল করুন" data-product_sku="">×</a>
                    <a href="javascript:void(0)">
                        <img width="192" height="254" src="{{asset('frontend/product_images/'.$productImage)}}" class="attachment-woocommerce_thumbnail size-woocommerce_thumbnail" alt="" decoding="async" loading="lazy">
                        <span class="mini_cart_title">{{$productName}}</span>
                        @if($sizeName != '')
                            <span class="mini_cart_title" style="font-size:13px; font-weight: normal;">({{$sizeName}})</span>
                        @endif
                    </a>
                    <span class="quantity">
                        {{$quantity}} × 
                        <span class="woocommerce-Price-amount amount">
                            <bdi>{{$price}}&nbsp; <span class="woocommerce-Price-currencySymbol">৳</span></bdi>
                        </span>
                        @if($is_discount)
                            <del>
                                <span class="woocommerce-Price-amount amount">
                                    <bdi>{{$normalPrice}}&nbsp; <span class="woocommerce-Price-currencySymbol">৳</span></bdi>
                                </span>
                            </del>
                        @endif
                    </span>
                </li>
            @endforeach
        </ul>
        <table class="total-mini-cart">
            <tbody>
            <tr>
                <td class="w-label">মোট:</td>
                <td class="value">
                <span class="woocommerce-Price-amount amount">
                    <bdi>{{$totalNormalPrice}}&nbsp; <span class="woocommerce-Price-currencySymbol">৳</span>
                    </bdi>
                </span>
                </td>
            </tr>
            @if($totalNormalPrice > $totalPrice)
                <tr>
                    <td class="w-label">ছাড়:</td>
                    <td class="value">- <span class="woocommerce-Price-amount amount">
                        <bdi>{{$totalNormalPrice - $totalPrice}}&nbsp; <span class="woocommerce-Price-currencySymbol">৳</span>
                        </bdi>
                    </span>
                    </td>
                </tr>
            @endif
            </tbody>
        </table>
        <table class="total-mini-cart">
            <tbody>
            <tr>
                <td class="w-label">সর্বমোট:</td>
                <td class="value">
                <strong>
                    <span class="woocommerce-Price-amount amount">
                    <bdi>{{$totalPrice}}&nbsp; <span class="woocommerce-Price-currencySymbol">৳</span>
                    </bdi>
                    </span>
                </strong>
                </td>
            </tr>
            </tbody>
        </table>
        <p class="buttons">
            <a href="{{route('cart')}}" class="button wc-forward">শপিং ব্যাগ</a>
            <a href="{{route('checkout')}}" class="button checkout wc-forward">অর্ডার করুন</a>
        </p>
    </div>
@else
    <div class="widget_shopping_cart_content">
        <ul class="cart_list product_list_widget ">
            <li class="empty">আপনার শপিং ব্যাগ এ কোনো পণ্য নেই</li>
        </ul>
    </div>
@endif