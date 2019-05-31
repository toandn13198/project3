@extends('layout.template')

@section('title')
    Tài khoản cá nhân
@endsection

@section('content')
    <div class="card">
        <div class="row no-gutters justify-content-center">
            <div class="col-xs-12 col-sm-6">
                <div class="card-title">
                    <h3 class="text-center text-primary">Thông tin cá nhân</h3>
                </div>
                <div class="card-body">
                    <form action="{{ base_url('user/postChangeInfo') }}" method="POST" enctype='multipart/form-data'>
                        <div class="form-group">
                            <label for="">ID</label>
                            <input type="text" name="id" value="{{ $user['id'] }}" class="form-control input-default" readonly>
                        </div>
                        <div class="form-group">
                            <label for="">Ảnh đại diện</label>
                            <input type="hidden" name="old-image" value="{{ $user['image'] }}">
                            <input type="file" name="image" class="input-image" id="choose-image" ><br>
                            <div id="">
                                <img src="{{ asset('elaAdmin/images/users/' . $user['image']) }}" alt="" class="profile-pic" width="40%" id="image-previous">
                            </div>
                            <label for="choose-image" class="custom-file-upload mt-1">
                                <i class="fa fa-cloud-upload"></i> Chọn ảnh
                            </label>
                        </div>
                        <div class="form-group">
                            <label for="">Họ tên</label>
                            <input type="text" name="fullname" value="{{ $user['fullname'] }}" class="form-control input-default" required>
                        </div>
                        <div class="form-group">
                            <label for="">Giới tính</label>
                            <div class="form-check form-check-inline">
                                <input class="form-check-input" type="radio" name="gender" id="nam" value="1" @if($user['gender'] == 1) checked @endif>
                                <label class="form-check-label" for="nam">Nam</label>
                            </div>
                            <div class="form-check form-check-inline">
                                <input class="form-check-input" type="radio" name="gender" id="nu" value="0" @if($user['gender'] == 0) checked @endif>
                                <label class="form-check-label" for="nu">Nữ</label>
                            </div>
                        </div>
                        <div class="form-group">
                            <label for="">Ngày sinh</label>
                            <input type="text" id="birthday" name="birthday" value="{{ date('d-m-Y',strtotime($user['birthday'])) }}" class="form-control input-default datepicker-here" data-date-format="dd-mm-yyyy" autocomplete="off">
                        </div>
                        <div class="form-group mb-2">
                            <label for="">Địa chỉ</label>
                            <textarea name="address" class="form-control input-default" id="" rows="3">{{ $user['address'] }}</textarea>
                        </div>
                        <div class="form-group text-center">
                            <button class="btn btn-primary">Cập nhật thông tin</button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
@endsection

@section('script')
    <?php
        $birthday = ($user['birthday'] === NULL) ? '1970-01-01' : $user['birthday'];
        $arrBirthday = explode('-',$birthday);
    ?>
    <script>
        $(document).ready(function () {
            $("#birthday").data('datepicker').selectDate(new Date({{ $arrBirthday[0] }}, {{ $arrBirthday[1]-1 }}, {{ $arrBirthday[2] }}));
            $('#choose-image').change( function(event) {
                var tmppath = URL.createObjectURL(event.target.files[0]);
                $("#image-previous").fadeIn().attr('src', tmppath);
            });
        })
    </script>
@endsection