@extends('super_admin_portal.layouts.app')
@section('content')
    <ol class="breadcrumb">
        <li class="breadcrumb-item">Home</li>
        <li class="breadcrumb-item"><a href="#">Admin</a></li>
        <li class="breadcrumb-item active">Staff List</li>
        <!-- Breadcrumb Menu-->
        <li class="breadcrumb-menu d-md-down-none">
            <div class="btn-group" role="group" aria-label="Button group with nested dropdown">
                <a class="btn" href="{{ url('admin/company/staff_list')}}"><i class="icon-graph"></i> &nbsp;Company List</a>
            </div>
        </li>
    </ol>
    <!-- Main Content of the Page -->
    <div class="container-fluid">
        @if ($errors->any())
            <div class="alert alert-danger">
                <ul>
                    @foreach ($errors->all() as $error)
                        <li>{{ $error }}</li>
                    @endforeach
                </ul>
            </div>
        @endif
        @if (session('success'))
            <div class="alert alert-success">
                {{ session('success') }}
            </div>
        @endif
        <div class="row">
            <div class="col-lg-12">
                <div class="card">
                    <div class="card-header">
                        <i class="fa fa-align-justify"></i> Company List
                    </div>
                    <div class="card-body">
                        <table class="table table-responsive-sm table-bordered table-striped table-sm">
                            <thead>
                            <tr>
                                <th>Details</th>
                                <th>Company Name</th>
                                <th>User Name</th>
                                <th>Date registered</th>
                                <th>Email</th>
                                <th>Status</th>
                                <th>Action</th>
                            </tr>
                            </thead>
                            <tbody>
                            @foreach($record as $key => $value)
                                <tr>
                                    <td><a href="{{route('company.details', ['id' => \Illuminate\Support\Facades\Crypt::encryptString($value->id)])}}"><i class="fa fa-info"></i> Details</a></td>
                                    <td>{{$value['company_name']['TenantName']}}</td>
                                    <td>{{$value['Username']}}</td>
                                    <td>{!! GeneralFunctions::convertToDateTimeToString($value['created_at']) !!}</td>
                                    <td>{{$value['EmailAddress']}}</td>
                                    <td>
                                        @if($value['AccountStatus'] == 0)
                                            <span class="badge badge-danger">InActive</span>
                                        @else
                                            <a href="" class="badge badge-success">Active</a>
                                        @endif
                                    </td>
                                    <td>
                                        <div class="btn-group" role="group">
                                            <button id="btnGroupDrop1" type="button"
                                                    class="btn btn-secondary btn-sm dropdown-toggle"
                                                    data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
                                                Action
                                            </button>
                                            <div class="dropdown-menu" aria-labelledby="btnGroupDrop1">
                                                @if(GeneralFunctions::check_edit_permission('company_list'))
                                                    <a href="{{route('company.edit', ['id' => \Illuminate\Support\Facades\Crypt::encryptString($value->id)])}}" class="dropdown-item"><i class="fa fa-edit"></i> Edit</a>
                                                    @if($value['AccountStatus'] == 0)
                                                        <a class="dropdown-item state233" data-state="1" id="{{$value->id}}"><i class="fa fa-circle"></i> Active</a>
                                                    @else
                                                        <a class="dropdown-item state233" data-state="0" id="{{$value->id}}"><i class="fa fa-times-circle"></i> InActive</a>
                                                    @endif
                                                @endif
                                            </div>
                                        </div>
                                    </td>
                                </tr>
                            @endforeach
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
            <!--/.col-->
        </div>
    </div>
    {{--Modal--}}
    <div class="remodal" data-remodal-id="delete_modal" data-remodal-options="hashTracking: false, closeOnOutsideClick: false">
        <button data-remodal-action="close" class="remodal-close"></button>
        <h1>Change Company State</h1>

        <form id="state_form" action="{{ route('company.change.state') }}" method="POST">
            <input type="hidden" class="record_id" name="id" />
            <input type="hidden" id="state" name="state" value="" />
            {{ csrf_field() }}
            <textarea id="reason" name="reason" class="form-control" placeholder="Write a Reason" required></textarea>
        </form>
        <br>
        <button data-remodal-action="cancel" class="remodal-cancel">Cancel</button>
        <button data-remodal-action="confirm" class="remodal-confirm">OK</button>
    </div>
@endsection
@section('js')
    <script type="text/javascript">
        $(document).ready(function(){
            $(document).on('click', '.state233', function(){
                var state = $(this).attr('data-state');
                $('#state').val(state);
                $('.record_id').val($(this).attr('id'));
                if(state == 1)
                {
                    $('#reason').remove();
                    $('[data-remodal-action=confirm]').text('Activate');
                }
                var inst = $('[data-remodal-id=delete_modal]').remodal();
                inst.open();
            });
            $(document).on('confirmation', '.remodal', function () {
                $('#state_form').submit()[0];
            });
        });
    </script>
@endsection
