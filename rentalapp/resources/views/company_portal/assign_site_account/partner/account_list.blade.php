@extends('company_portal.layouts.app')
@section('content')
    <ol class="breadcrumb">
        <li class="breadcrumb-item">Home</li>
        <li class="breadcrumb-item"><a href="#">Admin</a></li>
        <li class="breadcrumb-item active">Dashboard</li>
        <!-- Breadcrumb Menu-->
        <li class="breadcrumb-menu d-md-down-none">
            <div class="btn-group" role="group" aria-label="Button group with nested dropdown">
                <a class="btn" href="{{ url('admin/company/dashboard')}}"><i class="icon-graph"></i> &nbsp;{{ $balance . ' '. $currencyName }}</a>
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
                        {{ trans('labels.partner.site.account.list') }}
                    </div>
                    <div class="card-body">
                        <ul class="nav nav-tabs" id="myTab" role="tablist">
                            <li class="nav-item">
                                <a class="nav-link active" id="home-tab" data-toggle="tab" href="#active" role="tab" aria-controls="home" aria-selected="true">{{ trans('labels.assigned') }}</a>
                            </li>
                            <li class="nav-item">
                                <a class="nav-link" id="home-tab" data-toggle="tab" href="#end" role="tab" aria-controls="home" aria-selected="true">{{ trans('labels.expired') }}</a>
                            </li>
                        </ul>
                        <div class="tab-content" id="myTabContent">
                            <div class="tab-pane fade show active" id="active" role="tabpanel" aria-labelledby="home-tab">
                                <table class="table table-responsive-sm table-bordered table-striped table-sm">
                                    <thead>
                                    <th>{{ trans('labels.no') }}</th>
                                    <th>{{ trans('labels.partner') }}</th>
                                    <th>{{ trans('labels.site') }}</th>
                                    <th>{{ trans('labels.assigned_by') }}</th>
                                    <th>{{ trans('labels.started_date') }}</th>
                                    <th>{{ trans('labels.end_date') }}</th>
                                    <th>{{ trans('labels.action') }}</th>
                                    </thead>
                                    <tbody>
                                    @php $count = 1; @endphp
                                    @foreach($partnerAccounts as $partnerAccount)
                                        @if(!is_null($partnerAccount->EndDate))
                                            @continue
                                        @endif
                                        <tr>
                                            <td>{{ $count++ }}</td>
                                            <td>{{$partnerAccount->user->Username}}</td>
                                            <td>{{$partnerAccount->sitesAccount->sites->SiteName}}</td>
                                            <td>{{ $partnerAccount->AssignedBy }}</td>
                                            <td>{!! GeneralFunctions::convertToDateTimeToString($partnerAccount->created_at) !!}</td>
                                            <td><span class="badge badge-success">{{ trans('labels.active') }}</span></td>
                                            <td>
                                                <div class="btn-group" role="group">
                                                    <button id="btnGroupDrop1" type="button" class="btn btn-secondary btn-sm dropdown-toggle" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
                                                        {{ trans('labels.action') }}
                                                    </button>
                                                    <div class="dropdown-menu" aria-labelledby="btnGroupDrop1">
                                                        @if(GeneralFunctions::check_edit_permission('site_account_assignment_reports'))
                                                            <a href="{{route('partner.account.list.edit', ['id' => \Illuminate\Support\Facades\Crypt::encryptString($partnerAccount->Id)])}}" class="dropdown-item"><i class="fa fa-edit"></i> {{ trans('labels.edit') }}</a>
                                                        @endif
                                                        {{--@if(GeneralFunctions::check_delete_permission('site_account_assignment_reports'))
                                                            <a class="dropdown-item delete" data-id="{{\Illuminate\Support\Facades\Crypt::encryptString($partnerAccount->Id)}}"><i class="fa fa-trash"></i> {{ trans('labels.delete') }}</a>
                                                        @endif--}}
                                                    </div>
                                                </div>
                                            </td>
                                        </tr>
                                    @endforeach
                                    </tbody>
                                </table>
                            </div>
                            <div class="tab-pane fade show" id="end" role="tabpanel" aria-labelledby="home-tab">
                                <table class="table table-responsive-sm table-bordered table-striped table-sm">
                                    <thead>
                                    <th>{{ trans('labels.no') }}</th>
                                    <th>{{ trans('labels.partner') }}</th>
                                    <th>{{ trans('labels.site') }}</th>
                                    <th>{{ trans('labels.assigned_by') }}</th>
                                    <th>{{ trans('labels.started_date') }}</th>
                                    <th>{{ trans('labels.end_date') }}</th>
                                    <th>{{ trans('labels.action') }}</th>
                                    </thead>
                                    <tbody>
                                    @php $count = 1; @endphp
                                    @foreach($partnerAccounts as $partnerAccount)
                                        @if(is_null($partnerAccount->EndDate))
                                            @continue
                                        @endif
                                        <tr>
                                            <td>{{ $count++ }}</td>
                                            <td>{{$partnerAccount->user->Username}}</td>
                                            <td>{{$partnerAccount->sitesAccount->sites->SiteName}}</td>
                                            <td>{{ $partnerAccount->AssignedBy }}</td>
                                            <td>{!! GeneralFunctions::convertToDateTimeToString($partnerAccount->created_at) !!}</td>
                                            <td>{{ GeneralFunctions::convertToDateTimeToString($partnerAccount->EndDate) }}</td>
                                            <td>
                                                <div class="btn-group" role="group">
                                                    <button id="btnGroupDrop1" type="button" class="btn btn-secondary btn-sm dropdown-toggle" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
                                                        {{ trans('labels.action') }}
                                                    </button>
                                                    <div class="dropdown-menu" aria-labelledby="btnGroupDrop1">
                                                        {{--@if(GeneralFunctions::check_edit_permission('site_account_assignment_reports'))
                                                            <a href="{{route('partner.account.list.edit', ['id' => \Illuminate\Support\Facades\Crypt::encryptString($partnerAccount->Id)])}}" class="dropdown-item"><i class="fa fa-edit"></i> {{ trans('labels.edit') }}</a>
                                                        @endif--}}
                                                        @if(GeneralFunctions::check_delete_permission('site_account_assignment_reports'))
                                                            <a class="dropdown-item delete" data-id="{{\Illuminate\Support\Facades\Crypt::encryptString($partnerAccount->Id)}}"><i class="fa fa-trash"></i> {{ trans('labels.delete') }}</a>
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
                </div>
            </div>
        </div>
    </div>
    {{--Modal--}}
    <div class="remodal" data-remodal-id="delete_modal" data-remodal-options="hashTracking: false, closeOnOutsideClick: false">
        <button data-remodal-action="close" class="remodal-close"></button>
        <h1>{{ trans('labels.del_partner_assignment') }}</h1>
        <p>
            {{ trans('labels.are_you_sure_to_delete_it') }}
        </p>
        <form id="state_form" action="{{ route('partner.assigned.deletion') }}" method="POST">
            <input type="hidden" id="data-id" name="id" value=""/>
            {{ csrf_field() }}
        </form>
        <br>
        <button data-remodal-action="cancel" class="remodal-cancel">{{ trans('labels.cancel') }}</button>
        <button data-remodal-action="confirm" class="remodal-confirm">{{ trans('labels.ok') }}</button>
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
            $(document).on('confirmation', '.remodal', function () {
                $('#state_form').submit()[0];
            });
        });
    </script>
@endsection