@extends('layout.template')

@section('title')
    Quản lý mức khấu trừ
@endsection

@section('content')
    <div class="row">
        <div class="col-12">
            <div class="card">
                <div class="card-title">
                </div>
                <div class="card-body">
                    <div class="row no-gutters justify-content-between mb-1">
                        <div><a href="{{ base_url('deduction/add') }}" class="btn btn-primary">Thêm mức khấu trừ</a></div>
                        <div>
                            <form action="">
                                <div class="input-group">
                                    <input class="form-control py-2 border-right-0 border" name="keyword" type="search" value="@isset($_GET['keyword']){{ $_GET['keyword'] }}@endisset" id="" placeholder="">
                                    <span class="input-group-append">
                                      <button class="btn btn-outline-secondary border-left-0 border" type="submit">
                                            <i class="fa fa-search"></i>
                                      </button>
                                    </span>
                                </div>
                            </form>
                        </div>
                    </div>
                    @if(count($deductions) > 0)
                    <div class="table-responsive">
                        <h3 class="text-info text-center">Danh sách mức khấu trừ</h3>
                        <table class="table table-striped">
                            <thead>
                            <tr>
                                <th>#</th>
                                <th>Tên</th>
                                <th>Từ</th>
                                <th>Đến</th>
                                <th>Khoảng(phút)</th>
                                <th>Khấu trừ</th>
                                <th>Đơn vị</th>
                                <th></th>
                                <th></th>
                            </tr>
                            </thead>
                            <tbody>
                            <?php
                            $selectedTime = "08:30";
                            ?>
                            @foreach($deductions as $key => $deduction)
                            <tr>
                                <th scope="row">{{ ($pagi->getPage()-1)*$pagi->getLimit()+$key+1 }}</th>
                                <td>{{ $deduction['name'] }}</td>
                                <td>{{ date('h:i',strtotime($selectedTime . ' +' . $deduction['start'] . ' minutes')) }}</td>
                                <td>{{ ($deduction['end'] != NULL) ? date('h:i',strtotime($selectedTime . ' +' . $deduction['end'] . ' minutes')) : ''}}</td>
                                <td>{{ $deduction['start'] . (($deduction['end'] != NULL) ? '-' . $deduction['end'] . ' phút' : ' phút trở lên') }}</td>
                                <td>
                                    @if($deduction['unit'] == 0 )
                                        {{ number_format($deduction['minus_amount']) }}
                                    @else
                                        {{ $deduction['minus_amount'] }}
                                    @endif
                                </td>
                                <td>
                                    @if($deduction['unit'] == 0 )
                                        VNĐ
                                    @else
                                        Ngày công
                                    @endif
                                </td>
                                <td>
                                    <a href="{{ base_url('deduction/edit/' . $deduction['id']) }}"><i class="fa fa-pencil-square-o btn btn-warning" aria-hidden="true"></i></a>
                                    <a href="{{ base_url('deduction/delete/' . $deduction['id']) }}" onclick="return confirm('Bạn có muốn xóa mức khấu trừ này không?')"><i class="fa fa-trash btn btn-danger" aria-hidden="true"></i></a>
                                </td>
                            </tr>
                            @endforeach
                            </tbody>
                        </table>
                    </div>
                    {!! $pagi->getPagination() !!}
                    @else
                        <h3 class="text-warning">Không có dữ liệu khấu trừ để hiển thị!!!</h3>
                    @endif
                </div>

            </div>
        </div>
    </div>
@endsection

@section('script')
@endsection