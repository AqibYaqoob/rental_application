@extends('company_portal.layouts.app')
@section('css')
    <link href="{{ asset('vendor/bootstrap-datetimepicker/bootstrap-datepicker.min.css') }}" rel="stylesheet">
@endsection
@section('content')
    <ol class="breadcrumb">
        <li class="breadcrumb-item"><a href="{{ url('admin/company/dashboard')}}">{{ trans('labels.dashboard') }}</a></li>
        <li class="breadcrumb-item active">{{ trans('labels.profitloss_sharing') }}</li>
        <!-- Breadcrumb Menu-->
        <li class="breadcrumb-menu d-md-down-none">
            <div class="btn-group" role="group" aria-label="Button group with nested dropdown">
                <a class="btn" href="#"><i class="icon-graph"></i> &nbsp;{{ $balance . ' '. $currencyName }}</a>
            </div>
        </li>
    </ol>
    <div class="container-fluid">
        @include('errors.flash_message')
        @if(session('error'))
            <div class="alert alert-danger">
                {{ session('error') }}
            </div>
        @endif
        <div class="row">
            <div class="col-md-12">
                <div class="card">
                    <div class="card-header">
                        <i class="fa fa-align-justify"></i>
                        {{ trans('labels.profit_sharing') }}
                    </div>
                    <div class="card-body">
                        {{--<form action="{{route('company.share.profit.partner')}}" method="post">
                            {{csrf_field()}}--}}
                        <div class="row form-group">
                            <div class="col-md-2">{{ trans('labels.start_date') }}</div>
                            <div class="col-md-9">
                                <input id="start" type="text" class="form-control datepicker" autocomplete="off" autocomplete="nope" />
                            </div>
                        </div>
                        <div class="row form-group">
                            <div class="col-md-2">{{ trans('labels.end_date') }}</div>
                            <div class="col-md-9">
                                <input id="end" type="text" class="form-control datepicker" autocomplete="off" autocomplete="nope"/>
                            </div>
                        </div>
                        {{--<div class="row form-group">
                            <div class="col-md-2">Current Rate</div>
                            <div class="col-md-9">
                                <input id="number" type="number" step="any" name="rate" value="{{ $rate }}" class="form-control" />
                            </div>
                        </div>--}}
                        <div class="row text-center">
                            <div class="col-md-9 offset-2">
                                @if(GeneralFunctions::check_add_permission('share_profit'))
                                    <button class="btn btn-md btn-primary detail">{{ trans('labels.share') }}</button>
                                @endif
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
    {{--Modal--}}
    <div class="remodal" data-remodal-id="detail_modal" data-remodal-options="hashTracking: false, closeOnOutsideClick: false">
        <button data-remodal-action="close" class="remodal-close"></button>
        <h1>{{ trans('labels.transaction_details') }}</h1>
        <form id="state_form" action="{{ route('company.share.profit.partner') }}" method="POST">
            {{ csrf_field() }}
            <input type="hidden" id="start_date" name="start" value=""/>
            <input type="hidden" id="end_date" name="end" value=""/>
            <table class="table table-responsive-sm table-bordered table-striped table-sm">
                <thead>
                <th>#</th>
                <th>{{ trans("labels.account_code") }}</th>
                <th>{{ trans("labels.number_of_transactions") }}</th>
                <th>{{ trans("labels.currency") }}</th>
                <th>{{ trans("labels.current_rate") }}</th>
                </thead>
                <tbody id="tbody">
                <tr>
                    <td colspan="6">No Record Found!</td>
                </tr>
                </tbody>
            </table>
        </form>
        <br>
        <button data-remodal-action="cancel" class="remodal-cancel">{{ trans('labels.cancel') }}</button>
        <button data-remodal-action="confirm" class="remodal-confirm">{{ trans('labels.ok') }}</button>
    </div>
@endsection
@section('js')
    <script src="{{ asset('vendor/bootstrap-datetimepicker/bootstrap-datepicker.min.js') }}"></script>
    <script>
        $(document).ready(function(){
            $(document).on('click', '.detail', function(){
                var start = $('#start').val();
                var end = $('#end').val();
                if(!isNaN(Date.parse(start)) && !isNaN(Date.parse(end)))
                {
                    $.ajax({
                        url:'{{route("company.detail.profitLoss")}}',
                        type:'Post',
                        data:{'start':start,'end':end,'_token':'{{csrf_token()}}'},
                        dataType: 'json',
                        statusCode:{
                            200:function (result) {
                                if (!$.isEmptyObject(result))
                                {
                                    $('#start_date').val(start);
                                    $('#end_date').val(end);
                                    var rows;
                                    var count = 1;
                                    $.each(result, function (index, value) {
                                        rows += '<tr>';
                                        rows += '<td>'+ count +'</td>';
                                        rows += '<td>'+ value[0] +'</td>';
                                        rows += '<td>'+ value[1] +'</td>';
                                        rows += '<td>'+ value['cur'][1] +'</td>';
                                        //rows += '<td><input type="number" step="any" name="'+ index +''+'-'+ value['cur'][0] +'" value="'+ value['cur'][2] +'" class="form-control"></td>';
                                        rows += '<td><input type="number" step="any" name="'+ index +''+'-'+ value['cur'][0] +'" value="'+ value['cur'][2] +'" class="form-control"></td>';
                                        rows += '</tr>';
                                        count += 1;
                                    });
                                    $('#tbody').html(rows);
                                }
                            }
                        }
                    });
                    var inst = $('[data-remodal-id=detail_modal]').remodal();
                    inst.open();
                    $(document).on('confirmation', '.remodal', function () {
                        if (!isNaN(Date.parse($('#start_date').val())) && !isNaN(Date.parse($('#end_date').val())))
                        {
                            $('#state_form').submit()[0];
                        }
                    });
                }
            });
        });
    </script>
@endsection
