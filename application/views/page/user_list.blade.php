@extends('layout.template')

@section('title')
    Quản lý nhân viên
@endsection

@section('content')
    <div class="row">
        <div class="col-12">
            <div class="card">
                <div class="card-title">
                </div>
                <div class="card-body">
                    <form action="" id="form-filter">
                        <div class="form-row">
                            <div class="form-group col-sm-2 mr-2">
                                <label for="">Loại nhân viên</label>
                                <select id="employee-type" name="employee-type" class="form-control input-default" name="employee-type">
                                    <option value="3" @if(isset($_GET['employee-type']) && $_GET['employee-type'] == 3) selected @endif>Tất cả</option>
                                    <option value="1" @if(isset($_GET['employee-type']) && $_GET['employee-type'] == 1) selected @endif>Có nhóm</option>
                                    <option value="2" @if(isset($_GET['employee-type']) && $_GET['employee-type'] == 2) selected @endif >Không có nhóm</option>
                                </select>
                            </div>
                            @if(!isset($_GET['employee-type']) || $_GET['employee-type'] != 2)
                            <div class="form-group col-sm-2 mr-2 have-group">
                                <label for="">Phòng ban</label>
                                <select id="department" class="form-control input-default" name="department">
                                    <option value="" selected>Chọn phòng ban</option>
                                    @foreach($departments as $department)
                                        <option  value="{{ $department['id'] }}" @if(isset($_GET['department']) && ($_GET['department'] == $department['id'])) selected @endif>{{ $department['name'] }}</option>
                                    @endforeach
                                </select>
                            </div>
                            <div class="form-group col-sm-2 mr-2 have-group">
                                <label for="">Nhóm</label>
                                <select id="team" class="form-control input-default" name="team">
                                    <option value="" selected>Chọn nhóm</option>
                                    @foreach($teams as $team)
                                        <option  value="{{ $team['id'] }}" @if(isset($_GET['team']) && ($_GET['team'] == $team['id'])) selected @endif>{{ $team['name'] }}</option>
                                    @endforeach
                                </select>
                            </div>
                            @endif
                        </div>
                        <div class="row no-gutters justify-content-between mb-1">
                            <div><a href="{{ base_url('user/add') }}" class="btn btn-primary">Thêm nhân viên</a></div>
                            <div>
                                <div class="input-group">
                                    <input class="form-control py-2 border-right-0 border" name="keyword" type="search" value="@isset($_GET['keyword']){{ $_GET['keyword'] }}@endisset" id="" placeholder="">
                                    <span class="input-group-append">
                                      <button class="btn btn-outline-secondary border-left-0 border" type="submit">
                                            <i class="fa fa-search"></i>
                                      </button>
                                    </span>
                                </div>
                            </div>
                        </div>
                    </form>
                    @if(count($data) > 0)
                    <div class="table-responsive">
                        <h3 class="text-info text-center">Danh sách nhân viên</h3>
                        <table class="table table-striped">
                            <thead>
                            <tr>
                                <th>#</th>
                                <th>Tên nhân viên</th>
                                <th>Ngày sinh</th>
                                <th>Giới tính</th>
                                <th>Email</th>
                                <th>Phân quyền</th>
                                <th></th>
                            </tr>
                            </thead>
                            <tbody>
                                @foreach($data as $key => $user)
                                <tr>
                                    <th scope="row">{{ ($pagi->getPage()-1)*$pagi->getLimit() + $key + 1 }}</th>
                                    <td>{{ $user['fullname'] }}</td>
                                    <td>{{ date('d-m-Y',strtotime($user['birthday'])) }}</td>
                                    <td>
                                        @if($user['gender'] == 1)
                                            Nam
                                        @else
                                            Nữ
                                        @endif
                                    </td>
                                    <td>{{ $user['email'] }}</td>
                                    <td>
                                        @if($user['role'] == 1)
                                            <span class="badge badge-info">Nhân viên</span>
                                        @elseif($user['role'] == 2)
                                            <span class="badge badge-primary">HR</span>
                                        @elseif($user['role'] == 3)
                                            <span class="badge badge-danger">Admin</span>
                                        @endif
                                    </td>
                                    <td>
                                        @if($user['role'] < get_instance()->session->role)
                                        <a href="{{ base_url('user/edit/' . $user['id']) }}"><i class="fa fa-pencil-square-o btn btn-warning" aria-hidden="true"></i></a>
                                        <a href="{{ base_url('user/delete/' . $user['id']) }}" onclick="return confirm('Bạn thực sự muốn xóa nhân viên này?')"><i class="fa fa-trash btn btn-danger" aria-hidden="true"></i></a>
                                        @endif
                                    </td>
                                </tr>
                                @endforeach
                            </tbody>
                        </table>
                        {!! $pagi->getPagination() !!}
                    </div>
                    @else
                        <h3 class="text-warning">Không có nhân viên để hiển thị!!!</h3>
                    @endif
                </div>
            </div>
        </div>
    </div>
@endsection

@section('script')
    <script>
        $(document).ready(function () {
            $(document).on('change', '#department,#team', function (e) {
                $("#form-filter").submit();
            });
            $("#employee-type").on('change', function () {
                let employee_type =  $(this).val();
                if (employee_type == 1){
                    $(".have-group").show();
                    $("#form-filter").submit();
                } else if (employee_type == 2){
                    $(".have-group").hide();
                    $("#form-filter").submit();
                }else if (employee_type == 3){
                    $(".have-group").show();
                    $("#form-filter").submit();
                }
            })
        })
    </script>
@endsection