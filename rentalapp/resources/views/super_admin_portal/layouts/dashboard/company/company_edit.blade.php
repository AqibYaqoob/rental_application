@extends('super_admin_portal.layouts.app')
@section('css')
    <link href="{{ asset('vendor/bootstrap-datetimepicker/bootstrap-datepicker.min.css') }}" rel="stylesheet">
@endsection
@section('content')
    <ol class="breadcrumb">
        <li class="breadcrumb-item">Home</li>
        <li class="breadcrumb-item"><a href="#">Admin</a></li>
        <li class="breadcrumb-item active">Edit company</li>
        <!-- Breadcrumb Menu-->
        <li class="breadcrumb-menu d-md-down-none">
            <div class="btn-group" role="group" aria-label="Button group with nested dropdown">
                <a class="btn" href="{{ url('admin/company/staff_list')}}"><i class="icon-graph"></i> &nbsp;Company List</a>
            </div>
        </li>
    </ol>
    <div class="container-fluid">
        @if ($errors->any())
            <div class="alert alert-danger">
                <ul class="list-group">
                    @foreach ($errors->all() as $error)
                        <li class="list-group-item">{{ $error }}</li>
                    @endforeach
                </ul>
            </div>
        @endif
        <div class="row">
            <div class="col-md-12">
                <div class="card">
                    <div class="card-header">
                        <i class="fa fa-align-justify"></i>
                        Edit Company
                    </div>
                    <div class="card-body">
                        <form action="{{route('company.update')}}" method="post">
                            <input type="hidden" name="_token"  value="{{csrf_token()}}" />
                            <input type="hidden" name="id" value="{{\Illuminate\Support\Facades\Crypt::encryptString($company->id)}}" />
                            <div class="row form-group">
                                <div class="col-md-2">Company Name</div>
                                <div class="col-md-9">
                                    <input type="text" name="company_name" value="{{$company->tenants->TenantName}}" class="form-control" required/>
                                </div>
                            </div>
                            <div class="row form-group">
                                <div class="col-md-2">Registered Date</div>
                                <div class="col-md-9">
                                    <input type="text" autocomplete="off" id="date" name="date" value="{{$company->created_at->format('d/m/Y')}}" class="form-control datepicker" required/>
                                </div>
                            </div>
                            <div class="text-center">
                                @if(\App\Helpers\GeneralFunctions::check_edit_permission('admin_company_list'))
                                    <button class="btn btn-md btn-primary">Update</button>
                                @endif
                            </div>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection

@section('js')
    <script src="{{ asset('vendor/bootstrap-datetimepicker/bootstrap-datepicker.min.js') }}"></script>
    <script type="text/javascript">
        $(document).ready(function () {
            $('.datepicker').datepicker({
                format: 'dd/mm/yyyy',
                startDate: '{{$company->created_at->format('d/m/Y')}}'
            });
        });
    </script>
@endsection