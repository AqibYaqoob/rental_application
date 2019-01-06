@extends('company_portal.layouts.app')
@section('content')
    <ol class="breadcrumb">
        <li class="breadcrumb-item">{{trans('labels.home')}}</li>
        <li class="breadcrumb-item active">{{trans('labels.dashboard')}}</li>
        <!-- Breadcrumb Menu-->
        <li class="breadcrumb-menu d-md-down-none">
            <div class="btn-group" role="group" aria-label="Button group with nested dropdown">
                <a class="btn" href="#"><i class="icon-graph"></i>
                    &nbsp;{{ $balance . ' '. $currencyName }}</a>
            </div>
        </li>
    </ol>
    <!-- Main Content of the Page -->
    <div class="container-fluid">
        <div class="row">
            <div class="col-md-3">
                <div class="card">
                    <div class="card-body">
                        <div class="d-flex">
                            <span class="align-self-center p-3 m-top-7">
                                <i class="fa fa-address-card-o fa-3x"></i>
                            </span>
                            <div class="align-self-center">
                                <h6 class="text-muted m-t-10 m-b-0">{{ trans('labels.site_account') }}</h6>
                                <h2 class="m-t-0">{{ $totalSiteAccount }}</h2>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            <div class="col-md-3">
                <div class="card">
                    <div class="card-body">
                        <div class="d-flex">
                            <span class="align-self-center p-3 m-top-7">
                                <i class="fa fa-handshake-o fa-3x"></i>
                            </span>
                            <div class="align-self-center">
                                <h6 class="text-muted m-t-10 m-b-0">{{ trans('labels.partners') }}</h6>
                                <h2 class="m-t-0">{{ $totalPartners }}</h2>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            <div class="col-md-3">
                <div class="card">
                    <div class="card-body">
                        <div class="d-flex">
                            <span class="align-self-center p-3 m-top-7">
                                <i class="fa fa-slideshare fa-3x"></i>
                            </span>
                            <div class="align-self-center">
                                <h6 class="text-muted m-t-10 m-b-0">{{ trans('labels.shared_account') }}</h6>
                                <h2 class="m-t-0">{{ $totalSharedAccounts }}</h2>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            <div class="col-md-3">
                <div class="card">
                    <div class="card-body">
                        <div class="d-flex">
                            <span class="align-self-center p-3 m-top-7">
                                <i class="fa fa-money fa-3x"></i>
                            </span>
                            <div class="align-self-center">
                                <h6 class="text-muted m-t-10 m-b-0">{{ trans('labels.base_currency') }}</h6>
                                <h2 class="m-t-0">{{ $currencyName }}</h2>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <div class="animated fadeIn">
            <div class="row">
                <div class="col-md-12">
                    <div class="card">
                        <div class="card-header">
                            <i class="fa fa-align-justify"></i>
                            {{ trans('labels.recent_transactions') }}
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
                                    </tr>
                                @endforeach
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>

                {{--<div class="col-md-8">
                    <div class="card">
                        <div class="card-body">
                            <div class="row">
                                <div class="col-sm-6">
                                    <h4 class="card-title mb-0">Traffic</h4>
                                    <div class="small text-muted">November 2017 - December 2017</div>
                                </div>
                                <div class="col-sm-6">
                                    <button type="button" class="btn btn-outline-primary float-right"><i
                                                class="icon-cloud-download"></i></button>
                                    <div class="btn-toolbar float-right" role="toolbar"
                                         aria-label="Toolbar with button groups">
                                        <div class="btn-group mr-3" data-toggle="buttons" aria-label="First group">
                                            <label class="btn btn-outline-secondary">
                                                <input type="radio" name="options" id="option1"> Day
                                            </label>
                                            <label class="btn btn-outline-secondary active">
                                                <input type="radio" name="options" id="option2" checked=""> Month
                                            </label>
                                            <label class="btn btn-outline-secondary">
                                                <input type="radio" name="options" id="option3"> Year
                                            </label>
                                        </div>
                                    </div>
                                </div>
                            </div>
                            <hr class="mb-4">
                            <div class="chart-wrapper" style="height:343px;">
                                <canvas id="main-chart" height="343"></canvas>
                            </div>
                        </div>
                    </div>
                </div>--}}

                {{--<div class="col-md-4">
                    <div class="card">
                        <div class="card-body">
                            <h4 class="card-title mb-0">Traffic Details</h4>
                            <div class="small text-muted">November 2017 - December 2017</div>
                            <hr class="mb-4">

                            <div>Visits
                                <span class="font-weight-bold float-right">(40% - 29.703 Users)</span>
                            </div>
                            <div class="progress progress-xs mt-2 mb-3">
                                <div class="progress-bar bg-success" style="width: 40%" aria-valuenow="40"
                                     aria-valuemin="0" aria-valuemax="100"></div>
                            </div>

                            <div>Unique
                                <span class="font-weight-bold float-right">(20% - 24.093 Unique Users)</span>
                            </div>
                            <div class="progress progress-xs mt-2 mb-3">
                                <div class="progress-bar bg-info" style="width: 20%" aria-valuenow="20"
                                     aria-valuemin="0" aria-valuemax="100"></div>
                            </div>

                            <div>Pageviews
                                <span class="font-weight-bold float-right">(60% - 78.706 Views)</span>
                            </div>
                            <div class="progress progress-xs mt-2 mb-3">
                                <div class="progress-bar bg-warning" style="width: 60%" aria-valuenow="60"
                                     aria-valuemin="0" aria-valuemax="100"></div>
                            </div>

                            <div>New Users
                                <span class="font-weight-bold float-right">(80% - 22.123 Users)</span>
                            </div>
                            <div class="progress progress-xs mt-2 mb-3">
                                <div class="progress-bar bg-danger" style="width: 80%" aria-valuenow="80"
                                     aria-valuemin="0" aria-valuemax="100"></div>
                            </div>

                            <div>Bounce Rate
                                <span class="font-weight-bold float-right">(40.15%)</span>
                            </div>
                            <div class="progress progress-xs mt-2 mb-3">
                                <div class="progress-bar bg-success" style="width: 40%" aria-valuenow="40"
                                     aria-valuemin="0" aria-valuemax="100"></div>
                            </div>

                            <div>Visits
                                <span class="font-weight-bold float-right">(40% - 29.703 Users)</span>
                            </div>
                            <div class="progress progress-xs mt-2 mb-3">
                                <div class="progress-bar bg-success" style="width: 40%" aria-valuenow="40"
                                     aria-valuemin="0" aria-valuemax="100"></div>
                            </div>

                            <div>Unique
                                <span class="font-weight-bold float-right">(20% - 24.093 Unique Users)</span>
                            </div>
                            <div class="progress progress-xs mt-2 mb-3">
                                <div class="progress-bar bg-info" style="width: 20%" aria-valuenow="20"
                                     aria-valuemin="0" aria-valuemax="100"></div>
                            </div>
                        </div>
                    </div>
                </div>--}}

            </div>
            <!--/.row-->
            <!--/.row-->
        </div>

    </div>
@endsection
