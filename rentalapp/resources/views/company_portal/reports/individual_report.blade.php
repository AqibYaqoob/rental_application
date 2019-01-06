@extends('company_portal.layouts.app')
@section('content')
    <ol class="breadcrumb">
        <li class="breadcrumb-item"><a href="{{ url('admin/company/dashboard')}}">{{ trans('labels.dashboard') }}</a></li>
        <li class="breadcrumb-item active">{{ trans('labels.individual_report') }}</li>
        <!-- Breadcrumb Menu-->
        <li class="breadcrumb-menu d-md-down-none">
            <div class="btn-group" role="group" aria-label="Button group with nested dropdown">
                <a class="btn" href="#"><i class="icon-graph"></i> &nbsp;{{ $balance . ' '. $currencyName }}</a>
            </div>
        </li>
    </ol>
    <div class="container-fluid">
        <div class="row">
            <div class="col-md-12">
                <div class="card">
                    <div class="card-header">
                        <i class="fa fa-align-justify"></i>
                        {{ trans('labels.individual_report') }}
                    </div>
                    <div class="card-body">
                        <ul class="nav nav-tabs" id="myTab" role="tablist">
                            <li class="nav-item">
                                <a class="nav-link active" id="home-tab" data-toggle="tab" href="#profitloss" role="tab" aria-controls="home" aria-selected="true">Profit/Loss</a>
                            </li>
                            <li class="nav-item">
                                <a class="nav-link" id="home-tab" data-toggle="tab" href="#account-detail" role="tab" aria-controls="home" aria-selected="true">Account Detail</a>
                            </li>
                            <li class="nav-item">
                                <a class="nav-link " id="home-tab" data-toggle="tab" href="#interest-detail" role="tab" aria-controls="home" aria-selected="true">Interest Detail</a>
                            </li>
                        </ul>
                        <div class="tab-content" id="myTabContent">
                            <div class="tab-pane fade show active" id="profitloss" role="tabpanel" aria-labelledby="home-tab">
                                <table class="table table1 table-responsive-md table-bordered table-striped table-sm">
                                    <thead>
                                    <th>{{ trans('labels.no') }}</th>
                                    <th>{{ trans('labels.partner_name') }}</th>
                                    <th>{{ trans('labels.site_account') }}</th>
                                    <th>{{ trans('labels.transaction_date') }}</th>
                                    <th>{{ trans('labels.total_profit_loss') }}</th>
                                    <th>{{ trans('labels.created_date') }}</th>
                                    </thead>
                                    <tbody>
                                    {{--@php $count = 1; @endphp
                                    @foreach($profitLoss as $data)
                                        <tr>
                                            <td>{{ $count++ }}</td>
                                            <td>{{ $data->user->Username }}</td>
                                            <td>{{ $data->sitesAccount->SiteAccountCode }}</td>
                                            <td>{{ $data->TransactionDate }}</td>
                                            <td class="text-center">{{ $data->TotalProfitLoss }}</td>
                                            <td>{{ $data->created_at->format('jS F Y g:ia') }}</td>
                                        </tr>
                                    @endforeach
                                    <tr>
                                        <td></td>
                                        <td></td>
                                        <td></td>
                                        <td></td>
                                        <td class="bg-light text-center"><span class="">Total={{ $totalProfitLoss }}</span></td>
                                        <td></td>
                                    </tr>--}}
                                    </tbody>
                                </table>
                            </div>
                            <div class="tab-pane fade show" id="account-detail" role="tabpanel" aria-labelledby="home-tab">
                                <table class="table table2 table-bordered table-striped table-sm">
                                    <thead>
                                    <th>{{ trans('labels.no') }}</th>
                                    <th>{{ trans('labels.transaction_id') }}</th>
                                    <th>{{ trans('labels.currency') }}</th>
                                    <th>{{ trans('labels.current_rate') }}</th>
                                    <th>{{ trans('labels.amount_in_currency') }}</th>
                                    <th>{{ trans('labels.amount_in_base_currency') }}</th>
                                    <th>{{ trans('labels.created_by') }}</th>
                                    <th>{{ trans('labels.remarks') }}</th>
                                    <th>{{ trans('labels.user_name') }}</th>
                                    <th>{{ trans('labels.account_status') }}</th>
                                    <th>{{ trans('labels.transfer_type') }}</th>
                                    <th>{{ trans('labels.created_date') }}</th>
                                    </thead>
                                    <tbody>
                                    {{--@php
                                        $count = 1;
                                        $totalConverted = 0;
                                        $totalAmount = 0;
                                    @endphp
                                    @foreach($details as $detail)
                                        @php
                                            $totalConverted += round($detail->Amount/$detail->Current_Rate * $rate, 2);
                                            $totalAmount += $detail->Amount;
                                        @endphp
                                        <tr>
                                            <td>{{ $count++ }}</td>
                                            <td>{{ $detail->TransactionID }}</td>
                                            <td>{{ $detail->currency->CurrencyName }}</td>
                                            <td>{{ $detail->Current_Rate }}</td>
                                            <td>{{ $detail->Amount }}</td>
                                            <td>{{ $detail->Amount/$detail->Current_Rate * $rate }}</td>
                                            <td>{{ $detail->CreatedBy }}</td>
                                            <td>{{ $detail->Remarks }}</td>
                                            <td>{{ $detail->CorrelationId }}</td>
                                            <td>{{ $detail->user->Username }}</td>
                                            <td>
                                                @if($detail->AccountStatus == 1)
                                                    WithDraw
                                                @elseif($detail->AccountStatus == 2)
                                                    Deposit
                                                @elseif($detail->AccountStatus == 3)
                                                    Profit/Loss
                                                @elseif($detail->AccountStatus == 4)
                                                    Monthly Interest
                                                @endif
                                            </td>
                                            <td>
                                                @if($detail->transfer_type == 1)
                                                    Not P2P
                                                @elseif($detail->transfer_type == 2)
                                                    P2P
                                                @endif
                                            </td>
                                            <td>{{ $detail->created_at->format('jS F Y g:ia') }}</td>
                                        </tr>
                                    @endforeach
                                    <tr>
                                        <td></td>
                                        <td></td>
                                        <td></td>
                                        <td></td>
                                        <td>{{ $totalAmount }}</td>
                                        <td> {{ $totalConverted }}</td>
                                        <td></td>
                                        <td></td>
                                        <td></td>
                                        <td></td>
                                        <td></td>
                                        <td></td>
                                        <td></td>
                                    </tr>--}}
                                    </tbody>
                                </table>
                            </div>
                            <div class="tab-pane fade show" id="interest-detail" role="tabpanel" aria-labelledby="home-tab">
                                <table class="table table3 table-bordered table-striped table-sm">
                                    <thead>
                                    <th>{{ trans('labels.no') }}</th>
                                    <th>{{ trans('labels.partner_name') }}</th>
                                    <th>{{ trans('labels.date') }}</th>
                                    <th>{{ trans('labels.amount') }}</th>
                                    </thead>
                                    <tbody>
                                    {{--@php $count =1; @endphp
                                    @foreach($interests as $interest)
                                        <tr>
                                            <td>{{ $count++ }}</td>
                                            <td>{{ $interest->created_at->format('jS F Y g:ia') }}</td>
                                            <td>{{ $interest->amount }}</td>
                                        </tr>
                                    @endforeach
                                    <tr>
                                        <td></td>
                                        <td></td>
                                        <td class="bg-light">{{ $totalInterest }}</td>
                                    </tr>--}}
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
        $(document).ready(function () {
            $('.table1').DataTable({
                "processing": true,
                "serverSide": true,
                "ajax":{
                    url: '{{ route('datatable.ajax', ['id' => $id]) }}',
                    type: 'get',
                    error:function (error) {
                        console.log(error);
                    }
                }
            });
            $('a[data-toggle="tab"]').on('shown.bs.tab', function (e) {
                var target = $(e.target).attr("href") // activated tab
                console.log(target);
                if(target == '#account-detail')
                {
                    if(! $.fn.DataTable.isDataTable( '.table2' ))
                    {
                        $('.table2').DataTable({
                            "processing": true,
                            "serverSide": true,
                            responsive: true,
                            ajax:{
                                url: '{{ route('datatable.ajax.account.detail', ['id' => $id]) }}',
                                type: 'get',
                                error:function (error) {
                                    console.log(error);
                                }
                            },
                            complete:function (data) {
                                $('.table2').append('<tr><td></td><td></td><td></td><td class="bg-light"> Total='+ data.responseJSON.total +'</td><td class="bg-light">Total='+ data.responseJSON.baseTotal +'</td></tr>');
                            }
                        });
                    }
                }
                if (target == '#interest-detail')
                {
                    if(! $.fn.DataTable.isDataTable( '.table3' ))
                    {
                        $('.table3').DataTable({
                            "processing": true,
                            "serverSide": true,
                            ajax:{
                                url: '{{ route('ajax.partner.interest.report', ['id' => $id]) }}',
                                type: 'get',
                                error:function (error) {
                                    console.log(error);
                                },
                                complete:function (data) {
                                    if( data.responseJSON.total != "undefined" )
                                        $('.table').append('<tr><td></td><td></td><td></td><td class="bg-light"> {{ trans("labels.total") }}='+ data.responseJSON.total +'</td></tr>');
                                }
                            }
                        });
                    }
                }
            });
        });
    </script>
@endsection