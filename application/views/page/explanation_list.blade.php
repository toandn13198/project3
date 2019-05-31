@extends('layout.template')

@section('title')
    Duyệt đơn giải trình
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
    @if(count($requestExplanation) > 0)
    @foreach($requestExplanation as $explanation)
    <div class="card">
        <div class="row no-gutters align-items-center">
            <div class="col-2 text-center p-5">
                <img src="{{ asset('elaAdmin/images/users/' . $explanation['image']) }}" alt="" width="60%">
                <h5 class="mt-1 nowrap text-info">{{ $explanation['fullname'] }}</h5>
            </div>
            <div class="col-10">
                <blockquote><span class="font-weight-bold">Ngày gửi đơn: </span> <span class="text-primary">{{ date('d-m-Y', strtotime($explanation['date_submit'])) }}</span></blockquote>
                <blockquote><span class="font-weight-bold">Ngày giải trình: </span> <span class="text-primary"> {{ date('d-m-Y', strtotime($explanation['date_explanation'])) }}</span></blockquote>
                <blockquote style="white-space: nowrap;text-overflow: ellipsis;overflow:hidden;"><span class="font-weight-bold">Giải trình: </span>{{ $explanation['content'] }}</blockquote>
                <div class="text-right">
                    <a class="btn btn-primary mr-2" href="{{ base_url('explanation/confirm/' . $explanation['id']) }}" onclick="return confirm('Bạn thực sự muốn xác nhận đơn này?')">Xác nhận</a>
                    <a class="btn btn-danger mr-2" href="{{ base_url('explanation/reject/' . $explanation['id']) }}" onclick="return confirm('Bạn thực sự muốn từ chối đơn này?')">Từ chối</a>
                    <button class="btn btn-info mr-2 explanation-info"  data-toggle="modal" data-target="#myModal" data-id="{{ $explanation['id'] }}" data-datesubmit="{{ date('d-m-Y', strtotime($explanation['date_submit'])) }}" data-dateexplanation="{{ date('d-m-Y', strtotime($explanation['date_explanation'])) }}" data-content="{{ $explanation['content'] }}" data-user="{{ $explanation['fullname'] }}">Chi tiết</button>
                </div>
            </div>
        </div>
    </div>
    @endforeach

    {!! $pagi->getPagination() !!}
    @else
    <h3 class="text-warning mt-2">Không có đơn nào để hiển thị!</h3>
    @endif

    <!-- Modal -->
    <div class="modal animated bounceInDown" tabindex="-1" role="dialog" aria-labelledby="exampleModalLabel" aria-hidden="true" id="myModal">
        <div class="modal-dialog modal-lg" role="document">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title">Chi tiết đơn giải trình</h5>
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true">&times;</span>
                    </button>
                </div>
                <div class="modal-body">
                    <blockquote><span class="font-weight-bold">Người gửi đơn: </span> <span class="text-primary" id="user"></span></blockquote>
                    <blockquote><span class="font-weight-bold">Ngày gửi đơn: </span> <span class="text-primary" id="date-submit"></span></blockquote>
                    <blockquote><span class="font-weight-bold">Ngày cần giải trình: </span> <span class="text-primary" id="date-explanation"></span></blockquote>
                    <blockquote><span class="font-weight-bold">Giải trình: </span><span id="content"></span></blockquote>
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
            $(".explanation-info").on("click", function () {
                $("#user").text($(this).data('user'));
                $("#date-submit").text($(this).data('datesubmit'));
                $("#date-explanation").text($(this).data('dateexplanation'));
                $("#content").text($(this).data('content'));
            })
        });
    </script>
@endsection