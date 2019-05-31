@extends('layout.template')

@section('title')
    Danh sách đơn giải trình đã duyệt
@endsection

@section('content')
    <div class="row no-gutters justify-content-start mt-3">
        <div class="col-12">
            <form action="" id="form-filter">
                <div class="row no-gutters">
                    <div class="col-3">
                        <div class="form-group row no-gutters align-items-center">
                            <label for="" class="mr-1">Trạng thái</label>
                            <div class="col-8">
                                <select name="status" id="status" class="form-control input-default">
                                    <option value="" selected>Chọn trạng thái</option>
                                    <option value="1" @if(isset($_GET['status']) && $_GET['status'] == 1) selected @endif>Đã xác nhận</option>
                                    <option value="2" @if(isset($_GET['status']) && $_GET['status'] == 2) selected @endif>Bị từ chối</option>
                                </select>
                            </div>
                        </div>
                    </div>
                    <div class="col-3">
                        <input class="form-control" name="datefilter" type="text" value="@isset($_GET['datefilter']){{ $_GET['datefilter'] }}@endisset" placeholder="Chọn thời gian gửi đơn" id="date-filter" autocomplete="off">
                    </div>
                    <div class="ml-2 input-group col-3 max-height-search">
                        <input class="form-control py-2 border-right-0 border max-height-search" name="keyword" type="search" value="@isset($_GET['keyword']){{ $_GET['keyword'] }}@endisset" id="" placeholder="Họ tên">
                        <span class="input-group-append">
                            <button class="btn btn-outline-secondary border-left-0 border" type="submit">
                                <i class="fa fa-search"></i>
                            </button>
                        </span>
                    </div>
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
                        <div class="row no-gutters justify-content-between align-items-center p-r-15">
                            <div>
                                <span class="font-weight-bold">Ngày gửi: </span><span class="text-info"> {{  date('d-m-Y',strtotime($explanation['date_submit'])) }}</span><br>
                                <span class="font-weight-bold">Ngày giải trình: </span> <span class="text-primary"> {{ date('d-m-Y', strtotime($explanation['date_explanation'])) }}</span><br>
                            </div>
                            @if($explanation['status']==0)
                                <div class="text-primary"><i class="fa fa-hand-paper-o" aria-hidden="true"></i> Chờ phê duyệt</div>
                            @elseif($explanation['status'] == 1)
                                <div class="text-success"><i class="fa fa-check" aria-hidden="true"></i> Đã xác nhận</div>
                            @else
                                <div class="text-danger"><i class="fa fa-times" aria-hidden="true"></i> Bị từ chối</div>
                            @endif

                        </div>
                        <span class="font-weight-bold">Giải trình: </span>{{ $explanation['content'] }}
                        <div class="text-right">
                            <button class="btn btn-info mr-2 explanation-info"  data-toggle="modal" data-target="#myModal" data-id="{{ $explanation['id'] }}" data-datesubmit="{{ date('d-m-Y', strtotime($explanation['date_submit'])) }}" data-dateexplanation="{{ date('d-m-Y', strtotime($explanation['date_explanation'])) }}" data-content="{{ $explanation['content'] }}" data-user="{{ $explanation['fullname'] }}">Chi tiết</button>
                        </div>
                    </div>
                </div>
            </div>
        @endforeach
        {{--pagination--}}
        {!! $pagi->getPagination() !!}
    @else
        <h3 class="text-warning mt-3">Không có đơn nào đã duyệt.</h3>
    @endif
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

            $(".explanation-info").on("click", function () {
                $("#user").text($(this).data('user'));
                $("#date-submit").text($(this).data('datesubmit'));
                $("#date-explanation").text($(this).data('dateexplanation'));
                $("#content").text($(this).data('content'));
            })
        })
    </script>
@endsection