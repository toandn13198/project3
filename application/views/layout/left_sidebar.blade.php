<div class="left-sidebar">
    <!-- Sidebar scroll-->
    <div class="scroll-sidebar">
        <!-- Sidebar navigation-->
        <nav class="sidebar-nav">
            @if(isAdmin() || isHR())
            <ul id="sidebarnav">
                <li class="nav-label"></li>
                <li>
                    <a href="{{ base_url('index/dashboard') }}" aria-expanded="false">
                        <i class="fa fa-tachometer"></i><span class="hide-menu">Dashboard </span>
                    </a>
                </li>
                <li>
                    <a href="{{ base_url('department') }}" aria-expanded="false">
                        <i class="fa fa-connectdevelop" aria-hidden="true"></i><span class="hide-menu">Quản lý phòng ban </span>
                    </a>
                </li>
                <li>
                    <a href="#" class="has-arrow" aria-expanded="false">
                        <i class="fa fa-angellist" aria-hidden="true"></i><span class="hide-menu">Quản lý nhóm </span>
                    </a>
                    <ul aria-expanded="false" class="collapse">
                        <li><a href="{{ base_url('team') }}">Danh sách nhóm</a></li>
                        <li><a href="{{ base_url('team/detailMyTeam') }}">Nhóm của bạn</a></li>
                    </ul>
                </li>
                <li>
                    <a href="{{ base_url('user') }}" aria-expanded="false">
                        <i class="fa fa-users" aria-hidden="true"></i><span class="hide-menu">Quản lý nhân viên </span>
                    </a>
                </li>
                <li>
                    <a href="{{ base_url('salary') }}" aria-expanded="false">
                        <i class="fa fa-money" aria-hidden="true"></i><span class="hide-menu">Quản lý mức lương </span>
                    </a>
                </li>
                <li>
                    <a href="{{ base_url('deduction') }}" aria-expanded="false">
                        <i class="fa fa-strikethrough" aria-hidden="true"></i><span class="hide-menu">Quản lý mức trừ lương </span>
                    </a>
                </li>
                <li>
                    <a href="#" class="has-arrow" aria-expanded="false">
                        <i class="fa fa-file-text-o" aria-hidden="true"></i><span class="hide-menu">Quản lý đơn</span>
                    </a>
                    <ul aria-expanded="false" class="collapse">
                        @if(isAdmin() || isLeader())
                        <li><a href="{{ base_url('vacation/approveRequest') }}">Duyệt danh sách đơn xin nghỉ</a></li>
                        <li><a href="{{ base_url('explanation/listExplanation') }}">Duyệt danh sách đơn giải <br>trình</a></li>
                        <li><a href="{{ base_url('vacation/approved') }}">Đơn xin nghỉ đã phê duyệt</a></li>
                        <li><a href="{{ base_url('explanation/approved') }}">Đơn giải trình đã phê duyệt</a></li>
                        @endif
                        <li><a href="{{ base_url('vacation/listSent') }}">Danh sách đơn xin nghỉ đã gửi</a></li>
                        <li><a href="{{ base_url('vacation/add') }}">Thêm đơn xin nghỉ</a></li>
                    </ul>
                </li>
                <li>
                    <a href="{{ base_url('timekeeping/manage') }}" aria-expanded="false">
                        <i class="fa fa-calendar" aria-hidden="true"></i><span class="hide-menu">Quản chấm công</span>
                    </a>
                </li>
                <li>
                    <a href="#" class="has-arrow" aria-expanded="false">
                        <i class="fa fa-line-chart" aria-hidden="true"></i><span class="hide-menu">Thống kê</span>
                    </a>
                    <ul aria-expanded="false" class="collapse">
                        <li>
                            <a href="{{ base_url('salary/statistics') }}" aria-expanded="false">
                                Thống kê lương
                            </a>
                        </li>
                        <li>
                            <a href="{{ base_url('salary/detail') }}" aria-expanded="false">
                                Thống kê lương chi tiết
                            </a>
                        </li>
                        <li>
                            <a href="{{ base_url('timekeeping/statistics') }}" aria-expanded="false">
                                Thống kê chấm công
                            </a>
                        </li>
                        <li>
                            <a href="{{ base_url('vacation/statistics_vacation') }}" aria-expanded="false">
                                Thống kê ngày nghỉ
                            </a>
                        </li>
                    </ul>
                </li>
                <li>
                    <a href="#" class="has-arrow" aria-expanded="false">
                        <i class="fa fa-history" aria-hidden="true"></i><span class="hide-menu">Lịch sử</span>
                    </a>
                    <ul aria-expanded="false" class="collapse">
                        <li>
                            <a href="{{ base_url('salary/salaryHistory') }}" aria-expanded="false">
                                Xem lương
                            </a>
                        </li>
                        <li>
                            <a href="{{ base_url('timekeeping/history') }}" aria-expanded="false">
                                Lịch sử chấm công
                            </a>
                        </li>
                        <li>
                            <a href="{{ base_url('vacation/vacationHistory') }}" aria-expanded="false">
                                Lịch sử nghỉ
                            </a>
                        </li>
                    </ul>
                </li>
                @if(isAdmin())
                <li>
                    <a href="#" class="has-arrow" aria-expanded="false">
                        <i class="fa fa-upload" aria-hidden="true"></i><span class="hide-menu">Nhập dữ liệu</span>
                    </a>
                    <ul aria-expanded="false" class="collapse">
                        <li>
                            <a href="{{ base_url('timekeeping/import') }}" aria-expanded="false">
                               Chấm công
                            </a>
                        </li>
                    </ul>
                </li>
                @endif
            </ul>
            @else
                <ul id="sidebarnav">
                    <li class="nav-label"></li>
                    <li>
                        <a href="{{ base_url('user/dashboard_user') }}" aria-expanded="false">
                            <i class="fa fa-tachometer"></i><span class="hide-menu">Dashboard </span>
                        </a>
                    </li>
                    <li>
                        <a href="{{ base_url('team/detailMyTeam') }}" aria-expanded="false">
                            <i class="fa fa-angellist" aria-hidden="true"></i><span>Nhóm của bạn</span>
                        </a>
                    </li>
                    <li>
                        <a href="#" class="has-arrow" aria-expanded="false">
                            <i class="fa fa-file-text-o" aria-hidden="true"></i><span class="hide-menu">Quản lý đơn</span>
                        </a>
                        <ul aria-expanded="false" class="collapse">
                            @if(isLeader())
                            <li><a href="{{ base_url('vacation/approveRequest') }}">Duyệt danh sách đơn xin nghỉ</a></li>
                            <li><a href="{{ base_url('explanation/listExplanation') }}">Duyệt danh sách đơn giải trình</a></li>
                            <li><a href="{{ base_url('vacation/approved') }}">Đơn xin nghỉ đã phê duyệt</a></li>
                            <li><a href="{{ base_url('explanation/approved') }}">Đơn giải trình đã phê duyệt</a></li>
                            @endif
                            <li><a href="{{ base_url('vacation/listSent') }}">Danh sách đơn xin nghỉ đã gửi</a></li>
                            <li><a href="{{ base_url('vacation/add') }}">Thêm đơn xin nghỉ</a></li>
                        </ul>
                    </li>
                    <li>
                        <a href="#" class="has-arrow" aria-expanded="false">
                            <i class="fa fa-history" aria-hidden="true"></i><span class="hide-menu">Lịch sử</span>
                        </a>
                        <ul aria-expanded="false" class="collapse">
                            <li>
                                <a href="{{ base_url('salary/salaryHistory') }}" aria-expanded="false">
                                    <i class="fa fa-money" aria-hidden="true"></i><span class="hide-menu"> Xem lương</span>
                                </a>
                            </li>
                            <li>
                                <a href="{{ base_url('timekeeping/history') }}" aria-expanded="false">
                                    <i class="fa fa-calendar" aria-hidden="true"></i><span class="hide-menu"> Lịch sử chấm công</span>
                                </a>
                            </li>
                            <li>
                                <a href="{{ base_url('vacation/vacationHistory') }}" aria-expanded="false">
                                    <i class="fa fa-id-badge color-warning" aria-hidden="true"></i><span class="hide-menu"> Lịch sử nghỉ</span>
                                </a>
                            </li>
                        </ul>
                    </li>
                </ul>
            @endif

        </nav>
        <!-- End Sidebar navigation -->
    </div>
    <!-- End Sidebar scroll-->
</div>