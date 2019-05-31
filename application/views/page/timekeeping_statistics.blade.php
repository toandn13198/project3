@extends('layout.template')

@section('title')
    Thống kê chấm công
@endsection

@section('content')
    <div class="row">
        <div class="col-12">
            <div class="card">
                <div class="card-title">
                </div>
                <div class="card-body">
                    <div class="row no-gutters justify-content-between mb-1">
                        <form action="" class="col-12" id="form-filter">
                            <div class="form-row">
                                <div class="form-group col-sm-2 mr-2">
                                    <label for="">Loại nhân viên</label>
                                    <select id="employee-type" name="employee-type" class="form-control input-default" name="employee-type">
                                        <option value="1" selected>Có nhóm</option>
                                        <option value="2" @if(isset($_GET['employee-type']) && $_GET['employee-type'] == 2) selected @endif >Không có nhóm</option>
                                    </select>
                                </div>
                                @if(!isset($_GET['employee-type']) || $_GET['employee-type'] == 1)
                                <div class="form-group col-sm-2 mr-2">
                                    <label for="">Phòng ban</label>
                                    <select id="department" class="form-control input-default" name="department">
                                        <option value="" selected>Chọn phòng ban</option>
                                        @foreach($departments as $department)
                                            <option  value="{{ $department['id'] }}" @if(isset($_GET['department']) && ($_GET['department'] == $department['id'])) selected @endif>{{ $department['name'] }}</option>
                                        @endforeach
                                    </select>
                                </div>
                                <div class="form-group col-sm-2 mr-2">
                                    <label for="">Nhóm</label>
                                    <select id="team" class="form-control input-default" name="team">
                                        <option value="" selected>Chọn nhóm</option>
                                        @foreach($teams as $team)
                                            <option  value="{{ $team['id'] }}" @if(isset($_GET['team']) && ($_GET['team'] == $team['id'])) selected @endif>{{ $team['name'] }}</option>
                                        @endforeach
                                    </select>
                                </div>
                                @endif
                                <div class="form-group col-sm-2 mr-2">
                                    <label for="">Nhân viên</label>
                                    <select class="form-control input-default" name="user">
                                        <option value="" selected>Chọn nhân viên</option>
                                        @foreach($users as $user)
                                            <option  value="{{ $user['id'] }}" @if(isset($_GET['user']) && ($_GET['user'] == $user['id'])) selected @endif>{{ $user['fullname'] }}</option>
                                        @endforeach
                                    </select>
                                </div>
                                <div class="form-group col-sm-3 mr-2">
                                    <label for="Lop">Chọn tháng</label>
                                    <div class="form-row">
                                        <div class="form-group col-sm-7 mr-2">
                                            <input type="text" id="month-filter" name="month" class="form-control input-default datepicker-here" data-min-view="months" data-view="months" data-date-format="mm-yyyy" value="@if(isset($_GET['month']) && $_GET['month'] != '') {{ $_GET['month'] }} @endif" autocomplete="off"/>
                                        </div>
                                        <div class="form-check col-sm-3 mr-2">
                                            <button class="btn btn-primary">Xem</button>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </form>
                    </div>
                    @if(isset($dataTimekeeping) && count($dataTimekeeping) > 0)
                        <div class="table-responsive">
                            <table class="table">
                                <thead>
                                <tr>
                                    <th>Thứ</th>
                                    <th>Ngày</th>
                                    <th>Giờ đến</th>
                                    <th>Giờ về</th>
                                    <th>Số giờ</th>
                                    <th>Tình trạng</th>
                                    <th>Ngày đi làm</th>
                                    <th>Số phút muộn</th>
                                    <th>Trừ đi muộn</th>
                                    <th>Note</th>
                                </tr>
                                </thead>
                                <tbody>
                                @for($i = 1; $i <= count($dataTimekeeping); $i ++)
                                    @php
                                        $value = $dataTimekeeping[$i];
                                    @endphp
                                    <tr class="@if($value['day_name']  === 'Chủ nhật' || $value['day_name']  === 'Thứ bảy'){{ 'bg-timekeeping' }}@endif">
                                        <th scope="row">{{ $value['day_name'] }}</th>
                                        <td>{{ $i }}</td>
                                        <td>{{ ($value['checkin'] === NULL) ? '' : date('H:i', strtotime($value['checkin'])) }}</td>
                                        <td>{{ ($value['checkout'] ==  NULL) ? '' : date('H:i', strtotime($value['checkout'])) }}</td>
                                        <td>{{ convertMinutesToHours($value['total_minutes'])}}</td>
                                        <td>
                                            @if($value['status'] === NULL)
                                            @elseif($value['status'] == 1)
                                                <span class="badge badge-info">Đủ in/out </span>
                                            @elseif($value['status'] == 0)
                                                <span class="badge badge-danger">Quên checkout </span>
                                            @elseif($value['status'] == 2)
                                                <span class="badge badge-danger">Quên checkin</span>
                                            @endif
                                        </td>
                                        <td>{{ $value['realDay'] }}</td>
                                        <td>{{ $value['total_minutes_late'] }}</td>
                                        <td>{{ ($value['deduction'] === NULL) ? '' : number_format($value['deduction']) }}</td>
                                        <td>
                                            @if($value['content_explanation'] !== NULL)
                                                <div class="text-note">{{$value['content_explanation']}}</div>
                                            @endif
                                        </td>
                                    </tr>
                                @endfor
                                </tbody>
                            </table>
                        </div>
                    @else
                        @isset($dataTimekeeping)<h3 class="text-warning">Không có dữ liệu để hiển thị!!!</h3> @endisset
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
            let month = $("#month-filter").val();
            let date =  $("#month-filter").datepicker().data('datepicker');
            if (month != ""){
                let param = month.split("-");
                date.selectDate(new Date(param[1],param[0]-1));
            }
            $("#employee-type").on('change', function () {
                let employee_type =  $(this).val();
                if (employee_type == 1){
                    $(".have-group").show();
                    $("#form-filter").submit();
                } else if (employee_type == 2){
                    $(".have-group").hide();
                    $("#form-filter").submit();
                }
            })
        })
    </script>
@endsection