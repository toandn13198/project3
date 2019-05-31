@extends('layout.template')

@section('title')
    Danh sách đơn xin nghỉ đã gửi
@endsection

@section('content')
    <div class="row no-gutters justify-content-start mt-3">
        <div class="col-xs-12 col-sm-8">
            <form action="" id="form-filter">
                <div class="row no-gutters">
                    <div class="col-6">
                        <div class="form-group row no-gutters align-items-center">
                            <label for="" class="mr-1">Trạng thái</label>
                            <div class="col-8">
                                <select name="status" id="status" class="form-control input-default">
                                    <option value="" selected>Chọn trạng thái</option>
                                    <option value="0" @if(isset($_GET['status']) && $_GET['status'] === '0') selected @endif>Chờ phê duyệt</option>
                                    <option value="1" @if(isset($_GET['status']) && $_GET['status'] == 1) selected @endif>Đã xác nhận</option>
                                    <option value="2" @if(isset($_GET['status']) && $_GET['status'] == 2) selected @endif>Bị từ chối</option>
                                </select>
                            </div>
                        </div>
                    </div>
                    <div class="col-6">
                        <input class="form-control" name="datefilter" type="text" value="@isset($_GET['datefilter']){{ $_GET['datefilter'] }}@endisset" placeholder="Chọn thời gian gửi đơn" id="date-filter" autocomplete="off">
                    </div>
                </div>
            </form>
        </div>
    </div>
    @if(count($listVacation) > 0)
    @foreach($listVacation as $vacation)
    <div class="card">
        <div class="row no-gutters align-items-center">
            <div class="col-12">
                <div class="row no-gutters justify-content-between align-items-center p-r-15">
                    <div><span class="font-weight-bold">Ngày gửi: </span><span class="text-info">{{  date('d-m-Y',strtotime($vacation['date_submit'])) }}</span></div>
                    @if($vacation['status']==0)
                        <div class="text-primary"><i class="fa fa-hand-paper-o" aria-hidden="true"></i> Chờ phê duyệt</div>
                    @elseif($vacation['status'] == 1)
                        <div class="text-success"><i class="fa fa-check" aria-hidden="true"></i> Đã xác nhận</div>
                    @else
                        <div class="text-danger"><i class="fa fa-times" aria-hidden="true"></i> Bị từ chối</div>
                    @endif

                </div>
                <span class="font-weight-bold">Thời gian nghỉ: </span><span class="text-info">{{ date('H:i d-m-Y',strtotime($vacation['date_from']))}} - {{ date('H:i d-m-Y',strtotime($vacation['date_to']))}}</span><br>
                <span class="font-weight-bold">Lý do: </span>{{ $vacation['reason'] }}<br>
                <div class="text-right">
                    <button class="btn btn-info mr-2 view-info"  data-toggle="modal" data-target="#myModal" data-id="{{ $vacation['id'] }}">Chi tiết</button>
                    @if($vacation['status'] == 0 )
                    <button class="btn btn-warning mr-2 edit" data-toggle="modal" data-target="#editModal" data-id="{{ $vacation['id'] }}">Chỉnh sửa</button>
                    <a href="{{ base_url('vacation/delete/' . $vacation['id']) }}" class="btn btn-danger mr-2" onclick="return confirm('Bạn thực sự muốn xóa đơn này?')">Xóa</a>
                    @endif
                </div>
            </div>
        </div>
    </div>
    @endforeach
    {{--pagination--}}
   {!! $pagi->getPagination() !!}
    @else
        <h3 class="text-warning mt-3">Không có đơn nào đã gửi.</h3>
    @endif
    <!-- Modal detail -->
    <div class="modal animated bounceInDown" tabindex="-1" role="dialog" aria-labelledby="exampleModalLabel" aria-hidden="true" id="myModal">
        <div class="modal-dialog modal-lg" role="document">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title">Chi tiết đơn xin nghỉ</h5>
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true">&times;</span>
                    </button>
                </div>
                <div class="modal-body">
                    <blockquote><span class="font-weight-bold">Thời gian gửi: </span><span class="text-info" id="date-submit"></span> </blockquote>
                    <blockquote><span class="font-weight-bold">Lý do: </span><span id="reason"></span></blockquote>
                    <blockquote><span class="font-weight-bold">Thời gian nghỉ: </span> <span class="text-info" id="date-from"></span> đến <span class="text-info" id="date-to"></span></blockquote>
                    <blockquote><span class="font-weight-bold">Bàn giao công việc cho: </span><span class="text-info" id="handover-user"></span></blockquote>
                    <blockquote><span class="font-weight-bold">Nội dung bàn giao công việc: </span><span id="handover-work"></span></blockquote>
                    <blockquote><span class="font-weight-bold">Gửi đơn cho: </span><span id="approver" class="text-info"></span></blockquote>

                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-dismiss="modal">Đóng</button>
                </div>
            </div>
        </div>
    </div>
    <!-- Modal edit -->
    <div class="modal animated bounceInDown" tabindex="-1" role="dialog" aria-labelledby="exampleModalLabel" aria-hidden="true" id="editModal">
        <div class="modal-dialog modal-lg" role="document">
            <div class="modal-content">
                <form action="{{ base_url('vacation/update') }}" id="form-edit" method="post">
                    <div class="modal-header">
                        <h5 class="modal-title text-info">Sửa đơn xin nghỉ</h5>
                        <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                            <span aria-hidden="true">&times;</span>
                        </button>
                    </div>
                    <div class="modal-body">
                        <input type="hidden" name="id" id="edit-id">
                        <div class="row">
                            <div class="col-6">
                                <div class="form-group">
                                    <label for="">Từ ngày</label>
                                    <input type="text" name="date_from" id="edit-date-from" class="form-control input-default datetimepicker" placeholder="" autocomplete="off" required>
                                    <div class="error" id="error-from"></div>
                                </div>
                            </div>
                            <div class="col-6">
                                <div class="form-group">
                                    <label for="">Đến ngày</label>
                                    <input type="text" name="date_to" id="edit-date-to" class="form-control input-default datetimepicker2" placeholder="" autocomplete="off" required>
                                    <div class="error" id="error-to"></div>
                                </div>
                            </div>
                        </div>
                        <div class="form-group">
                            <label for="">Lý do</label>
                            <textarea name="reason" id="edit-reason" rows="3" class="form-control input-default" required></textarea>
                        </div>
                        <div class="row">
                            <div class="col-6">
                                <div class="form-group">
                                    <label for="">Bàn giao công việc cho</label><br>
                                    <select class="js-example-basic-single form-control input-default" name="handover_id" id="edit-handover-id">
                                        @foreach($members as $member)
                                            <option value="{{ $member['id'] }}">{{ $member['fullname'] }}</option>
                                        @endforeach
                                    </select>
                                </div>
                            </div>
                        </div>
                        <div class="form-group">
                            <label for="">Nội dung bàn giao</label>
                            <textarea name="handover_work" id="edit-handover-work" rows="3" class="form-control input-default"></textarea>
                        </div>
                        @if((isLeader() || get_instance()->session->id_team == NULL || !haveLeader()) && !isAdmin())
                        <div class="form-group">
                            <label for="">Gửi đơn này cho</label>
                            <select class="js-example-basic-single form-control input-default" name="approver" id="approver-edit" required>
                                <option value="" disabled selected>Chọn người gửi</option>
                                @foreach($approvers as $approver)
                                    <option value="{{ $approver['id'] }}">{{ $approver['fullname'] }}</option>
                                @endforeach
                            </select>
                        </div>
                        @endif
                    </div>
                    <div class="modal-footer">
                        <button class="btn btn-primary">Cập Nhật</button>
                        <button type="button" class="btn btn-secondary" data-dismiss="modal">Đóng</button>
                    </div>
                </form>
            </div>
        </div>
    </div>
