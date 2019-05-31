@extends('layout.template')

@section('title')
    Nhập dữ liệu chấm công
@endsection

@section('content')
    <div class="row p-5 justify-content-center mt-5">
            <form action="{{ base_url('timekeeping/postImport') }}" method="post" enctype="multipart/form-data" id="form-import">
                @if($exist)
                    <div>
                        <h3 class="text-warning"><i class="fa fa-exclamation-triangle" aria-hidden="true"></i> Dữ liệu chấm công đã tồn tại.</h3>
                        <h3 class="text-primary">Bạn muốn:</h3>
                        <div class="pretty p-icon p-round p-jelly d-block mb-3">
                            <input type="radio" name="option" value="1" checked/>
                            <div class="state p-primary">
                                <i class="icon mdi mdi-check"></i>
                                <label class="text-description">Cập nhật (Không thay đổi dữ liệu cũ. Chỉ thêm những ngày không có dữ liệu.)</label>
                            </div>
                        </div>
                        <div class="pretty p-icon p-round p-jelly d-block mb-3">
                            <input type="radio" name="option" value="2"/>
                            <div class="state p-primary">
                                <i class="icon mdi mdi-check"></i>
                                <label class="text-description">Ghi đè (Ghi đè dữ liệu những ngày trùng và thêm mới nếu chưa có.)</label>
                            </div>
                        </div>
                        <div class="pretty p-icon p-round p-jelly d-block mb-3">
                            <input type="radio" name="option" value="3"/>
                            <div class="state p-primary">
                                <i class="icon mdi mdi-check"></i>
                                <label class="text-description">Tạo mới (Xóa hết dữ liệu cũ. Và import dữ liệu mới).</label>
                            </div>
                        </div>
                    </div>
                @endif
                <div>
                    @if($data !== NULL)
                        <input type="hidden" name="file" value="{{ $data }}" />
                    @else
                        <input type="file" name="data" class="input-image" id="choose-file"  accept=".csv"><br>
                        <label for="choose-file" class="custom-file-upload mt-1">
                            <i class="fa fa-cloud-upload"></i> Chọn file
                        </label>
                    @endif
                    <button class="btn btn-primary mt-3 mb-3">Nhập dữ liệu</button>
                </div>
                <div id="info">
                    <a href="javascript:void(0)" class="text-info" id="name"></a> <span id="size"></span>
                </div>
            </form>
        </div>
@endsection

@section('script')
    <script>
        $(document).ready(function () {
            $("#choose-file").on("change", function () {
                let file_upload = document.getElementById('choose-file');
                $("#name").html(file_upload.files[0].name);
                $("#size").html('(' + Math.round((file_upload.files[0].size / 1024)) + '</b> KB)');
                console.log(file_upload.files[0]);
            });
            $( "input[name='option']" ).on('change', function () {
                $('label.text-description').removeClass('animated pulse delay-1s');
                $(this).parent().find('label.text-description').addClass('animated pulse delay-1s')
            });
            $("#form-import").on("submit", function () {
                $('.flower-spinner').css('display','flex');
            })
        })
    </script>
@endsection