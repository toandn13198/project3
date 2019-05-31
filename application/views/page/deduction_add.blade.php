@extends('layout.template')

@section('title')
    Quản lý mức khấu trừ
@endsection

@section('content')
    <div class="row no-gutters justify-content-center">
        <div class="col-xs-12 col-sm-7">
            <div class="card">
                <div class="card-title">
                    <h3 class="text-center">Thêm mức khấu trừ</h3>
                </div>
                <div class="card-body">
                    <form action="{{ base_url('deduction/insert') }}" method="POST">
                        <div class="form-group">
                            <label for="">Tên</label>
                            <input type="text" name="name" class="form-control input-default" placeholder="Tên" required>
                        </div>
                        <div class="form-group">
                            <label for="">Từ</label>
                            <input type="number" name="start" id="start" class="form-control input-default" placeholder="5" required>
                        </div>
                        <div class="form-group">
                            <label for="">Đến</label>
                            <input type="number" name="end" id="end" class="form-control input-default" placeholder="10">
                        </div>
                        <div class="form-group">
                            <label for="">Đơn vị</label>
                            <select name="unit" id="" class="form-control input-default">
                                <option value="0">VND</option>
                                <option value="1">Ngày công</option>
                            </select>
                        </div>
                        <div class="form-group">
                            <label for="">Khấu trừ</label>
                            <input type="text" name="minus_amount" id="minus_amount" class="form-control input-default" placeholder="10,000" required>
                        </div>
                        <div class="form-group text-center">
                            <button class="btn btn-primary">Thêm mức khấu trừ</button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
@endsection

@section('script')
@endsection