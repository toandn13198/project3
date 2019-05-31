@extends('layout.template')

@section('title')
    Lịch sử nghỉ
@endsection

@section('content')
    <div class="row">
        <div class="col-12">
            <div class="card">
                <div class="card-body">
                    <div class="row no-gutters justify-content-between mb-1">
                        <form action="" class="col-12" id="form-filter">
                            <div class="form-row">
                                <div class="form-group col-sm-3 mr-2">
                                    <input type="text" id="month-filter" name="month" class="form-control input-default datepicker-here" data-min-view="months" data-view="months" data-date-format="mm-yyyy" value="@isset($_GET['month']){{ $_GET['month'] }} @endisset" autocomplete="off"/>
                                </div>
                                <div class="form-check col-sm-3 mr-2">
                                    <button class="btn btn-primary">Xem</button>
                                </div>
                            </div>
                        </form>
                    </div>
                    @if(count($data) >0)
                    <div class="mb-4" id="info-vacation">
                        <span class="font-weight-bold">Nghỉ có phép: </span><span class="text-info">{{ $allowed }} ngày</span><br>
                        <span class="font-weight-bold">Nghỉ không phép: </span><span class="text-info">{{ $unauthorized }} ngày</span><br>
                        <span class="font-weight-bold">Tổng số ngày nghỉ: </span><span class="text-info">{{ $allowed + $unauthorized }} ngày</span><br>
                    </div>
                    <div class="table-responsive">
                        <h3 class="text-info text-center">Chi tiết ngày nghỉ</h3>
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
                            @foreach($data as $key =>   $value)
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
                    @endif
                </div>

            </div>
        </div>
    </div>
@endsection

@section('script')
    <script>
        $(document).ready(function () {
            let month = $("#month-filter").val();
            let date =  $("#month-filter").datepicker().data('datepicker');
            if (month != ""){
                let param = month.split("-");
                date.selectDate(new Date(param[1],param[0]-1));
            }
        })
    </script>
@endsection