@extends('layout.template')

@section('title')
    Quản lý nhân viên
@endsection

@section('content')
    <div class="row no-gutters justify-content-center">
        <div class="col-xs-12 col-sm-7">
            <div class="card">
                <div class="card-title">
                    <h3 class="text-center text-info">Thêm nhân viên</h3>
                </div>
                <div class="card-body">
                    <form action="{{ base_url('user/insert') }}" method="post" id="form-insert">
                        <div class="form-group">
                            <label for="">Email</label>
                            <input type="email" name="email" class="form-control input-default" placeholder="Email">
                        </div>
                        <div class="form-group">
                            <label for="">Số điện thoại</label>
                            <input type="text" name="phone" class="form-control input-default" placeholder="Số điện thoại">
                        </div>
                        <div class="form-group">
                            <label for="">Họ tên</label>
                            <input type="text" name="fullname" class="form-control input-default" placeholder="Họ tên">
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
                            <input type="text" name="birthday" class="form-control input-default datepicker-here" data-date-format="dd-mm-yyyy" autocomplete="off">
                        </div>
                        <div class="form-group">
                            <label for="">Địa chỉ</label>
                            <textarea name="address" class="form-control input-default" id="" rows="3"></textarea>
                        </div>
                        <div class="form-group">
                            <label for="">Phân quyền</label>
                            <select name="role" id="" class="form-control input-default">
                                <option value="1" selected>Nhân viên</option>
                                @if(isAdmin())
                                <option value="2">HR</option>
                                @endif
                            </select>
                        </div>
                        <div class="form-group">
                            <label for="">Nhóm</label>
                            <select name="team" id="" class="js-example-basic-single form-control input-default">
                                <option disabled selected>Chọn nhóm</option>
                                @foreach($teams as $team)
                                    <option value="{{ $team['id'] }}">{{ $team['name'] }}</option>
                                @endforeach
                            </select>
                        </div>
                        <div class="form-group text-center">
                            <button class="btn btn-primary">Thêm nhân viên</button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
@endsection

@section('script')
    <script>
        $(document).ready(function () {
            $('.js-example-basic-single').select2();
            $("#form-insert").validate({
                rules: {
                    email: {
                        required: true,
                        email: true,
                        remote: {
                            url: "{{ base_url('user/checkEmail/') }}",
                            async:false
                        }
                    },
                    fullname: {
                        required: true,
                    }
                },
                messages: {
                    email:{
                        required: "Vui lòng nhập email.",
                        email: "Email không đúng định dạng.",
                        remote: "Email đã được sử dụng bởi tài khoản khác."
                    },
                    fullname:{
                        required: "Vui lòng nhập họ tên",
                    }
                }
            });
        })
    </script>
@endsection