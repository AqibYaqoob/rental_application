@extends('company_portal.layouts.app')
@section('content')

    <ol class="breadcrumb">
        <li class="breadcrumb-item"><a href="{{ url('admin/company/dashboard')}}">{{ trans('labels.dashboard') }}</a></li>
        <li class="breadcrumb-item active">{{ trans('labels.transaction_list') }}</li>
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
                        <i class="fa fa-align-justify"></i> {{ trans('labels.tenant_account_transaction_list') }}
                    </div>
                    <div class="card-body">
                        <!--               <ul class="nav nav-tabs" id="myTab" role="tablist">
                                          <li class="nav-item">
                                              <a class="nav-link active" id="home-tab" data-toggle="tab" href="#active" role="tab" aria-controls="home" aria-selected="true">Tenant Fund Transaction List</a>
                                          </li>
                                          <li class="nav-item">
                                              <a class="nav-link" id="home-tab" data-toggle="tab" href="#end" role="tab" aria-controls="home" aria-selected="true">Fund Transaction List</a>
                                          </li>
                                      </ul> -->
                        <!-- <div class="tab-pane fade show active" id="active" role="tabpanel" aria-labelledby="home-tab"> -->
                        <table class="table table-responsive table-bordered table-striped">
                            <thead>
                            <tr>
                                <th>#</th>
                                <th>{{ trans('labels.transaction_id') }}</th>
                                <th>{{ trans('labels.source') }}</th>
                                <th>{{ trans('labels.transaction') }}</th>
                                <th>{{ trans('labels.in_currencies') }}</th>
                                <th>{{ trans('labels.amount_in_currencies') }}</th>
                                <th>{{ trans('labels.amount_in_base_currency') }}</th>
                                <th>{{ trans('labels.remarks') }}</th>
                                <th>{{ trans('labels.transaction_date') }}</th>
                                <th>{{ trans('labels.action') }}</th>
                            </tr>
                            </thead>
                            <tbody>
                            @foreach($responseResult as $key => $value)
                                <tr  {!! $value['reference_transaction_id'] != null ? 'style="background-color: #c7c344;"' : '' !!}>
                                    <td>{{$value['no']}}</td>
                                    <td>{{$value['transaction_id']}}</td>
                                    <td>{{$value['source']}}</td>
                                    <td>{{$value['transaction']}}</td>
                                    <td>{{$value['in_currency']}}</td>
                                    <td>{{$value['amount_in_different_currencies']}}</td>
                                    <td>{{$value['amount_in_base_currency']}}</td>
                                    @if($value['reference_transaction_id'] != null)
                                        <td>{!! $value['remarks'].'. (Reference with Transaction '.$value['reference_transaction_id'].')' !!}</td>
                                    @else
                                        <td>{!! $value['remarks'] !!}</td>
                                    @endif
                                    <td>{!! GeneralFunctions::convertToDateTimeToString($value['created_at']) !!}</td>
                                    <td>
                                        @php
                                            $edit_url_path = '/admin/company/fund_transfer/edit?id='.GeneralFunctions::encryptString($value['id']);
                                        @endphp
                                        @if($value['account_status'] != 3)
                                            @if($value['reference_transaction_id'] == null)
                                                <div class="btn-group" role="group">
                                                    <button id="btnGroupDrop1" type="button" class="btn btn-secondary btn-sm dropdown-toggle" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
                                                        {{ trans('labels.action') }}
                                                    </button>
                                                    <div class="dropdown-menu" aria-labelledby="btnGroupDrop1">
                                                        @if(GeneralFunctions::check_edit_permission('tenant_account_details_transaction_list'))
                                                            <a href="{{url($edit_url_path)}}" class="dropdown-item">{{ trans('labels.settle_account') }}</a>
                                                        @endif
                                                    </div>
                                                </div>
                                            @endif
                                        @endif
                                    </td>
                                </tr>
                            @endforeach
                            </tbody>
                            <tfoot>
                            <td></td>
                            <td></td>
                            <td></td>
                            <td></td>
                            <td></td>
                            <td>{{ trans('labels.total') }}</td>
                            <td>{{$totalBalance}}</td>
                            <td></td>
                            <td></td>
                            <td></td>
                            </tfoot>
                        </table>
                        <!-- </div> -->
                    </div>
                    <!--/.col-->
                </div>
            </div>
        </div>
    </div>
@endsection
