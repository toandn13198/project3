@extends('layout.template')

@section('title')
    Quản lý đơn xin nghỉ
@endsection

@section('content')
    <div class="row no-gutters justify-content-center">
        <div class="col-xs-12 col-sm-7">
            <div class="card">
                <div class="card-title">
                    <h3 class="text-center text-info">Thêm đơn nghỉ phép</h3>
                </div>
                <div class="card-body">
                    <form action="{{ base_url('vacation/insert') }}" method="POST" id="form-vacation">
                        <div class="form-group">
                            <label for="">Từ ngày</label>
                            <input type="text" name="date_from" id="date_from" class="form-control input-default datetimepicker" placeholder="" autocomplete="off" required>
                            <div class="error" id="error-from"></div>
                        </div>
                        <div class="form-group">
                            <label for="">Đến ngày</label>
                            <input type="text" name="date_to" id="date_to" class="form-control input-default datetimepicker2" placeholder="" autocomplete="off" required>
                            <div class="error" id="error-to"></div>
                        </div>
                        <div class="form-group">
                            <label for="">Lý do</label>
                            <textarea name="reason" id="" rows="3" class="form-control input-default" required></textarea>
                        </div>
                        <div class="form-group">
                            <label for="">Bàn giao công việc cho</label>
                            <select class="js-example-basic-single form-control input-default" name="handover_id">
                                <option value="" disabled selected>Chọn người bàn giao</option>
                                @foreach($members as $member)
                                <option value="{{ $member['id'] }}">{{ $member['fullname'] }}</option>
                                @endforeach
                            </select>
                        </div>
                        <div class="form-group">
                            <label for="">Nội dung bàn giao</label>
                            <textarea name="handover_work" id="" rows="3" class="form-control input-default"></textarea>
                        </div>
                        @if((isLeader() || get_instance()->session->id_team == NULL || !haveLeader()) && !isAdmin())
                        <div class="form-group">
                            <label for="">Gửi đơn này cho</label>
                            <select class="js-example-basic-single form-control input-default" name="approver" required>
                                <option value="" disabled selected>Chọn người gửi</option>
                                @foreach($approvers as $approver)
                                    <option value="{{ $approver['id'] }}">{{ $approver['fullname'] }}</option>
                                @endforeach
                            </select>
                        </div>
                        @endif
                        <div class="form-group text-center">
                            <button type="submit" class="btn btn-primary" id="btn-submit">Thêm đơn nghỉ phép</button>
                        </div>
                    </form>
                </div>
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

            $("#form-vacation").on("submit", function (e) {
                let date_from = $("#date_from").val();
                date_from = date_from.split(' ');
                if(date_from[0] !== "08:30" && date_from[0] !== "13:30"){
                    $("#date_from").focus();
                    $("#error-from").text('Thời gian bắt đầu nghỉ không đúng định dạng.');
                    return false;
                }else {
                    $("#error-from").text('');
                }
                let date_to = $("#date_to").val();
                date_to = date_to.split(' ');
                if(date_to[0] !== "12:00" && date_to[0] !== "18:00"){
                    $("#date_to").focus();
                    $("#error-to").text('Thời gian bắt đầu nghỉ không đúng định dạng.');
                    return false;
                }else {
                    $("#error-to").text('');
                }
                let a = moment($("#date_from").val(),'HH:mm DD/MM/YYYY');
                if (a.diff(moment()) <0){
                    $("#date_from").focus();
                    $("#error-from").text('Thời gian bắt đầu phải lớn hơn hiện tại.');
                    return false;
                } else {
                    $("#error-to").text('');
                }
                let b = moment($("#date_to").val(),'HH:mm DD/MM/YYYY');
                if(b.diff(a) < 0){
                    $("#date_to").focus();
                    $("#error-to").text('Thời gian kết thúc phải lớn hơn bắt đầu.');
                    return false;
                }else {
                    $("#error-to").text('');
                }
            });
        })
    </script>
@endsection