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
                    <div class="row no-gutters justify-content-between mb-2" id="">
                        <div class="col-12 mb-2">
                            @if(isAdmin() || isHR() || isLeader())
                                <form action="" method="post">
                                    <a href="javascript:void(0)" class="btn btn-primary" data-toggle="modal" data-target="#myModal" >Thêm thành viên</a>
                                </form>
                            @endif
                        </div>
                        @if(count($members) > 0)
                            <div class="table-responsive">
                                <h3 class="text-info text-center">Danh sách thành viên của <span class="text-warning">{{ $team['name'] }}</span></h3>
                                <table class="table table-striped">
                                    <thead>
                                    <tr>
                                        <th>#</th>
                                        <th>Thành viên</th>
                                        <th>Ngày sinh</th>
                                        <th>Email</th>
                                        <th>Leader</th>
                                        @if(isAdmin() || isHR() || isLeader())
                                            <th></th>
                                        @endif
                                    </tr>
                                    </thead>
                                    <tbody>
                                    @foreach($members as $key => $member)
                                        <tr>
                                            <th scope="row">{{ $key+1 }}</th>
                                            <td>{{ $member['fullname'] }}</td>
                                            <td>{{ date('d-m-Y',strtotime($member['birthday'])) }}</td>
                                            <td>{{ $member['email'] }}</td>
                                            <td>
                                                @if($team['leader'] == $member['id'])
                                                    <a  href="{{ ((isAdmin() || isHR())) ? base_url('team/unsetLeader/' . $team['id']) : 'javascript:void(0)' }}" @if((isAdmin() || isHR()))onclick="return confirm('Bạn thực sự muốn xóa quyền Leader của {{$member['fullname']}} ?')"@endif><i class="fa fa-check-square-o text-info cursor-pointer" aria-hidden="true"></i></a>
                                                @else
                                                    <a href="{{ ((isAdmin() || isHR())) ? base_url('team/setLeader/' . $team['id'] . '/' . $member['id']) : 'javascript:void(0)' }}" @if((isAdmin() || isHR()))onclick="return confirm('Bạn thực sự muốn {{$member['fullname']}} trở thành Leader?')"@endif><i class="fa fa-square-o text-info cursor-pointer" aria-hidden="true"></i></a>
                                                @endif
                                            </td>

                                            <td>
                                                @if(isAdmin() || isHR() || (isLeader() && $member['id'] != getMyId()))
                                                    <a href="{{ base_url('team/removeMember/' . $team['id']) . '/' . $member['id']}}" @if((isAdmin() || isHR()) && $member['id'] == getMyId())onclick="return confirm('Bạn thực sự muốn rời khỏi nhóm!!!')"><i class="fa fa-sign-out btn btn-danger" aria-hidden="true"> Rời team</i> @else onclick="return confirm('Bạn thực sự muốn xóa {{ $member['fullname'] }} khỏi nhóm!!!')"><i class="fa fa-trash btn btn-danger" aria-hidden="true"></i>@endif</a>
                                                @endif
                                                @if(isAdmin() || isHR())
                                                    <a href="javascript:void(0)" data-team="{{ $team['id'] }}" data-name="{{ $member['fullname'] }}" data-id="{{ $member['id'] }}" class="btn-move"><i class="fa fa-arrows btn btn-warning" aria-hidden="true" data-toggle="modal" data-target="#moveTeam"></i></a>
                                                @endif
                                            </td>

                                        </tr>
                                    @endforeach
                                    </tbody>
                                </table>
                            </div>
                        @else
                            <h3 class="text-warning">Nhóm không có thành viên để hiển thị!</h3>
                        @endif
                    </div>
                </div>

            </div>
        </div>
    </div>
    <div class="modal animated bounceInDown" tabindex="-1" role="dialog" aria-labelledby="exampleModalLabel" aria-hidden="true" id="myModal">
        <div class="modal-dialog modal-lg" role="document">
            <div class="modal-content">
                <form action="{{ base_url('team/addMember/' . $team['id']) }}" method="post">
                    <div class="modal-header">
                        <h5 class="modal-title text-info">Thêm thành viên nhóm X</h5>
                        <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                            <span aria-hidden="true">&times;</span>
                        </button>
                    </div>
                    <div class="modal-body">
                        <div class="form-group">
                            <label for="">Thành viên</label>
                            <select class="js-example-basic-multiple form-control" name="member[]" multiple="multiple" required>
                                @foreach($users as $user)
                                    <option value="{{ $user['id'] }}">{{ $user['fullname'] }}</option>
                                @endforeach
                            </select>
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button class="btn btn-primary mr-2">Cập nhật</button>
                        <button type="button" class="btn btn-secondary" data-dismiss="modal">Đóng</button>
                    </div>
                </form>
            </div>
        </div>
    </div>
    <div class="modal animated bounceInDown" tabindex="-1" role="dialog" aria-labelledby="exampleModalLabel" aria-hidden="true" id="moveTeam">
        <div class="modal-dialog modal-lg" role="document">
            <div class="modal-content">
                <form action="{{ base_url('team/moveTeam/') }}" method="post">
                    <div class="modal-header">
                        <h5 class="modal-title text-info">Chuyển nhóm</h5>
                        <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                            <span aria-hidden="true">&times;</span>
                        </button>
                    </div>
                    <div class="modal-body">
                        <input type="hidden" name="id" value="" id="id-move">
                        <input type="hidden" name="old_team" value="" id="old-team">
                        <div>Thành viên được chuyển: <span id="name" class="text-info"></span></div>
                        <div class="form-group">
                            <label for="">Nhóm</label><br>
                            <select class="js-example-basic-single form-control" name="team" required>
                                @foreach($otherTeam as $value)
                                    <option value="{{ $value['id'] }}">{{ $value['name'] }}</option>
                                @endforeach
                            </select>
                        </div>
                        <div class="form-group">
                            <label for="">Chức vụ</label>
                            <select class="form-control w-40" name="role" required>
                                <option value="1" selected>Thành viên</option>
                                <option value="2">Trưởng nhóm</option>
                            </select>
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button class="btn btn-primary mr-2">Chuyển</button>
                        <button type="button" class="btn btn-secondary" data-dismiss="modal">Đóng</button>
                    </div>
                </form>
            </div>
        </div>
    </div>
@endsection

@section('script')
    <script>
        $(document).ready(function() {
            $('.js-example-basic-multiple,.js-example-basic-single').select2();
        });
        $(".btn-move").on("click", function () {
            $("#id-move").val($(this).data('id'));
            $("#name").text($(this).data('name'));
            $("#old-team").val($(this).data('team'));
        })
    </script>
@endsection