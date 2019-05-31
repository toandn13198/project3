@extends('layout.template')

@section('title')
    Duyệt đơn xin nghỉ phép
@endsection

@section('content')
    <div class="row no-gutters justify-content-start mt-3">
        <div class="col-xs-12 col-sm-8">
            <form action="" class="row" id="form-filter">
                <div class="input-group col-6">
                    <input class="form-control py-2 border-right-0 border" name="keyword" type="search" value="@isset($_GET['keyword']){{ $_GET['keyword'] }}@endisset" id="" placeholder="">
                    <span class="input-group-append">
                        <button class="btn btn-outline-secondary border-left-0 border" type="submit">
                            <i class="fa fa-search"></i>
                        </button>
                    </span>
                </div>
                <div class="col-6">
                    <input class="form-control" name="datefilter" type="text" value="@isset($_GET['datefilter']){{ $_GET['datefilter'] }}@endisset" placeholder="Chọn thời gian gửi đơn" id="date-filter" autocomplete="off">
                </div>
            </form>
        </div>
    </div>
    @if(count($requestVacation) > 0)
        @foreach($requestVacation as $value)
        <div class="card">
            <div class="row no-gutters align-items-center">
                <div class="col-2 text-center p-5">
                    <img src="{{ asset('elaAdmin/images/users/' . $value['image']) }}" alt="" width="60%">
                    <h5 class="mt-1 nowrap text-info">{{ $value['fullname'] }}</h5>
                </div>
                <div class="col-10">
                    <span class="font-weight-bold">Ngày gửi: </span><span class="text-info">{{ date('d-m-Y',strtotime($value['date_submit']))}}</span><br>
                    <span class="font-weight-bold">Thời gian nghỉ: </span><span class="text-info">{{ date('H:i d-m-Y',strtotime($value['date_from']))}} - {{ date('H:i d-m-Y',strtotime($value['date_to']))}}</span><br>
                    <span class="font-weight-bold">Lý do: </span>{{ $value['reason'] }}<br>
                    <div class="text-right">
                        <a href="{{ base_url('vacation/confirm/' . $value['id']) }}" class="btn btn-primary mr-2" onclick="return confirm('Bạn thực sự muốn xác nhận đơn này?')">Xác nhận</a>
                        <a href="{{ base_url('vacation/reject/' . $value['id']) }}" class="btn btn-danger mr-2" onclick="return confirm('Bạn thực sự muốn từ chối đơn này?')">Từ chối</a>
                        <button class="btn btn-info mr-2 view-info"  data-toggle="modal" data-target="#myModal" data-id="{{ $value['id'] }}">Chi tiết</button>
                    </div>
                </div>
            </div>
        </div>
        @endforeach
        {{--pagination--}}
        {!! $pagi->getPagination() !!}
    @else
        <h3 class="text-warning mt-3">Không có đơn nào cần phê duyệt</h3>
    @endif
    <!-- Modal -->
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
                    <blockquote><span class="font-weight-bold">Người gửi: </span><span class="text-info" id="user-fullname">Nguyễn Linh Nhi</span></blockquote>
                    <blockquote><span class="font-weight-bold">Thời gian nghỉ: </span> <span class="text-info" id="date-from"></span> đến <span class="text-info" id="date-to"></span></blockquote>
                    <blockquote><span class="font-weight-bold">Lý do: </span><span id="reason"></span></blockquote>
                    <blockquote><span class="font-weight-bold">Bàn giao công việc cho: </span><span class="text-info" id="handover-fullname"></span></blockquote>
                    <blockquote><span class="font-weight-bold">Nội dung bàn giao công việc: </span> <span id="handover-work"></span></blockquote>
                    <blockquote><span class="font-weight-bold">Thời gian gửi: </span><span class="text-info" id="date-submit"></span></blockquote>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-dismiss="modal">Đóng</button>
                </div>
            </div>
        </div>
    </div>
@endsection

@section('script')
    <script>
        $(document).ready(function () {
            $(document).on('click', '.view-info', function (e) {
                let id = $(this).data('id');
                $.get( "{{ base_url('vacation/getById/') }}"+id, function( data ) {
                    data = JSON.parse(data);
                    $("#user-fullname").text(data.user_fullname);
                    $("#date-submit").text(data.date_submit);
                    $("#reason").text(data.reason);
                    $("#date-from").text(data.date_from);
                    $("#date-to").text(data.date_to);
                    $("#handover-fullname").text(data.handover_fullname);
                    $("#handover-work").text(data.handover_work);
                });
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
        })
    </script>
@endsection