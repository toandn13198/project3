@extends('layout.template')

@section('title')
    Quản lý nhóm
@endsection

@section('content')
    <div class="row">
        <div class="col-12">
            <div class="card">
                <div class="card-title">
                </div>
                <div class="card-body">
                    <div class="row no-gutters justify-content-between mb-1">
                        <form action="" class="col-12" id="form-filter">
                            <div class="form-row">
                                <div class="form-group col-sm-2 mr-2">
                                    <label for="">Phòng ban</label>
                                    <select id="department" class="form-control input-default" name="department">
                                        <option value="0" selected>Chọn phòng ban</option>
                                        @foreach($departments as $department)
                                            <option  value="{{ $department['id'] }}" @if(isset($_GET['department']) && ($_GET['department'] == $department['id'])) selected @endif>{{ $department['name'] }}</option>
                                        @endforeach
                                    </select>
                                </div>
                            </div>
                            <div class="row no-gutters justify-content-between mb-1" id="listTeam">
                                <div><a href="{{ base_url('team/add') }}" class="btn btn-primary">Thêm nhóm</a></div>
                                <div>
                                    <div class="input-group">
                                        <input class="form-control py-2 border-right-0 border" name="keyword" type="search" value="@isset($_GET['keyword']){{ $_GET['keyword'] }}@endisset" id="" placeholder="">
                                        <span class="input-group-append">
                                  <button class="btn btn-outline-secondary border-left-0 border" type="submit">
                                        <i class="fa fa-search"></i>
                                  </button>
                                </span>
                                    </div>
                                </div>
                            </div>
                        </form>
                    </div>
                        @if(count($data) > 0)
                        <div class="table-responsive">
                            <h3 class="text-info text-center">Danh sách nhóm</h3>
                            <table class="table table-striped">
                                <thead>
                                <tr>
                                    <th>#</th>
                                    <th>Tên nhóm</th>
                                    <th>Trưởng nhóm</th>
                                    <th>Phòng ban</th>
                                    <th></th>
                                </tr>
                                </thead>
                                <tbody>
                                @foreach($data as $key => $team)
                                <tr>
                                    <th scope="row">{{ ($pagi->getPage() -1)*$pagi->getLimit() + $key + 1 }}</th>
                                    <td>{{ $team['name_team'] }}</td>
                                    <td>{{ $team['name_leader'] }}</td>
                                    <td>{{ $team['name_department'] }}</td>
                                    <td>
                                        <a href="{{ base_url('team/detail/' . $team['id_team']) }}"><i class="fa fa-info-circle btn btn-info" aria-hidden="true"></i>
                                        </a>
                                        <a href="{{ base_url('team/edit/' . $team['id_team']) }}"><i class="fa fa-pencil-square-o btn btn-warning"></i></a>
                                        <a href="{{ base_url('team/delete/' . $team['id_team']) }}" onclick="return confirm('Bạn thực sự muốn xóa nhóm này?')"><i class="fa fa-trash btn btn-danger"></i></a>
                                    </td>
                                </tr>
                                @endforeach
                                </tbody>
                            </table>
                        </div>
                        {!! $pagi->getPagination() !!}
                        @else
                            <h3 class="text-warning">Không có dữ liệu nhóm để hiện thị!!!</h3>
                        @endif
                </div>

            </div>
        </div>
    </div>
@endsection

@section('script')
    <script>
        $(document).ready(function () {
            $("#department").on("change", function (e) {
                $("#form-filter").submit();
            })
        })
    </script>
@endsection