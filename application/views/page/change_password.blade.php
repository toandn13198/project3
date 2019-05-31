@extends('layout.template')

@section('title')
    Tài khoản cá nhân
@endsection

@section('content')
    <div class="card">
        <div class="row no-gutters justify-content-center">
            <div class="col-xs-12 col-sm-6">
                <div class="card-title">
                    <h3 class="text-center text-primary">Đổi mật khẩu</h3>
                </div>
                <div class="card-body">
                    <form action="{{ base_url('user/postChangePass') }}" method="POST" id="change-pass">
                        <div class="form-group">
                            <label for="">Mật khẩu hiện tại</label>
                            <input type="password" name="currentPassword" value="" class="form-control input-default">
                        </div>
                        <div class="form-group">
                            <label for="">Mật khẩu mới</label>
                            <input type="password" name="newPassword" id="new-password" value="" class="form-control input-default">
                        </div>
                        <div class="form-group mb-2">
                            <label for="">Nhập lại mật khẩu mới</label>
                            <input type="password" name="reNewPassword" value="" class="form-control input-default">
                        </div>
                        <div class="text-danger mb-3">
                            {{ $error_changepass }}
                        </div>
                        <div class="form-group text-center">
                            <button class="btn btn-primary">Đổi mật khẩu</button>
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
            $("#change-pass").validate({
                rules: {
                    currentPassword: {
                        required: true,
                        remote: {
                            url: "{{ base_url('user/checkPassword') }}",
                            async:false
                        }
                    },
                    newPassword:{
                        required: true
                    },
                    reNewPassword: {
                        required: true,
                        equalTo: "#new-password"
                    }
                },
                messages: {
                    currentPassword:{
                        required: "Vui lòng nhập mật khẩu hiện tại.",
                        remote: "Mật khẩu hiện tại không chính xác."
                    },
                    newPassword:{
                        required: "Vui lòng nhập mật khẩu mới."
                    },
                    reNewPassword: {
                        required: 'Vui lòng nhập lại mật khẩu mới.',
                        equalTo: 'Mật khẩu nhập lại nhập không chính xác.'
                    }
                }
            });
        })
    </script>
@endsection