@extends('layout.template')

@section('title')
    Quản lý mức khấu trừ
@endsection

@section('content')
    <div class="row no-gutters justify-content-center">
        <div class="col-xs-12 col-sm-7">
            <div class="card">
                <div class="card-title">
                    <h3 class="text-center">Sửa mức khấu trừ</h3>
                </div>
                <div class="card-body">
                    <form action="{{ base_url('deduction/update') }}" method="POST">
                        <input type="hidden" name="id" value="{{ $id }}">
                        <div class="form-group">
                            <label for="">Tên</label>
                            <input type="text" name="name" value="{{ $name }}" class="form-control input-default" placeholder="Tên" required>
                        </div>
                        <div class="form-group">
                            <label for="">Từ</label>
                            <input type="number" name="start" id="start" value="{{ $start }}" class="form-control input-default" placeholder="" required>
                        </div>
                        <div class="form-group">
                            <label for="">Đến</label>
                            <input type="number" name="end" id="end" value="{{ $end }}" class="form-control input-default" placeholder="">
                        </div>
                        <div class="form-group">
                            <label for="">Đơn vị</label>
                            <select name="unit" id="" class="form-control input-default">
                                <option value="0"  @if($unit == 0) selected @endif >VND</option>
                                <option value="1" @if($unit == 1) selected @endif>Ngày công</option>
                            </select>
                        </div>
                        <div class="form-group">
                            <label for="">Khấu trừ</label>
                            <input type="text" name="minus_amount" id="minus_amount" value="{{ $minus_amount }}" class="form-control input-default" placeholder="Trừ tiền" required>
                        </div>
                        <div class="form-group text-center">
                            <button class="btn btn-primary">Cập nhật</button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
@endsection

@section('script')
@endsection