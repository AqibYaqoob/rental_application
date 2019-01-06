@extends('company_portal.layouts.app')
@section('content')
    @php
        $delete_url_path = '/admin/company/delete_site_account_transaction';
    @endphp
    <ol class="breadcrumb">
        <li class="breadcrumb-item"><a href="{{ url('admin/company/dashboard')}}">{{ trans('labels.dashboard') }}</a></li>
        <li class="breadcrumb-item active">{{ trans('labels.account_transaction_list') }}</li>
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
        <div class="row">
            <div class="col-lg-12">
                <div class="card">
                    <div class="card-header">
                        <i class="fa fa-align-justify"></i> {{ trans('labels.site_account_transaction_list') }}
                    </div>
                    <div class="card-body">
                        <table class="table table-responsive-sm table-bordered table-striped table-sm">
                            <thead>
                            <tr>
                                <th>{{ trans('labels.site_account_code') }}</th>
                                <th>{{ trans('labels.total_turnover_amount') }}</th>
                                <th>{{ trans('labels.total_profit_loss') }}</th>
                                <th>{{ trans('labels.current_rate') }}</th>
                                <th>{{ trans('labels.transaction_date') }}</th>
                                <th>{{ trans('labels.action') }}</th>
                            </tr>
                            </thead>
                            <tbody>
                            @foreach($record as $key => $value)
                                <tr>
                                    <td>{{$value['site_account']['SiteAccountCode']}}</td>
                                    <td>{{$value['TotalTurnover']}}</td>
                                    <td>{{$value['TotalProfitLoss']}}</td>
                                    <td>{{$value['CurrentRate']}}</td>
                                    <td>{!! GeneralFunctions::convertToDateTimeToString($value['TransactionDate']) !!}</td>
                                    <td>
                                        @php
                                            $edit_url_path = '/admin/company/site_account_transaction_form?id='.GeneralFunctions::encryptString($value['Id']);
                                        @endphp
                                        <div class="btn-group" role="group">
                                            <button id="btnGroupDrop1" type="button"
                                                    class="btn btn-secondary btn-sm dropdown-toggle"
                                                    data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
                                                {{ trans('labels.action') }}
                                            </button>
                                            <div class="dropdown-menu" aria-labelledby="btnGroupDrop1">
                                                @if(GeneralFunctions::check_edit_permission('site_account_transaction_list'))
                                                    <a href="{{url($edit_url_path)}}" class="dropdown-item"><i class="fa fa-edit"></i> {{ trans('labels.edit') }}</a>
                                                @endif
                                                @if(GeneralFunctions::check_delete_permission('site_account_transaction_list'))
                                                    <a class="dropdown-item delete delete_btn" id="{{$value['Id']}}"><i class="fa fa-trash"></i> {{ trans('labels.delete') }}</a>
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

    <!-- Remove Record Modal -->
    <div class="remodal" data-remodal-id="delete_modal"
         data-remodal-options="hashTracking: false, closeOnOutsideClick: false">
        <button data-remodal-action="close" class="remodal-close"></button>
        <h1>{{ trans('labels.delete_site_account_transaction') }}</h1>
        <p>
            {{ trans('labels.a.y.s.y.w.t.d.t.r') }}
        </p>
        <form id="delete_form" action="{{ url($delete_url_path) }}" method="POST">
            <input type="hidden" name="record_uuid" id="remodal_record_uuid">
            {{ csrf_field() }}
        </form>
        <br>
        <button data-remodal-action="cancel" class="remodal-cancel">{{ trans('cancel') }}</button>
        <button data-remodal-action="confirm" class="remodal-confirm">{{ trans('ok') }}</button>
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
