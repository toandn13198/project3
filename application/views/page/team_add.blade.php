@extends('layout.template')

@section('title')
    Quản lý nhóm
@endsection

@section('content')
    <div class="row no-gutters justify-content-center">
        <div class="col-xs-12 col-sm-7">
            <div class="card">
                <div class="card-title">
                    <h3 class="text-center text-info">Thêm nhóm</h3>
                </div>
                <div class="card-body">
                    <form action="{{ base_url('team/insert') }}" method="post" id="form-insert">
                        <div class="form-group">
                            <label for="">Tên nhóm</label>
                            <input type="text" name="name" class="form-control input-default" placeholder="Tên nhóm">
                        </div>
                        <div class="form-group">
                            <label for="">Phòng ban</label>
                            <select name="department" id="" class="form-control input-default">
                                <option disabled selected>Chọn phòng ban</option>
                                @foreach($departments as $department)
                                <option value="{{ $department['id'] }}">{{ $department['name'] }}</option>
                                @endforeach
                            </select>
                        </div>
                        <div class="form-group">
                            <label for="">Thành viên</label>
                            <select class="js-example-basic-multiple form-control" name="member[]" multiple="multiple">
                                @foreach($users as $user)
                                <option value="{{ $user['id'] }}">{{ $user['fullname'] }}</option>
                                @endforeach
                            </select>
                        </div>
                        <div class="form-group">
                            <label for="">Trưởng nhóm</label>
                            <select class="js-example-basic-single form-control" name="leader">
                                <option disabled selected>Chọn trưởng nhóm</option>
                                @foreach($users as $user)
                                    <option value="{{ $user['id'] }}">{{ $user['fullname'] }}</option>
                                @endforeach
                            </select>
                        </div>
                        <div class="form-group text-center">
                            <button class="btn btn-primary">Thêm nhóm</button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
@endsection

@section('script')
    <script>
        $(document).ready(function() {
            $('.js-example-basic-multiple,.js-example-basic-single').select2();
            $("#form-insert").validate({
                rules: {
                    name: {
                        required: true,
                        remote: {
                            url: "{{ base_url('team/checkName/') }}",
                            async:false
                        }
                    },
                    department: {
                        required: true,
                    }
                },
                messages: {
                    name:{
                        required: "Vui lòng nhập tên nhóm.",
                        remote: "Tên nhóm đã tồn tại."
                    },
                    department:{
                        required: "Vui lòng chọn phòng ban",
                    }
                }
            });
        });
    </script>
@endsection