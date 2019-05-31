<div class="header">
    <nav class="navbar top-navbar navbar-expand-md navbar-light">
        <!-- Logo -->
        <div class="navbar-header">
            <a class="navbar-brand" href="{{ base_url() }}">
                <!-- Logo icon -->
                <b><img src="{{ asset('images/logo.png') }}" alt="homepage" class="dark-logo" width="50%" /></b>
                <!--End Logo icon -->
            </a>
        </div>
        <!-- End Logo -->
        <div class="navbar-collapse">
            <!-- toggle and nav items -->
            <ul class="navbar-nav mr-auto mt-md-0">
                <!-- This is  -->
                <li class="nav-item"> <a class="nav-link nav-toggler hidden-md-up text-muted  " href="javascript:void(0)"><i class="mdi mdi-menu"></i></a> </li>
                <li class="nav-item m-l-10"> <a class="nav-link sidebartoggler hidden-sm-down text-muted  " href="javascript:void(0)"><i class="ti-menu"></i></a> </li>
            </ul>
            <!-- User profile and search -->
            @if(isAdmin())
                <span class="badge badge-danger">Admin</span>
            @elseif(isHR())
                <span class="badge badge-primary">HR</span>
            @elseif(isLeader())
                <span class="badge badge-success">Leader</span>
            @else
                <span class="badge badge-info">Nhân viên</span>
            @endif
            <ul class="navbar-nav my-lg-0">
                <!-- Profile -->
                <li class="nav-item dropdown">
                   <a class="nav-link dropdown-toggle text-muted  " href="#" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false"><img src="{{ asset('elaAdmin/images/users/' . $_SESSION['image']) }}" alt="user" class="profile-pic" /> {{ $_SESSION['fullname'] }}</a>
                    <div class="dropdown-menu dropdown-menu-right animated zoomIn">
                        <ul class="dropdown-user">
                            <li><a href="{{ base_url('user/changeInfo') }}"><i class="ti-user"></i> Cập nhật thông tin</a></li>
                            <li><a href="{{ base_url('user/changePass') }}"><i class="fa fa-key" aria-hidden="true"></i> Đổi mật khẩu</a></li>
                            <li><a href="{{ base_url('user/logout') }}"><i class="fa fa-power-off"></i> Đăng xuất</a></li>
                        </ul>
                    </div>
                </li>
            </ul>
        </div>
    </nav>
</div>