@endsection

@section('script')
    <script>
        $(document).ready(function (e) {
            $('.datetimepicker').datetimepicker({ footer: true, modal: true, format: "HH:MM dd-mm-yyyy" });
            $('.datetimepicker2').datetimepicker({ footer: true, modal: true, format: "HH:MM dd-mm-yyyy" });
            $('.js-example-basic-single').select2();

            //filter
            $(document).on('change', '#status', function () {
                $("#form-filter").submit();
            });
            let date = "@isset($_GET['datefilter']){{ $_GET['datefilter'] }}@endisset";
            if(date != ""){
                let arrDate = date.split('-');
                console.log(arrDate);
                $('#date-filter').data('daterangepicker').setStartDate(arrDate[0].trim());
                $('#date-filter').data('daterangepicker').setEndDate(arrDate[1].trim());
            }
            $('input[name="datefilter"]').on('apply.daterangepicker', function(ev, picker) {
                $("#form-filter").submit();
            });
            //edit
            $(document).on('click', '.edit', function (e) {
                let id = $(this).data('id');
                $.get( "{{ base_url('vacation/getById/') }}"+id, function( data ) {
                    data = JSON.parse(data);
                    $("#edit-date-from").val(data.date_from);
                    $("#edit-date-to").val(data.date_to);
                    $("#edit-reason").val(data.reason);
                    $("#select2-edit-handover-id-container").text(data.handover_fullname);
                    $("#edit-handover-id").val(data.handover_id);
                    $("#select2-approver-edit-container").text(data.approver_info.fullname);
                    $("#approver-edit").val(data.approver);
                    $("#edit-handover-work").val(data.handover_work);
                    $("#edit-id").val(id);
                });
            });
            $(document).on('click', '.view-info', function (e) {
                let id = $(this).data('id');
                $.get( "{{ base_url('vacation/getById/') }}"+id, function( data ) {
                    data = JSON.parse(data);
                    $("#date-submit").text(data.date_submit);
                    $("#reason").text(data.reason);
                    $("#date-from").text(data.date_from);
                    $("#date-to").text(data.date_to);
                    $("#handover-user").text(data.handover_fullname);
                    $("#handover-work").text(data.handover_work);
                    $("#approver").text(data.approver_info.fullname);
                });
            })
            //validate form edit
            $("#form-edit").on("submit", function (e) {
                let date_from = $("#edit-date-from").val();
                date_from = date_from.split(' ');
                if(date_from[0] !== "08:30" && date_from[0] !== "13:30"){
                    $("#edit-date-from").focus();
                    $("#error-from").text('Thời gian bắt đầu nghỉ không đúng định dạng.');
                    return false;
                }else {
                    $("#error-from").text('');
                }
                let date_to = $("#edit-date-to").val();
                date_to = date_to.split(' ');
                if(date_to[0] !== "12:00" && date_to[0] !== "18:00"){
                    $("#edit-date-to").focus();
                    $("#error-to").text('Thời gian bắt đầu nghỉ không đúng định dạng.');
                    return false;
                }else {
                    $("#error-to").text('');
                }
                let a = moment($("#edit-date-from").val(),'HH:mm DD/MM/YYYY');
                if (a.diff(moment()) <0){
                    $("#edit-date-from").focus();
                    $("#error-from").text('Thời gian bắt đầu phải lớn hơn hiện tại.');
                    return false;
                } else {
                    $("#error-to").text('');
                }
                let b = moment($("#edit-date-to").val(),'HH:mm DD/MM/YYYY');
                if(b.diff(a) < 0){
                    $("#edit-date-to").focus();
                    $("#error-to").text('Thời gian kết thúc phải lớn hơn bắt đầu.');
                    return false;
                }else {
                    $("#error-to").text('');
                }
            });
        })
    </script>
@endsection