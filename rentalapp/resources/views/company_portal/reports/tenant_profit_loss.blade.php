@extends('company_portal.layouts.app')
@section('content')
    <ol class="breadcrumb">
        <li class="breadcrumb-item"><a href="{{ url('admin/company/dashboard')}}">{{ trans('labels.dashboard') }}</a></li>
        <li class="breadcrumb-item active">{{ trans('labels.tenant_profit_loss') }}</li>
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
                        {{ trans('labels.tenant_profitLoss_report') }}
                    </div>
                    <div class="card-body">
                        <table class="table table-responsive-md table-bordered table-striped table-sm">
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
                                    <td>{{ $data->TotalProfitLoss }}</td>
                                    <td>{{ $data->created_at->format('jS F Y g:ia') }}</td>
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
                url: '{{ route('ajax.report.tenant.profitloss') }}',
                type: 'get',
                error:function (error) {
                    console.log(error);
                },
                complete:function (data) {
                    $('table').append('<tr><td></td><td></td><td></td><td></td><td class="bg-light"> {{ trans("labels.total") }}='+ data.responseJSON.total +'</td><td></td></tr>');
                }
            }
        });
    </script>
@endsection