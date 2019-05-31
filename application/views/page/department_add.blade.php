@extends('layout.template')

@section('title')
    Quản lý phòng ban
@endsection

@section('content')
    <div class="row no-gutters justify-content-center">
        <div class="col-xs-12 col-sm-7">
            <div class="card">
                <div class="card-title">
                    <h3 class="text-center text-info">Thêm phòng ban</h3>
                </div>
                <div class="card-body">
                    <form action="{{ base_url('department/insert') }}" method="post" id="form-insert">
                        <div class="form-group">
                            <label for="">Tên phòng ban</label>
                            <input type="text" name="name" class="form-control input-default" placeholder="Tên phòng ban">
                        </div>
                        <div class="form-group">
                            <label for="">Mô tả</label>
                            <textarea class="form-control input-default" name="description" rows="3" placeholder="Mô tả"></textarea>
                        </div>
                        <div class="form-group text-center">
                            <button class="btn btn-primary">Thêm phòng ban</button>
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
            $("#form-insert").validate({
                rules: {
                    name: {
                        required: true,
                        remote: {
                            url: "{{ base_url('department/checkName/') }}",
                            async:false
                        }
                    },
                    description: {
                        required: true,
                    }
                },
                messages: {
                    name:{
                        required: "Vui lòng nhập tên phòng ban.",
                        remote: "Tên phòng ban đã tồn tại."
                    },
                    description:{
                        required: "Vui lòng nhập mô tả",
                    }
                }
            });
        })
    </script>
@endsection