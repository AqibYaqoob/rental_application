@extends('company_portal.layouts.app')
@section('content')
    <ol class="breadcrumb">
        <li class="breadcrumb-item"><a href="{{ url('admin/company/dashboard')}}">{{ trans('labels.dashboard') }}</a></li>
        <li class="breadcrumb-item active">{{ trans('labels.complete_interest_report') }}</li>
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
                        {{ trans('labels.complete_interest_report') }}
                    </div>
                    <div class="card-body">
                        <table class="table table-responsive-md table-bordered table-striped table-sm">
                            <thead>
                            <th>{{ trans('labels.no') }}</th>
                            <th>{{ trans('labels.partner_name') }}</th>
                            <th>{{ trans('labels.date') }}</th>
                            <th>{{ trans('labels.amount') }}</th>
                            </thead>
                            <tbody>
                            <tr>
                                <td></td>
                                <td></td>
                                <td></td>
                                <td></td>
                            </tr>
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
        $(document).ready(function () {
            $('.table').DataTable({
                "processing": true,
                "serverSide": true,
                ajax:{
                    url: '{{ route('ajax.partner.interest.report') }}',
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
        });
    </script>
@endsection