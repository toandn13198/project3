@extends('layout.template')

@section('title')
@endsection

@section('content')
    <div class="row no-gutters justify-content-center">
        <div class="col-xs-12 col-sm-7">
            <div class="card">
                <div class="card-title">
                    <h3 class="text-center text-primary">Cập nhật thông tin cá nhân</h3>
                </div>
                <div class="card-body">
                    <form action="">
                        <div class="form-group">
                            <label for="">Họ tên</label>
                            <input type="text" name="" value="" class="form-control input-default">
                        </div>
                        <div class="form-group">
                            <label for="">Email</label>
                            <input type="email" name="" value="" class="form-control input-default">
                        </div>
                        <div class="form-group">
                            <label for="">Số điện thoại</label>
                            <input type="text" name="" value="" class="form-control input-default">
                        </div>
                        <div class="form-group">
                            <label for="">Giới tính</label>
                            <div class="form-check form-check-inline">
                                <input class="form-check-input" type="radio" name="gender" id="nam" value="1" checked>
                                <label class="form-check-label" for="nam">Nam</label>
                            </div>
                            <div class="form-check form-check-inline">
                                <input class="form-check-input" type="radio" name="gender" id="nu" value="0">
                                <label class="form-check-label" for="nu">Nữ</label>
                            </div>
                        </div>
                        <div class="form-group">
                            <label for="">Ngày sinh</label>
                            <input type="text" name="birthday" class="form-control input-default datepicker">
                        </div>
                        <div class="form-group">
                            <label for="">Địa chỉ</label>
                            <textarea name="address" class="form-control input-default" id="" rows="3"></textarea>
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