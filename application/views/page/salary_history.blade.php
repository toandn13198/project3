@extends('layout.template')

@section('title')
    Xem lương
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
                                <div class="form-group col-sm-3 mr-2">
                                    <input type="text" id="month-filter" name="month" class="form-control input-default datepicker-here" data-min-view="months" data-view="months" data-date-format="mm-yyyy" value="@isset($_GET['month']){{ $_GET['month'] }} @endisset" autocomplete="off"/>
                                </div>
                                <div class="form-check col-sm-3 mr-2">
                                    <button class="btn btn-primary">Xem</button>
                                </div>
                            </div>
                        </form>
                    </div>
                    @if(isset($hard_salary))
                    <div class="table-responsive">
                        <h3 class="text-center text-info">Bảng lương tháng của bạn</h3>
                        <table class="table">
                            <tbody>
                                <tr>
                                    <td class="text-dark">Tổng số ngày công của tháng</td>
                                    <td class="text-info">{{ $workingDay }}</td>
                                </tr>
                                <tr>
                                    <td class="text-dark">Tổng số ngày công đi làm</td>
                                    <td class="text-info">{{ $realday }}</td>
                                </tr>
                                <tr>
                                    <td class="text-dark">Số ngày nghỉ có phép</td>
                                    <td class="text-info">{{ $dayVacation }}</td>
                                </tr>
                                <tr>
                                    <td class="text-dark">Lương cứng</td>
                                    <td class="text-info">{{ number_format($hard_salary,0) }}</td>
                                </tr>
                                <tr>
                                    <td class="text-dark">Trợ cấp</td>
                                    <td class="text-info">{{ number_format($subsidize,0) }}</td>
                                </tr>
                                <tr>
                                    <td class="text-dark">Số tiền bị trừ</td>
                                    <td class="text-info">{{ number_format($deduction,0) }}</td>
                                </tr>
                                <tr style="background: #F2F2FB">
                                    <td class="text-warning">Lương thực nhận</td>
                                    <td class="text-warning">{{ number_format($realSalary,0) }}</td>
                                </tr>

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