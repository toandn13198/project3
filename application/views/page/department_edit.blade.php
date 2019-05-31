@extends('layout.template')

@section('title')
    Sửa phòng ban
@endsection

@section('content')
    <div class="row no-gutters justify-content-center">
        <div class="col-xs-12 col-sm-7">
            <div class="card">
                <div class="card-title">
                    <h3 class="text-center">Sửa phòng ban</h3>
                </div>
                <div class="card-body">
                    <form action="{{ base_url('department/update') }}" method="post" id="form-edit">
                        <input type="hidden" name="id" value="{{ $id }}" id="id-department">
                        <div class="form-group">
                            <label for="">Tên phòng ban</label>
                            <input type="text" name="name" value="{{ $name }}" class="form-control input-default" placeholder="Tên phòng ban">
                        </div>
                        <div class="form-group">
                            <label for="">Mô tả</label>
                            <textarea class="form-control input-default" name="description" rows="3" placeholder="Mô tả">{{ $description }}</textarea>
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
    <script>
        $(document).ready(function () {
            $("#form-edit").validate({
                rules: {
                    name: {
                        required: true,
                        remote: {
                            url: "{{ base_url('department/checkName/') }}",
                            data: {id : $("#id-department").val()},
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