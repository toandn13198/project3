@extends('layout.template')

@section('title')
    Quản lý phòng ban
@endsection

@section('content')
    <div class="row">
        <div class="col-12">
            <div class="card">
                <div class="card-title">
                </div>
                <div class="card-body">
                    <div class="row no-gutters justify-content-between mb-1">
                        <div><a href="{{ base_url('department/add') }}" class="btn btn-primary">Thêm phòng ban</a></div>
                        <div>
                            <form action="">
                                <div class="input-group">
                                    <input class="form-control py-2 border-right-0 border" name="keyword" type="search" value="@isset($_GET['keyword']){{ $_GET['keyword'] }}@endisset" id="" placeholder="">
                                    <span class="input-group-append">
                                      <button class="btn btn-outline-secondary border-left-0 border" type="submit">
                                            <i class="fa fa-search"></i>
                                      </button>
                                    </span>
                                </div>
                            </form>
                        </div>
                    </div>
                    @if(count($data)>0)
                        <h3 class="text-info text-center">Danh sách phòng ban</h3>
                        <div class="table-responsive">
                        <table class="table table-striped">
                            <thead>
                            <tr>
                                <th>#</th>
                                <th>Tên phòng ban</th>
                                <th>Mô tả</th>
                                <th></th>
                            </tr>
                            </thead>
                            <tbody>
                                @foreach($data as $key => $department)
                                <tr>
                                    <th scope="row">{{ ($pagi->getPage()-1)*$pagi->getLimit() + $key + 1 }}</th>
                                    <td>{{ $department['name'] }}</td>
                                    <td>{{ $department['description'] }}</td>
                                    <td>
                                        <a href="{{ base_url('department/edit/' . $department['id']) }}"><i class="fa fa-pencil-square-o btn btn-warning" aria-hidden="true"></i></a>
                                        <a href="{{ base_url('department/delete/' . $department['id']) }}"><i class="fa fa-trash btn btn-danger" aria-hidden="true" onclick="return confirm('Bạn thực sự muốn xóa phòng ban này?')"></i></a>
                                    </td>
                                </tr>
                                @endforeach
                            </tbody>
                        </table>
                            {!! $pagi->getPagination() !!}
                    </div>
                    @else
                        <h3 class="text-warning">Không có phòng ban để hiển thị!!!</h3>
                    @endif
                </div>

            </div>
        </div>
    </div>
@endsection

@section('script')
@endsection