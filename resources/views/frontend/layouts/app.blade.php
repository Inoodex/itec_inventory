<!DOCTYPE html>
<html lang="en" data-layout="vertical" data-topbar="light" data-sidebar="light" data-sidebar-size="lg"
    data-sidebar-image="none">

@include('frontend.layouts.head')

<body>

    <!-- Main Wrapper -->
    <div class="main-wrapper">

        @include('frontend.layouts.header')

        @include('frontend.layouts.sidebar')
        <!-- Page Wrapper -->
        <div class="page-wrapper">
            <div class="content container-fluid" style="margin:0; padding-bottom:0;">
                <!-- Alerts -->
                @include('layouts.flash-message')
                <!-- /Alerts -->
            </div>
            @yield('content')
        </div>
        <!-- /Page Wrapper -->

    </div>
    <!-- /Main Wrapper -->

    <!-- Bootstrap Core JS -->
    <script src="{{asset('assets')}}/js/bootstrap.bundle.min.js"></script>

    <!-- Select2 JS -->
    <script src="{{ asset('assets') }}/plugins/select2/js/select2.min.js"></script>

    <!-- Feather Icon JS -->
    <script src="{{asset('assets')}}/js/feather.min.js"></script>

    <!-- Slimscroll JS -->
    <script src="{{asset('assets')}}/plugins/slimscroll/jquery.slimscroll.min.js"></script>

    <!-- Chart JS -->
    <script src="{{asset('assets')}}/plugins/apexchart/apexcharts.min.js"></script>
    <script src="{{asset('assets')}}/plugins/apexchart/chart-data.js"></script>

    <!-- Theme Settings JS -->
    <script src="{{asset('assets')}}/js/theme-settings.js"></script>
    <script src="{{asset('assets')}}/js/greedynav.js"></script>

    <!-- Custom JS -->
    <script src="{{asset('assets')}}/js/script.js"></script>

    <script>
        $(document).ready(function() {
            if ($.fn.select2) {
                $('.select2').each(function() {
                    const $select = $(this);
                    const $modal = $select.closest('.modal');
                    
                    $select.select2({
                        width: '100%',
                        dropdownParent: $modal.length ? $modal : $(document.body)
                    });
                });
            }

            // Wrap every table in .table-responsive so it scrolls horizontally
            // instead of overflowing the card / getting cut off on smaller screens.
            var dpWrapTables = function () {
                $('table').each(function () {
                    var $t = $(this);
                    if ($t.parents('.table-responsive').length) return;
                    $t.wrap('<div class="table-responsive"></div>');
                });
            };
            dpWrapTables();
            setTimeout(dpWrapTables, 500);
            $(window).on('load', dpWrapTables);

            // Dropdowns inside scrollable tables: use fixed positioning so the
            // action menus are never clipped by the .table-responsive wrapper.
            if (window.bootstrap && window.bootstrap.Dropdown) {
                $('.table-responsive .dropdown-toggle').each(function () {
                    var el = this;
                    var instance = window.bootstrap.Dropdown.getInstance(el);
                    if (instance) instance.dispose();
                    new window.bootstrap.Dropdown(el, {
                        popperConfig: { strategy: 'fixed' }
                    });
                });
            }
        });
    </script>

    @stack('scripts')
</body>
</html>
