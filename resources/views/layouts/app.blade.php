<!DOCTYPE html>
<html lang="en" data-layout="vertical" data-topbar="light" data-sidebar="light"  data-sidebar-size="lg" data-sidebar-image="none">
	
<!-- Mirrored from kanakku.dreamstechnologies.com/html/template/index.html by HTTrack Website Copier/3.x [XR&CO'2014], Sun, 24 Nov 2024 06:09:13 GMT -->
@include('frontend.layouts.head')
<body>
		
		<!-- Main Wrapper -->
		<div class="main-wrapper">
		
			@include('frontend.layouts.header')
			@include('frontend.layouts.sidebar')
			
			<!-- Page Wrapper -->
			<div class="page-wrapper">
				@yield('content')
			</div>
			<!-- /Page Wrapper -->

		</div>
		<!-- /Main Wrapper -->

        @include('frontend.layouts.right_sidebar')

		<!-- jQuery -->
		<script src="{{asset('assets')}}/js/jquery-3.7.1.min.js" type="19fcc0aa7d222f8686a0e100-text/javascript"></script>
		
		<!-- Bootstrap Core JS -->
		<script src="{{asset('assets')}}/js/bootstrap.bundle.min.js" type="19fcc0aa7d222f8686a0e100-text/javascript"></script>
		
		<!-- Feather Icon JS -->
		<script src="{{asset('assets')}}/js/feather.min.js" type="19fcc0aa7d222f8686a0e100-text/javascript"></script>
		
		<!-- Slimscroll JS -->
		<script src="{{asset('assets')}}/plugins/slimscroll/jquery.slimscroll.min.js" type="19fcc0aa7d222f8686a0e100-text/javascript"></script>
		
		<!-- Chart JS -->
		<script src="{{asset('assets')}}/plugins/apexchart/apexcharts.min.js" type="19fcc0aa7d222f8686a0e100-text/javascript"></script>
		<script src="{{asset('assets')}}/plugins/apexchart/chart-data.js" type="19fcc0aa7d222f8686a0e100-text/javascript"></script>

		<!-- Theme Settings JS -->
		<script src="{{asset('assets')}}/js/theme-settings.js" type="19fcc0aa7d222f8686a0e100-text/javascript"></script>
		<script src="{{asset('assets')}}/js/greedynav.js" type="19fcc0aa7d222f8686a0e100-text/javascript"></script>
		
		<!-- Custom JS -->
		<script src="{{asset('assets')}}/js/script.js" type="19fcc0aa7d222f8686a0e100-text/javascript"></script>

		<script src="{{asset('assets')}}/js/rocket-loader.min.js" data-cf-settings="19fcc0aa7d222f8686a0e100-|49" defer></script>
</body>

<!-- Mirrored from kanakku.dreamstechnologies.com/html/template/index.html by HTTrack Website Copier/3.x [XR&CO'2014], Sun, 24 Nov 2024 06:10:03 GMT -->
</html>