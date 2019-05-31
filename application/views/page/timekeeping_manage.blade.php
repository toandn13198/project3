@extends('layout.template')

@section('title')
    Quản lý chấm công
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
                                        <td data-id="{{ $value['id'] }}" @if($value['checkin'] !== NULL && $value['id_explanation'] !== NULL)class="edit-deduction" @endif>{{ ($value['deduction'] === NULL) ? '' : number_format($value['deduction']) }}</td>
                                        <td>
                                            @if($value['id_explanation'] !== NULL)
                                                @if($value['checkin'] == NULL && $value['day_name'] != 'Chủ nhật')
                                                    <i class="fa fa-plus btn btn-primary btn-add-timekeeping" id="" aria-hidden="true" data-toggle="modal" data-target="#timekeeping" data-id="{{ $value['id_user'] }}" data-date="{{ $value['date'] }}"></i>
                                                @endif
                                                <i class="fa fa-info-circle btn btn-info btn-info-explanation" id="" aria-hidden="true" data-toggle="modal" data-target="#info-explanation" data-id="{{ $value['id_explanation'] }}"></i>
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

    {{--Modal timekeeping--}}
    <div class="modal animated bounceInRight" tabindex="-1" role="dialog" aria-labelledby="exampleModalLabel" aria-hidden="true" id="info-explanation">
        <div class="modal-dialog modal-lg" role="document">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title">Chi tiết giải trình</h5>
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true">&times;</span>
                    </button>
                </div>
                <div class="modal-body">
                    <div id="status"></div>
                    <span class="font-weight-bold">Ngày gửi: </span><span class="text-info" id="date-submit"></span><br>
                    <span class="font-weight-bold">Ngày giải trình: </span><span class="text-info" id="date-explanation"></span><br>
                    <blockquote><span class="font-weight-bold">Lý do: </span><span id="content"></span></blockquote>
                    <blockquote><span class="font-weight-bold">Người phê duyệt: </span><span id="approver" class="text-info"></span></blockquote>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-dismiss="modal">Đóng</button>
                </div>
            </div>
        </div>
    </div>

    {{--Modal form add explanation--}}
    <div class="modal animated bounceInDown" tabindex="-1" role="dialog" aria-labelledby="exampleModalLabel" aria-hidden="true" id="timekeeping">
        <div class="modal-dialog modal-lg" role="document">
            <div class="modal-content">
                <form action="{{ base_url('timekeeping/add') }}" method="POST" id="insert-timekeeping">
                    <div class="modal-header">
                        <h5 class="modal-title text-info">Thêm ngày công</h5>
                        <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                            <span aria-hidden="true">&times;</span>
                        </button>
                    </div>
                    <div class="modal-body">
                        <input type="hidden" name="id_user" value="" id="id_user">
                        <input type="hidden" name="date" value="" id="date">
                        <div class="row justify-content-between">
                            <div class="form-group col-6">
                                <label for="">Giờ đến</label>
                                <input type="text" name="checkin" id="checkin" class="form-control input-default" required>
                            </div>
                            <div class="form-group col-6">
                                <label for="">Giờ về</label>
                                <input type="text" name="checkout" id="checkout" class="form-control input-default" required>
                            </div>
                        </div>
                        <div class="error" id="error-time"></div>
                    </div>
                    <div class="modal-footer">
                        <button type="submit" class="btn btn-primary">Thêm</button>
                        <button type="button" class="btn btn-secondary" data-dismiss="modal">Đóng</button>
                    </div>
                </form>
            </div>
        </div>
    </div>
@endsection

@section('script')
    <script>
        $(document).ready(function () {
            $(".edit-deduction").on('dblclick', function (ele) {
                let value = $(this).text();
                value = value.replace(/\,/g, '');
                $(this).html("<input type='text' data-value='"+ value +"' value='"+ value +"' name='deduction' class='form-control input-default w-m-100' />");
                $(this).find('input').focus();
            });
            $(document).on('click', function (ele) {
                $(".edit-deduction").has('input').each(function (element) {
                    if ($(this).find('input').is(":focus" )){
                        return false;
                    }
                    let old_value = $(this).find('input').data('value');
                    let value = $(this).find('input').val();
                    let id = $(this).data('id');
                    $(this).text(formatMoney(value));
                    if (old_value == value) return false;
                    $.post( "{{base_url('timekeeping/updateDeduction')}}",{ id: id, deduction: value }, function( data ) {
                        if(data === "1"){
                            noti('success','','Cập nhật trừ đi muộn thành công','toast-bottom-right');
                        }else {
                            noti('error','','Cập nhật trừ đi muộn thất bại','toast-bottom-right');
                        }
                    });
                });
            });
            //config timepicker
            $('#checkin').timepicker({
                showOtherMonths: true,
                format: 'HH:MM'
            });
            $('#checkout').timepicker({
                showOtherMonths: true,
                format: 'HH:MM'
            });
            //js for form filter
            $(document).on('change', '#department,#team', function (e) {
                $("#form-filter").submit();
            });
            let month = $("#month-filter").val();
            let date =  $("#month-filter").datepicker().data('datepicker');
            if (month != ""){
                let param = month.split("-");
                date.selectDate(new Date(param[1],param[0]-1));
            }
            //
            $(".btn-add-timekeeping").on('click', function () {
                $("#id_user").val($(this).data('id'));
                $("#date").val($(this).data('date'));
            });
            //
            $(".btn-info-explanation").on('click', function () {
                let id = $(this).data('id');
                $.get( "{{ base_url('explanation/getById/') }}"+id, function( data ) {
                    data = JSON.parse(data);
                    if(data.status == 1 ){
                        $("#status").html('<h4 class="text-success"><i class="fa fa-check" aria-hidden="true"></i> Đã xác nhận</h4>');
                    }else{
                        $("#status").html('');
                    }
                    $("#date-submit").text(moment(data.date_submit).format("DD-MM-YYYY"));
                    $("#date-explanation").text(moment(data.date_explanation).format("DD-MM-YYYY"));
                    $("#content").text(data.content);
                    $("#approver").text(data.approver_fullname);
                });
            });
            //
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
            
            $("#insert-timekeeping").on("submit", function () {
                let checkin = moment($("#checkin").val(),'HH:mm');
                let checkout = moment($("#checkout").val(),'HH:mm');
                if (checkout.diff(checkin) < 0){
                    $("#error-time").html('Giờ về phải lớn hơn giờ đến');
                    return false;
                }else {
                    $("#error-time").html('Giờ về phải lớn hơn giờ đến');
                }
            })
        })
    </script>
@endsection