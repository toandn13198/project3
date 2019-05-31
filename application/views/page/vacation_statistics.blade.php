@extends('layout.template')

@section('title')
    Thống kê ngày nghỉ
@endsection

@section('content')
    <div class="row">
        <div class="col-12">
            <div class="card">
                <div class="card-title">
                    <h3 class="text-info">Thống kê ngày nghỉ</h3>
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
                    @if($dataVacation)
                        <div class="mt-2">
                            <span class="mr-5">Nghỉ có phép: {{ $dataVacation['data']['allowed'] }} ngày</span>
                            <span class="mr-5">Nghỉ không phép: {{ $dataVacation['data']['unauthorized'] }} ngày</span>
                            <span>Tổng số ngày nghỉ: {{ $dataVacation['data']['allowed']+$dataVacation['data']['unauthorized'] }} ngày</span>
                        </div>
                        @if(count($dataVacation['data']['data']) > 0)
                        <div class="table-responsive">
                            <table class="table table-striped">
                                <thead>
                                <tr>
                                    <th>#</th>
                                    <th>Ngày nghỉ</th>
                                    <th>Thời gian nghỉ</th>
                                    <th>Trạng thái</th>
                                </tr>
                                </thead>
                                <tbody>
                                @foreach($dataVacation['data']['data'] as $key =>   $value)
                                    <tr>
                                        <th scope="row">{{ $key +1 }}</th>
                                        <td>{{ date('d-m-Y',strtotime($value['date'])) }}</td>
                                        <td>
                                            @if($value['number'] == 0.5)
                                                @if($value['time'] == 1)
                                                    Buổi sáng
                                                @else
                                                    Buổi chiều
                                                @endif
                                            @else
                                                Cả ngày
                                            @endif
                                        </td>
                                        <td>
                                            @if($value['status'] === NULL)
                                                {{ ($value['morning']) ? 'Sáng có phép' : 'Sáng không phép'}}<br>
                                                {{ ($value['afternoon']) ? 'Chiều có phép' : 'Chiều không phép'}}
                                            @else
                                                {{ ($value['status']) ? 'Có phép' : 'Không phép' }}
                                            @endif
                                        </td>
                                    </tr>
                                @endforeach
                                </tbody>
                            </table>
                        </div>
                        @else
                            <h4 class="text-warning mt-2">Không có ngày nghỉ nào</h4>
                        @endif
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