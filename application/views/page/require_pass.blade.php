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
    <title>Cập nhật mật khẩu</title>
    <!-- Bootstrap Core CSS -->
    <link href="{{ asset('elaAdmin/css/lib/bootstrap/bootstrap.min.css') }}" rel="stylesheet">
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
<!-- Preloader - style you can find in spinners.css -->
<div class="preloader">
    <svg class="circular" viewBox="25 25 50 50">
        <circle class="path" cx="50" cy="50" r="20" fill="none" stroke-width="2" stroke-miterlimit="10" /> </svg>
</div>
<!-- Main wrapper  -->
<div id="main-wrapper">

    <div class="unix-login">
        <div class="container-fluid">
            <div class="row justify-content-center">
                <div class="col-lg-4">
                    <div class="login-content card">
                        <div class="login-form">
                            <h3 class="text-info text-center">Cập nhật mật khẩu</h3>
                            <small>Vui lòng cập nhật mật khẩu để tiếp tục</small>
                            <form action="{{ base_url('login/updatePass') }}" method="POST" id="update-pass">
                                <div class="form-group">
                                    <label>Mật khẩu</label>
                                    <input type="password" name="password" id="new-password" value="" class="form-control" placeholder="Mật khẩu">
                                </div>
                                <div class="form-group">
                                    <label>Nhập lại mật khẩu</label>
                                    <input type="password" name="repassword" value="" class="form-control" placeholder="Mật khẩu">
                                </div>
                                <button type="submit" class="btn btn-primary btn-flat m-t-30">Cập nhật</button>
                            </form>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

</div>
<!-- End Wrapper -->
<!-- All Jquery -->
<script src="{{ asset('elaAdmin/js/lib/jquery/jquery.min.js') }}"></script>
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

<script>
    $(document).ready(function () {
        $("#update-pass").validate({
            rules: {
                password:{
                    required: true
                },
                repassword: {
                    required: true,
                    equalTo: "#new-password"
                }
            },
            messages: {
                password:{
                    required: "Vui lòng nhập mật khẩu mới."
                },
                repassword: {
                    required: 'Vui lòng nhập lại mật khẩu mới.',
                    equalTo: 'Mật khẩu nhập lại nhập không chính xác.'
                }
            }
        });
    })
</script>
</body>

</html>