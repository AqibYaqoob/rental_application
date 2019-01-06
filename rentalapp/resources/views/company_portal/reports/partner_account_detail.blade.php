@extends('company_portal.layouts.app')
@section('content')
    <ol class="breadcrumb">
        <li class="breadcrumb-item"><a href="{{ url('admin/company/dashboard')}}">{{ trans('labels.dashboard') }}</a></li>
        <li class="breadcrumb-item active">{{ trans('labels.partner_account_detail') }}</li>
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
                        {{ trans('labels.p_account_detail') }}
                    </div>
                    <div class="card-body">
                        <table class="table table-responsive table-bordered table-striped table-sm">
                            <thead>
                            <th>{{ trans('labels.') }}</th>
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
                            {{--@php $count = 1; @endphp
                            @foreach($details as $detail)
                                <tr>
                                    <td>{{ $count++ }}</td>
                                    <td>{{ $detail->TransactionID }}</td>
                                    <td>{{ $detail->Amount }}</td>
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
                                        @endif
                                    </td>
                                    <td>{{ $detail->currency->CurrencyName }}</td>
                                    <td>{{ $detail->Current_Rate }}</td>
                                    <td>
                                        @if($detail->transfer_type == 1)
                                            Not P2P
                                            @elseif($detail->transfer_type == 2)
                                        P2P
                                            @endif
                                    </td>
                                    <td>{{ $detail->created_at->format('jS F Y g:ia') }}</td>
                                    <td>
                                        <div class="btn-group" role="group">
                                            <button id="btnGroupDrop1" type="button" class="btn btn-secondary btn-sm dropdown-toggle" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
                                                Action
                                            </button>
                                            <div class="dropdown-menu" aria-labelledby="btnGroupDrop1">
                                                <a href="javascript:void(0)" class="dropdown-item"><i class="fa fa-edit"></i> Settlement</a>
                                            </div>
                                        </div>
                                    </td>
                                </tr>
                            @endforeach--}}
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection
@section('js')
    <script type="text/javascript">
        $('table').DataTable({
            dom: 'Bfrtip',
            select: true,
            buttons: [
                {
                    extend:'excelHtml5',
                    exportOptions: {
                        columns: ':visible:not(th:last-child)'
                    }
                },
                {
                    extend:'csvHtml5',
                    exportOptions: {
                        columns: ':visible:not(th:last-child)'
                    }
                },
                {
                    extend:'pdfHtml5',
                    orientation: 'landscape',
                    pageSize: 'LEGAL',
                    exportOptions:
                        {
                            columns: ':visible:not(th:last-child)'
                        }
                }
            ],
            "processing": true,
            "serverSide": true,
            responsive: true,
            "ajax":{
                url: '{{ route('ajax.report.partner.account') }}',
                type: 'get',
                error:function (error) {
                    console.log(error);
                },
                complete:function (data) {
                    $('table').append('<tr><td></td><td></td><td></td><td></td><td class="bg-light"> {{ trans("labels.total") }}='+ data.responseJSON.total +'</td><td class="bg-light">{{ trans("labels.total") }}='+ data.responseJSON.baseTotal +'</td></tr>');
                }
            }
        });
    </script>
@endsection