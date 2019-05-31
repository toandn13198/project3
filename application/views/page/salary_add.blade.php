@extends('layout.template')

@section('title')
    Quản lý mức lương
@endsection

@section('content')
    <div class="row no-gutters justify-content-center">
        <div class="col-xs-12 col-sm-7">
            <div class="card">
                <div class="card-title">
                    <h3 class="text-center text-info">Thêm mức lương</h3>
                </div>
                <div class="card-body">
                    <form action="{{ base_url('salary/insert') }}" method="POST">
                        <div class="form-group">
                            <label for="">Nhân viên</label>
                            <select class="js-example-basic-single form-control" name="user" id="user-id" required>
                                <option value="" disabled selected>Chọn nhân viên</option>
                                @foreach($users as $user)
                                    <option value="{{ $user['id'] }}">{{ $user['fullname'] }}</option>
                                @endforeach
                            </select>
                        </div>
                        <div class="form-group">
                            <label for="">Lương cứng</label>
                            <input type="number" name="hard_salary" class="form-control input-default" placeholder="Lương cứng" required>
                        </div>
                        <div class="form-group">
                            <label for="">Trợ cấp</label>
                            <input type="number" name="subsidize" class="form-control input-default" placeholder="Trợ cấp" required>
                        </div>
                        <div class="form-group text-center">
                            <button class="btn btn-primary">Thêm mức lương</button>
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
            $('.js-example-basic-single').select2();
        });
    </script>
@endsection