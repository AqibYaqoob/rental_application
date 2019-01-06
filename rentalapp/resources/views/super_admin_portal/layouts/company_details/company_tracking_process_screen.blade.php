@extends('super_admin_portal.layouts.app')
@section('content')
    <ol class="breadcrumb">
        <li class="breadcrumb-item">Home</li>
        <li class="breadcrumb-item"><a href="#">Admin</a></li>
        <li class="breadcrumb-item active">Dashboard</li>
        <!-- Breadcrumb Menu-->
        <li class="breadcrumb-menu d-md-down-none">
            <div class="btn-group" role="group" aria-label="Button group with nested dropdown">
                <a class="btn" href="{{ url('admin/company/dashboard')}}"><i class="icon-graph"></i> &nbsp;Dashboard</a>
            </div>
        </li>
    </ol>
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
        @if(session('success'))
            <div class="alert alert-success">
                {{session('success')}}
            </div>
        @endif
        @if(session('error'))
            <div class="alert alert-danger">
                {{session('error')}}
            </div>
        @endif
        <div class="row">
            <div class="col-md-12">
                <div class="card">
                    <div class="card-header">
                        <i class="fa fa-align-justify"></i>
                        Companies (Tenants Account)
                    </div>
                    <div class="card-body">
                        <ul class="nav nav-tabs" id="myTab" role="tablist">
                            <li class="nav-item">
                                <a class="nav-link active" id="home-tab" data-toggle="tab" href="#paid_account" role="tab" aria-controls="home" aria-selected="true">Active Payment Completed Accounts</a>
                            </li>
                            <li class="nav-item">
                                <a class="nav-link" id="home-tab" data-toggle="tab" href="#free_account" role="tab" aria-controls="home" aria-selected="true">Active Free Accounts</a>
                            </li>
                            <li class="nav-item">
                                <a class="nav-link" id="home-tab" data-toggle="tab" href="#rejected_account" role="tab" aria-controls="home" aria-selected="true">Rejected Accounts</a>
                            </li>
                        </ul>
                        <div class="tab-content" id="myTabContent">
                            <div class="tab-pane fade show active" id="paid_account" role="tabpanel" aria-labelledby="home-tab">
                                <table class="table table-responsive-sm table-bordered table-striped table-sm">
                                    <thead>
                                        <th>Details</th>
                                        <th>Company Name</th>
                                        <th>User Name</th>
                                        <th>Date registered</th>
                                        <th>Email</th>
                                        <th>Status</th>
                                    </thead>
                                    <tbody>
                                    @foreach($paid_active_accounts as $key => $value)
                                        <tr>
                                           <td><a href="{{route('company.details', ['id' => \Illuminate\Support\Facades\Crypt::encryptString($value['id'])])}}"><i class="fa fa-info"></i> Details</a></td>
                                            <td>{{$value['company_name']['TenantName']}}</td>
                                            <td>{{$value['Username']}}</td>
                                            <td>{!! GeneralFunctions::convertToDateTimeToString($value['created_at']) !!}</td>
                                            <td>{{$value['EmailAddress']}}</td>
                                            <td><span class="badge badge-success">Active</span></td>

                                        </tr>
                                    @endforeach
                                    </tbody>
                                </table>
                            </div>
                            <div class="tab-pane fade show" id="free_account" role="tabpanel" aria-labelledby="home-tab">
                                <table class="table table-responsive-sm table-bordered table-striped table-sm">
                                    <thead>
                                        <th>Details</th>
                                        <th>Company Name</th>
                                        <th>User Name</th>
                                        <th>Date registered</th>
                                        <th>Email</th>
                                        <th>Status</th>
                                    </thead>
                                    <tbody>
                                    @foreach($free_active_accounts as $key => $value)
                                        <tr>
                                            <td><a href="{{route('company.details', ['id' => \Illuminate\Support\Facades\Crypt::encryptString($value['id'])])}}"><i class="fa fa-info"></i> Details</a></td>
                                            <td>{{$value['company_name']['TenantName']}}</td>
                                            <td>{{$value['Username']}}</td>
                                            <td>{!! GeneralFunctions::convertToDateTimeToString($value['created_at']) !!}</td>
                                            <td>{{$value['EmailAddress']}}</td>
                                            <td><span class="badge badge-success">Active</span></td>
                                        </tr>
                                    @endforeach
                                    </tbody>
                                </table>
                            </div>
                            <div class="tab-pane fade show" id="rejected_account" role="tabpanel" aria-labelledby="home-tab">
                                <table class="table table-responsive-sm table-bordered table-striped table-sm">
                                    <thead>
                                        <th>Company Name</th>
                                        <th>User Name</th>
                                        <th>Date of Rejection</th>
                                        <th>Email</th>
                                        <th>Status</th>
                                    </thead>
                                    <tbody>
                                        @foreach($rejected_accounts as $key => $value)
                                            <tr>
                                                <td>{{$value['tenant_name']}}</td>
                                                <td>{{$value['username']}}</td>
                                                <td>{!! GeneralFunctions::convertToDateTimeToString($value['disapproval_date']) !!}</td>
                                                <td>{{$value['email_address']}}</td>
                                                <td><span class="badge badge-danger">Rejected</span></td>
                                            </tr>
                                        @endforeach
                                    </tbody>
                                </table>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection
@section('js')
    <script type="text/javascript">
        $(document).ready(function(){
            $(document).on('click', '.delete', function(){
                var id = $(this).attr('data-id');
                $('#data-id').val(id);
                var inst = $('[data-remodal-id=delete_modal]').remodal();
                inst.open();
            });
            $(document).on('click', '#delete_ok', function () {
                $('#state_form').submit()[0];
            });
            /*----------  Close Account Section  ----------*/
            $(document).on('click', '.close_account', function(){
                var id = $(this).attr('data-id');
                $('#close_acc_data_id').val(id);
                var inst = $('[data-remodal-id=close_modal_account]').remodal();
                inst.open();
            });
            $(document).on('click', '#close_ok', function () {
                console.log('Confirmation');
                $('#close_state_form').submit()[0];
            });
        });
    </script>
@endsection
