@extends('layout.template')

@section('title')
    Quản lý nhóm
@endsection

@section('content')
    <div class="row no-gutters justify-content-center">
        <div class="col-xs-12 col-sm-7">
            <div class="card">
                <div class="card-title">
                    <h3 class="text-center text-info">Cập nhật nhóm</h3>
                </div>
                <div class="card-body">
                    <form action="{{ base_url('team/update') }}" method="post" id="form-edit">
                        <input type="hidden" name="id" value="{{ $team['id'] }}" id="id-team">
                        <div class="form-group">
                            <label for="">Tên nhóm</label>
                            <input type="text" name="name" value="{{ $team['name'] }}" class="form-control input-default" placeholder="Tên nhóm">
                        </div>
                        <div class="form-group">
                            <label for="">Phòng ban</label>
                            <select name="department" id="" class="form-control input-default">
                                @foreach($departments as $department)
                                    <option value="{{ $department['id'] }}" @if($department['id'] == $team['id_department']) selected @endif>{{ $department['name'] }}</option>
                                @endforeach
                            </select>
                        </div>
                        <div class="form-group">
                            <label for="">Thành viên</label>
                            <select id="member" class="js-example-basic-multiple form-control" name="member[]" multiple="multiple">
                                @foreach($usersOnTeam as $user)
                                    <option value="{{ $user['id'] }}" >{{ $user['fullname'] }}</option>
                                @endforeach
                            </select>
                        </div>
                        <input type="hidden" value="{{$team['leader']}}" name="old-leader">
                        <div class="form-group">
                            <label for="">Trưởng nhóm</label>
                            <select class="js-example-basic-single form-control" name="leader">
                                <option disabled selected>Chọn trưởng nhóm</option>
                                @foreach($usersOnTeam as $user)
                                    <option value="{{ $user['id'] }}" @if($user['id'] == $team['leader']) selected @endif>{{ $user['fullname'] }}</option>
                                @endforeach
                            </select>
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
<?php
$arr = convertToArrayId($usersOfTeam);
$str = implode(',',$arr);
?>
@section('script')
    <script>
        $(document).ready(function() {
            $('.js-example-basic-multiple,.js-example-basic-single').select2();
            let str = "{{ $str }}";
            let arr = str.split(',');
            $('.js-example-basic-multiple').val(arr).change();

            $("#form-edit").validate({
                rules: {
                    name: {
                        required: true,
                        remote: {
                            url: "{{ base_url('team/checkName/') }}",
                            data: {id: $("#id-team").val()},
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