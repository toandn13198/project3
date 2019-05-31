@extends('layout.template')

@section('title')
    Lịch sử chấm công
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
                            <div class="form-group">
                                <label for="Lop">Chọn tháng</label>
                                <div class="form-row">
                                    <div class="form-group col-sm-3 mr-2">
                                        <input type="text" id="month-filter" name="month" class="form-control input-default datepicker-here" data-min-view="months" data-view="months" data-date-format="mm-yyyy" value="@if(isset($_GET['month']) && $_GET['month'] != '') {{ $_GET['month'] }} @endif" autocomplete="off"/>
                                    </div>
                                    <div class="form-check col-sm-3 mr-2">
                                        <button class="btn btn-primary">Xem</button>
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
                                    @if($value['day_name'] != 'Chủ nhật')
                                        @if($value['id_explanation'] == NULL)
                                        <i class="fa fa-plus btn btn-primary btn-add-explanation" id="" aria-hidden="true" data-toggle="modal" data-target="#add-explanation" data-date="{{ $value['date'] }}"></i>
                                        @else
                                        <i class="fa fa-info-circle btn btn-info btn-info-explanation" id="" aria-hidden="true" data-toggle="modal" data-target="#explanation" data-id="{{ $value['id_explanation'] }}"></i>
                                        @endif
                                    @endif
                                </td>
                            </tr>
                            @endfor
                            </tbody>
                        </table>
                    </div>
                    @else
                    @endif
                </div>

            </div>
        </div>
    </div>

    {{--Modal explanation--}}
    <div class="modal animated bounceInRight" tabindex="-1" role="dialog" aria-labelledby="exampleModalLabel" aria-hidden="true" id="explanation">
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
                    <blockquote><span class="font-weight-bold">Giải trình: </span><span id="content"></span></blockquote>
                    <blockquote><span class="font-weight-bold">Người phê duyệt: </span><span id="approver" class="text-info"></span></blockquote>
                </div>
                <div class="modal-footer">
                    <div id="action-explanation">
                        <button type="button" class="btn btn-warning" id="btn-edit" aria-hidden="true" data-toggle="modal" data-target="#edit-explanation" data-id="">Sửa</button>
                        <a href="{{ base_url('explanation/delete/') }}" onclick="return confirm('Bạn thực sự muốn xóa đơn giải trình này')" class="btn btn-danger" id="btn-delete">Xoá</a>
                    </div>
                    <button type="button" class="btn btn-secondary" data-dismiss="modal">Đóng</button>
                </div>
            </div>
        </div>
    </div>

    {{--Modal form add explanation--}}
    <div class="modal animated bounceInDown" tabindex="-1" role="dialog" aria-labelledby="exampleModalLabel" aria-hidden="true" id="add-explanation">
        <div class="modal-dialog modal-lg" role="document">
            <div class="modal-content">
                <form action="{{ base_url('explanation/add') }}" method="POST">
                    <div class="modal-header">
                        <h5 class="modal-title text-info">Thêm giải trình ngày <span id="add-date-submit"></span></h5>
                        <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                            <span aria-hidden="true">&times;</span>
                        </button>
                    </div>
                    <div class="modal-body">
                        <input type="hidden" name="date_explanation" value="" id="date_explanation">
                        @if((isLeader() || get_instance()->session->id_team == NULL || !haveLeader()) && !isAdmin())
                            <div class="form-group">
                                <label for="">Gửi đơn này cho</label>
                                <select class="js-example-basic-single form-control input-default" name="approver" required>
                                    <option value="" disabled selected>Chọn người phê duyệt</option>
                                    @foreach($approvers as $approver)
                                        <option value="{{ $approver['id'] }}">{{ $approver['fullname'] }}</option>
                                    @endforeach
                                </select>
                            </div>
                        @endif
                        <div class="form-group">
                            <label for="">Giải trình:</label>
                            <textarea name="content" id="" class="form-control input-default" rows="3" required></textarea>
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button type="submit" class="btn btn-primary">Thêm giải trình</button>
                        <button type="button" class="btn btn-secondary" data-dismiss="modal">Đóng</button>
                    </div>
                </form>
            </div>
        </div>
    </div>
    {{--Modal form edit explanation--}}
    <div class="modal animated bounceInDown" tabindex="-1" role="dialog" aria-labelledby="exampleModalLabel" aria-hidden="true" id="edit-explanation">
        <div class="modal-dialog modal-lg" role="document">
            <div class="modal-content">
                <form action="{{ base_url('explanation/update') }}" method="POST">
                    <div class="modal-header">
                        <h5 class="modal-title text-info">Sửa giải trình ngày <span id="edit-date-submit"></span></h5>
                        <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                            <span aria-hidden="true">&times;</span>
                        </button>
                    </div>
                    <div class="modal-body">
                        <input type="hidden" name="id" value="" id="edit-id">
                        @if((isLeader() || get_instance()->session->id_team == NULL || !haveLeader()) && !isAdmin())
                            <div class="form-group">
                                <label for="">Gửi đơn này cho:</label>
                                <select class="js-example-basic-single form-control input-default" name="approver" id="edit-approver" required>
                                    <option value="" disabled selected>Chọn người phê duyệt</option>
                                    @foreach($approvers as $approver)
                                        <option value="{{ $approver['id'] }}">{{ $approver['fullname'] }}</option>
                                    @endforeach
                                </select>
                            </div>
                        @endif
                        <div class="form-group">
                            <label for="">Giải trình:</label>
                            <textarea name="content" id="edit-content" class="form-control input-default" rows="3" required></textarea>
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button type="submit" class="btn btn-primary">Cập nhật</button>
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
            //config month filter
            let month = $("#month-filter").val();
            let date =  $("#month-filter").datepicker().data('datepicker');
            if (month != ""){
                let param = month.split("-");
                date.selectDate(new Date(param[1],param[0]-1));
            }
            //
            $('.js-example-basic-single').select2();
            $(".btn-add-explanation").on('click', function () {
                $("#id_user").val($(this).data('id'));
                let date_explanation = $(this).data('date');
                $("#date_explanation").val(date_explanation);
                $("#add-date-submit").text(moment(date_explanation).format("DD-MM-YYYY"));
            });
            $(".btn-info-explanation").on('click', function () {
                let id = $(this).data('id');
                $.get( "{{ base_url('explanation/getById/') }}"+id, function( data ) {
                    data = JSON.parse(data);
                    if(data.status == 0){
                        $("#status").html('<h4 class="text-primary"><i class="fa fa-hand-paper-o" aria-hidden="true"></i></i> Chờ phê duyệt</h4>');
                        $("#action-explanation").show();
                    }else if(data.status ==1 ){
                        $("#status").html('<h4 class="text-success"><i class="fa fa-check" aria-hidden="true"></i> Đã xác nhận</h4>');
                        $("#action-explanation").hide();
                    }else{
                        $("#status").html('<h4 class="text-danger"><i class="fa fa-times" aria-hidden="true"></i> Bị từ chối</h4>');
                        $("#action-explanation").hide();
                    }
                    $("#date-submit").text(moment(data.date_submit).format("DD-MM-YYYY"));
                    $("#date-explanation").text(moment(data.date_explanation).format("DD-MM-YYYY"));
                    $("#content").text(data.content);
                    $("#approver").text(data.approver_fullname);
                    $("#btn-edit").data('id',data.id);
                    let hrefDelete = $("#btn-delete").attr('href') + data.id;
                    $("#btn-delete").attr('href',hrefDelete);
                });
            });
            $("#btn-edit").on('click', function () {
                $("#explanation").modal('hide');
                let id = $(this).data('id');
                $.get( "{{ base_url('explanation/getById/') }}"+id, function( data ) {
                    data = JSON.parse(data);
                    $("#edit-id").val(id);
                    $("#edit-content").text(data.content);
                    $("#select2-edit-approver-container").text(data.approver_fullname);
                    $("#edit-approver").val(data.approver);
                });
            })
        })
    </script>
@endsection