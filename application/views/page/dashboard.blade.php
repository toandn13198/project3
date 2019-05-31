@extends('layout.template')

@section('title')
    Dashboard
@endsection

@section('content')
    <div class="row justify-content-start">
        <div class="col-md-3">
            <div class="card p-30">
                <div class="media">
                    <div class="media-left meida media-middle">
                        <span><i class="fa fa-connectdevelop f-s-40 color-warning"></i></span>
                    </div>
                    <div class="media-body media-text-right">
                        <h2>{{ $department }}</h2>
                        <p class="m-b-0">Phòng ban</p>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-md-3">
            <div class="card p-30">
                <div class="media">
                    <div class="media-left meida media-middle">
                        <span><i class="fa fa-users f-s-40 color-primary"></i></span>
                    </div>
                    <div class="media-body media-text-right">
                        <h2>{{ $team }}</h2>
                        <p class="m-b-0">Nhóm</p>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-md-3">
            <div class="card p-30">
                <div class="media">
                    <div class="media-left meida media-middle">
                        <span><i class="fa fa-user f-s-40 color-info"></i></span>
                    </div>
                    <div class="media-body media-text-right">
                        <h2>{{ $user }}</h2>
                        <p class="m-b-0">Nhân viên</p>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-md-3">
            <div class="card p-30">
                <div class="media">
                    <div class="media-left meida media-middle">
                        <span><i class="fa fa-user f-s-40 color-danger"></i></span>
                    </div>
                    <div class="media-body media-text-right">
                        <h2>{{ $vacation }}</h2>
                        <p class="m-b-0">Lượt nghỉ phép</p>
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection

@section('script')
@endsection