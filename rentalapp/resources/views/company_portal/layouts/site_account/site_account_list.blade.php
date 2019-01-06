@extends('company_portal.layouts.app')
@section('content')
    @php
        $delete_url_path = '/admin/company/delete_site_account';
    @endphp
    <ol class="breadcrumb">
        <li class="breadcrumb-item"><a href="{{ url('admin/company/dashboard')}}">{{ trans('labels.dashboard') }}</a></li>
        <li class="breadcrumb-item active">{{ trans('labels.site_account_list') }}</li>
        <!-- Breadcrumb Menu-->
        <li class="breadcrumb-menu d-md-down-none">
            <div class="btn-group" role="group" aria-label="Button group with nested dropdown">
                <a class="btn" href="#"><i class="icon-graph"></i> &nbsp;{{ $balance . ' '. $currencyName }}</a>
            </div>
        </li>
    </ol>
    <!-- Main Content of the Page -->
    <div class="container-fluid">
        @include('errors.flash_message')
        @if(session('success'))
            <div class="alert alert-success">
                {{session('success')}}
            </div>
        @endif
        <div class="row">
            <div class="col-lg-12">
                <div class="card">
                    <div class="card-header">
                        <i class="fa fa-align-justify"></i> {{ trans('labels.site_account_list') }}
                    </div>
                    <div class="card-body">
                        <table class="table table-responsive table-bordered table-striped table-sm">
                            <thead>
                            <tr>
                                <th>{{ trans('labels.site_name') }}</th>
                                <th>{{ trans('labels.company_name') }}</th>
                                <th>{{ trans('labels.selected_currency') }}</th>
                                <th>{{ trans('labels.status') }}</th>
                                <th>{{ trans('labels.site.account.code') }}</th>
                                <th>{{ trans('labels.total.turnover.percent') }}</th>
                                <th>{{ trans('labels.max.single.bet') }}</th>
                                <th> {{ trans('labels.credit') }}</th>
                                <th> {{ trans('labels.per_bet_value') }}</th>
                                <th>{{ trans('labels.created_date') }}</th>
                                <th>{{ trans('labels.action') }}</th>
                                <th>{{ trans('labels.assignment_report') }}</th>
                                <th>{{ trans('labels.shareholder_report') }}</th>
                            </tr>
                            </thead>
                            <tbody>
                            @foreach($record as $key => $value)
                                <tr>
                                    <td>{{$value['sites']['SiteName']}}</td>
                                    <td>{{$value['tenants']['TenantName']}}</td>
                                    <td>{{$value['currency']['currency_list']['currency'].' ('.$value['currency']['currency_list']['code'].')'}}</td>
                                    <td>
                                        @if($value['IsActive'] == 1)
                                            <span class="badge badge-success">{{ trans('labels.active') }}</span>
                                        @else
                                            <a href="" class="badge badge-danger">{{ trans('labels.inactive') }}</a>
                                        @endif
                                    </td>
                                    <td>{{$value['SiteAccountCode']}}</td>
                                    <td>
                                        {!! GeneralFunctions::toPercentage($value['TotalTurnoverPercent']) !!}
                                    </td>
                                    <td>{{$value['MaxSingleBet']}}</td>
                                    <td> {{ $value['credit'] }}</td>
                                    <td> {{ $value['per_bet'] }}</td>
                                    <td>{{date_format(new DateTime($value['created_at']), 'jS F Y g:ia') }}</td>
                                    <td>
                                        @php
                                            $edit_url_path = '/admin/company/site_account_form?id='.GeneralFunctions::encryptString($value['Id']);
                                        @endphp
                                        <div class="btn-group" role="group">
                                            <button id="btnGroupDrop1" type="button" class="btn btn-secondary btn-sm dropdown-toggle" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
                                                {{ trans('labels.action') }}
                                            </button>
                                            <div class="dropdown-menu" aria-labelledby="btnGroupDrop1">
                                                @if(GeneralFunctions::check_edit_permission('site_account_list'))
                                                    <a href="{{url($edit_url_path)}}" class="dropdown-item"><i class="fa fa-edit"></i>&nbsp; {{ trans('labels.edit') }}</a>
                                                @endif
                                                @if(GeneralFunctions::check_delete_permission('site_account_list'))
                                                    <a href="javascript:void(0)" id="{{$value['Id']}}" class="dropdown-item delete_btn"><i class="fa fa-trash"></i>&nbsp;{{ trans('labels.delete') }}</a>
                                                @endif
                                            </div>
                                        </div>
                                    </td>
                                    <td>
                                        @if(GeneralFunctions::check_view_permission('site_account_assignment_reports'))
                                            <a href="{{ route("partner.account.list", ['id' => \Illuminate\Support\Facades\Crypt::encryptString($value['Id'])]) }}" class="btn btn-sm btn-primary"><i class="fa fa-external-link"></i> View</a>
                                        @endif
                                    </td>
                                    <td>
                                        @if(GeneralFunctions::check_view_permission('site_account_shareholder_reports'))
                                            <a href="{{ route("shareHolder.account.list", ["id" => \Illuminate\Support\Facades\Crypt::encryptString($value['Id'])]) }}" class="btn btn-sm btn-primary"><i class="fa fa-external-link"></i> View</a>
                                        @endif
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

    <!-- Remove Record Modal -->
    <div class="remodal" data-remodal-id="delete_modal"
         data-remodal-options="hashTracking: false, closeOnOutsideClick: false">
        <button data-remodal-action="close" class="remodal-close"></button>
        <h1>{{ trans('labels.delete_site_account') }}</h1>
        <p>
            {{ trans('labels.a.y.s.y.w.t.d.t.r') }}
        </p>
        <form id="delete_form" action="{{ url($delete_url_path) }}" method="POST">
            <input type="hidden" name="record_uuid" id="remodal_record_uuid">
            {{ csrf_field() }}
        </form>
        <br>
        <button data-remodal-action="cancel" class="remodal-cancel">{{ trans('labels.cancel') }}</button>
        <button data-remodal-action="confirm" class="remodal-confirm">{{ trans('labels.ok') }}</button>
    </div>
@endsection
@section('js')
    <script type="text/javascript">
        $(document).ready(function () {
            var record_uuid;
            $(document).on('click', '.delete_btn', function () {
                record_uuid = $(this).attr('id');
                $('#remodal_record_uuid').val(record_uuid);
                var inst = $('[data-remodal-id=delete_modal]').remodal();
                inst.open();
            });
            $(document).on('confirmation', '.remodal', function () {
                $('#delete_form').submit()[0];
            });
        });
    </script>
@endsection