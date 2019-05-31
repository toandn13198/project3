<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="utf-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <!-- Tell the browser to be responsive to screen width -->
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="description" content="">
    <meta name="author" content="">
    <!-- Favicon icon -->
    <link rel="icon" type="image/png" sizes="16x16" href="{{ asset('elaAdmin/images/favicon.png') }}">
    <title>@yield('title')</title>
    <link href="{{ asset('css/pretty-checkbox.min.css') }}" rel="stylesheet">
    <link href="{{ asset('elaAdmin/css/lib/toastr/toastr.min.css') }}" rel="stylesheet">
    <!-- Bootstrap Core CSS -->
    <link href="{{ asset('elaAdmin/css/lib/bootstrap/bootstrap.min.css') }}" rel="stylesheet">
    {{--Picker--}}
    <link rel="stylesheet" href="{{ asset('js/daterangepicker/daterangepicker.css') }}">
    <link rel="stylesheet" href="{{ asset('js/timepicker/css/gijgo.min.css') }}">
    <link rel="stylesheet" href="{{ asset('js/air-datepicker/css/datepicker.min.css') }}">
    <link rel="stylesheet" href="{{ asset('elaAdmin/css/lib/select2/select2.min.css') }}">
    <!-- Custom CSS -->
    <link href="{{ asset('elaAdmin/css/helper.css') }}" rel="stylesheet">
    <link href="{{ asset('elaAdmin/css/style.css') }}" rel="stylesheet">
    {{--My css--}}
    <link rel="stylesheet" href="{{ asset('css/style.css') }}">
    <!-- HTML5 Shim and Respond.js IE8 support of HTML5 elements and media queries -->
    <!-- WARNING: Respond.js doesn't work if you view the page via file:** -->
    <!--[if lt IE 9]>
    <script src="https:**oss.maxcdn.com/libs/html5shiv/3.7.0/html5shiv.js"></script>
    <script src="https:**oss.maxcdn.com/libs/respond.js/1.4.2/respond.min.js"></script>
    <![endif]-->
</head>

<body class="fix-header fix-sidebar">
<div class="flower-spinner">
    <div class="bg-op"></div>
    <div class="dots-container">
        <div class="bigger-dot">
            <div class="smaller-dot"></div>
        </div>
    </div>
</div>
<!-- Preloader - style you can find in spinners.css -->
<div class="preloader">
    <svg class="circular" viewBox="25 25 50 50">
        <circle class="path" cx="50" cy="50" r="20" fill="none" stroke-width="2" stroke-miterlimit="10" /> </svg>
</div>
<!-- Main wrapper  -->
<div id="main-wrapper">
    <!-- header header  -->
    @include('layout.header')
    <!-- End header header -->
    <!-- Left Sidebar  -->
    @include('layout.left_sidebar')
    <!-- End Left Sidebar  -->
    <!-- Page wrapper  -->
    <div class="page-wrapper">
        <!-- Bread crumb -->
        <div class="row page-titles">
            <div class="col-md-5 align-self-center">
                <h3 class="text-primary">@yield('title')</h3>
            </div>
            <div class="col-md-7 align-self-center text-right">
                <i class="fa fa-clock-o mr-1" aria-hidden="true"></i><span id="timenow" class="mr-2"></span>
                @if(checkAttendance() != 0)
                <a href="{{ base_url('timekeeping/checkIn') }}" class="btn btn-info"><i class="fa fa-sign-in" aria-hidden="true"></i> Checkin</a>
                @else
                <a href="{{ base_url('timekeeping/checkOut') }}" class="btn btn-danger"><i class="fa fa-sign-out" aria-hidden="true"></i> Checkout</a>
                @endif
            </div>
        </div>
        <!-- End Bread crumb -->
        <!-- Container fluid  -->
        <div class="container-fluid">
            <!-- Start Page Content -->
            @yield('content')
            <!-- End PAge Content -->
        </div>
        <!-- End Container fluid  -->
    </div>
    <!-- End Page wrapper  -->
</div>
<!-- End Wrapper -->
<!-- All Jquery -->
<script src="{{ asset('elaAdmin/js/lib/jquery/jquery.min.js') }}"></script>
<script src="{{ asset('js/daterangepicker/moment.min.js') }}"></script>
<script>
    setInterval(() => {
        $("#timenow").html(moment().format("HH:mm:ss"));
    }, 1000);
</script>
<!-- Bootstrap tether Core JavaScript -->
<script src="{{ asset('elaAdmin/js/lib/bootstrap/js/popper.min.js') }}"></script>
<script src="{{ asset('elaAdmin/js/lib/bootstrap/js/bootstrap.min.js') }}"></script>
<script src="{{ asset('js/jquery.validate.min.js') }}"></script>
<!-- slimscrollbar scrollbar JavaScript -->
<script src="{{ asset('elaAdmin/js/jquery.slimscroll.js') }}"></script>
<!--Menu sidebar -->
<script src="{{ asset('elaAdmin/js/sidebarmenu.js') }}"></script>
<!--stickey kit -->
<script src="{{ asset('elaAdmin/js/lib/sticky-kit-master/dist/sticky-kit.min.js') }}"></script>
<!--Custom JavaScript -->
<script src="{{ asset('elaAdmin/js/scripts.js') }}"></script>
<script src="{{ asset('js/daterangepicker/daterangepicker.js') }}"></script>
<script src="{{ asset('js/timepicker/js/gijgo.min.js') }}"></script>
<script src="{{ asset('js/air-datepicker/js/datepicker.min.js') }}"></script>
<script src="{{ asset('js/air-datepicker/js/i18n/datepicker.en.js') }}"></script>
<script src="{{ asset('js/pickerConfig.js') }}"></script>
<script src="{{ asset('elaAdmin/js/lib/select2/select2.full.min.js') }}"></script>
<script src="{{ asset('elaAdmin/js/lib/toastr/toastr.min.js') }}"></script>
<script src="{{ asset('js/app.js') }}"></script>
@yield('script')

<?php
//display notification
$ci=& get_instance();
$notification = $ci->session->flashdata('notification');
if ($notification){
    echo "<script>
    $(document).ready(function () {
        noti('" . $notification['type'] . "','" . $notification['title'] ."','" . $notification['message'] . "','toast-bottom-right');
    })
</script>";
}
?>
</body>

</html>