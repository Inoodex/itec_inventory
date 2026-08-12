<nav class="woocommerce-MyAccount-navigation">
    <ul>
        <li class="woocommerce-MyAccount-navigation-link woocommerce-MyAccount-navigation-link--dashboard {{request()->routeIs('customer_profile') ? 'is-active' : ''}}">
        <a href="{{route('customer_profile')}}">ড্যাশবোর্ড</a>
        </li>
        <li class="woocommerce-MyAccount-navigation-link woocommerce-MyAccount-navigation-link--orders {{(request()->routeIs('customers_orders') || request()->routeIs('customers_order_view')) ? 'is-active' : ''}}">
            <a href="{{route('customers_orders')}}">অর্ডার সমূহ</a>
        </li>
        <li style="display:none;" class="woocommerce-MyAccount-navigation-link woocommerce-MyAccount-navigation-link--edit-address">
        <a href="javascript:void(0)">ঠিকানা</a>
        </li>
        <li style="display:none;" class="woocommerce-MyAccount-navigation-link woocommerce-MyAccount-navigation-link--woo-wallet">
        <a href="javascript:void(0)">ওয়ালেট</a>
        </li>
        <li class="woocommerce-MyAccount-navigation-link woocommerce-MyAccount-navigation-link--edit-account {{request()->routeIs('customer_profile') ? 'is-active' : ''}}">
        <a href="{{route('customers_profile')}}">প্রোফাইল</a>
        </li>
        <li style="display:none;" class="woocommerce-MyAccount-navigation-link woocommerce-MyAccount-navigation-link--wishlist">
        <a href="javascript:void(0)">উইশ লিস্ট</a>
        </li>
        <li class="woocommerce-MyAccount-navigation-link woocommerce-MyAccount-navigation-link--customer-logout">
            <form id="logoutId" action="{{route('customerLogout')}}" method="post">
                @csrf
                <a onclick="document.getElementById('logoutId').submit()" href="javascript:void(0)">লগআউট</a>
            </form>
        </li>
    </ul>
</nav>