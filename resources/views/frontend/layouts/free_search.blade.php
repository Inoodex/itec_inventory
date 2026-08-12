

@foreach ($products as $product)
    @if($product->is_book_or_product=='1')
        <li class="ui-menu-item-reeee" style="text-align:left;">
            <a href="{{ route('productDetails' , $product->id) }}" style="padding: 0px !important;">
                <span style="color:black; padding: 0px !important;">{{$product->name}}</span>
                <span id="ui-id-152" tabindex="-1" class="ui-menu-item-wrapper" style="text-align:left; padding: 0px !important;">
                    @php
                        $writerIds = explode(',', $product->writer_id);
                    @endphp

                    @foreach ($writerIds as $index => $writerId)
                        <a href="{{route('filterProduct',['type' => 'book', 'writer' => $writerId])}}">{{ getArrayData($lib_writer, $writerId) }}</a>
                        @if($index < count($writerIds) - 1)
                            |
                        @endif
                    @endforeach
                </span>
            </a>
        </li>
    @elseif($product->is_book_or_product=='2')
        <li class="ui-menu-item-reeee" style="text-align:left;" >    
            <a href="{{ route('productDetails' , $product->id) }}" style="padding: 0px !important;"> 
                <span style="color:black; padding: 0px !important;">{{$product->name}}</span>
                <span id="ui-id-152" tabindex="-1" class="ui-menu-item-wrapper" style="text-align:left; padding: 0px !important;">
                    @php
                        $brandIds = explode(',', $product->brand_id);
                    @endphp

                    @foreach ($brandIds as $index => $brandId)
                        <a href="{{route('filterProduct',['type' => 'book', 'brand' => $brandId])}}" style="padding: 0px !important;">{{ getArrayData($lib_brand, $brandId) }}</a>
                        @if($index < count($brandIds) - 1)
                            |
                        @endif
                    @endforeach
                </span>
            </a>  
        </li>
    @endif
@endforeach