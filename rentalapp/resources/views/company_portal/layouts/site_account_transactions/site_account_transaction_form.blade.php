@extends('company_portal.layouts.app')
@section('css')
    <link href="{{ asset('vendor/bootstrap-datetimepicker/bootstrap-datepicker.min.css') }}" rel="stylesheet">
    <link href="{{ asset('vendor/repeater/qunit-1.14.0.css') }}" rel="stylesheet">
@endsection
@section('content')
    @php
        $siteId = null;
        $account_code = '';
        $transaction_date = '';
        $total_turnover_amount = '';
        $total_profit_loss = '';
        $current_rate = '';
        $column_name_total = '';
        $turn_over_number = '';
        $column_name_profit_loss = '';
        $profit_loss_number = '';
        $report_data = null;
        $record_calculated = null;

        if(isset($site_account_tran_details)){
          $siteId = $site_account_tran_details['site_account']['SiteId'];
          $account_code = $site_account_tran_details['site_account']['SiteAccountCode'];
          $transaction_date = GeneralFunctions::convertDBdateIntoDatepicker($site_account_tran_details['TransactionDate']);
          $total_turnover_amount = $site_account_tran_details['TotalTurnover'];
          $total_profit_loss = $site_account_tran_details['TotalProfitLoss'];
          $current_rate = $site_account_tran_details['CurrentRate'];
          $column_name_total = $site_account_tran_details['total_turnover_column'];
          $turn_over_number = $site_account_tran_details['total_turn_over_column_number'];
          $column_name_profit_loss = $site_account_tran_details['total_profit_loss_column'];
          $profit_loss_number = $site_account_tran_details['total_profit_loss_column_number'];
          $report_data = $site_account_tran_details['report_data'];
          $record_calculated = $site_account_tran_details['seen'];
        }
    @endphp
    <ol class="breadcrumb">
        <li class="breadcrumb-item"><a href="{{ url('admin/company/dashboard')}}">{{ trans('labels.dashboard') }}</a></li>
        <li class="breadcrumb-item active">{{ trans('labels.add_site_account_transaction') }}</li>
        <!-- Breadcrumb Menu-->
        <li class="breadcrumb-menu d-md-down-none">
            <div class="btn-group" role="group" aria-label="Button group with nested dropdown">
                <a class="btn" href="#"><i class="icon-graph"></i> &nbsp;{{ $balance . ' '. $currencyName }}</a>
            </div>
        </li>
    </ol>

    <div class="container-fluid">
        <div class="row">
            @include('errors.flash_message')
        </div>
        <div class="row">
            <div class="col-lg-12">
                <div class="card">
                    <div class="card-header">
                        <i class="fa fa-align-justify"></i> {{ trans('labels.ass_site_account_info') }}
                    </div>
                    <div class="card-body">
                        <form action="{{url('/admin/company/site/account/transaction/save/record')}}" method="post" class="form-horizontal site_save_form repeater">
                                    <div class="row form-group" id="partner_list" style="display: none">
                                        <div class="col-md-3">Partners</div>
                                        <div class="col-md-7">
                                            <select name="tab_partner" class="form-control">
                                                <option value="">Select Partner</option>
                                                @foreach($partners as $partner)
                                                    <option value="{{ \Illuminate\Support\Facades\Crypt::encryptString($partner->id) }}"> {{ $partner->Username }}</option>
                                                    @endforeach
                                            </select>
                                        </div>
                                    </div>
                                    <div class="form-group row tab-active">
                                        <label class="col-md-3 col-form-label" for="site_name">{{ trans('labels.select_site_name') }}</label>
                                        <div class="col-md-7">
                                            {{ csrf_field() }}
                                            <input type="hidden" name="id" value="{{Request::input('id')}}">
                                            <select class="form-control site_name" name="site_name">
                                                <option value="">{{ trans('labels.choose_option') }}</option>
                                                @foreach($sites as $key => $value)
                                                    <option value="{{$value['Id']}}" {!! $siteId ? 'selected' : ''!!}>{{$value['SiteName']}}</option>
                                                @endforeach
                                            </select>
                                        </div>
                                        <div class="col-md-2">
                                            <img src="{{ url('img/loading.gif') }}" class="loading_gif_drop_down"
                                                 style="height: 26px !important; display: none;">
                                        </div>
                                    </div>
                                    <div class="form-group row tab-active">
                                        <label class="col-md-3 col-form-label" for="site_account_code">{{ trans('labels.site_account_code') }}</label>
                                        <div class="col-md-7">
                                            <select class="form-control site_account_code" name="site_account_code"
                                                    id="site_account_code">
                                                <option value="">{{ trans('labels.choose_option') }}</option>
                                            </select>
                                        </div>
                                        <div class="col-md-2">
                                            <img src="{{ url('img/loading.gif') }}" class="loading_gif_drop_down_2"
                                                 style="height: 26px !important; display: none;">
                                        </div>
                                    </div>
                                    <div data-repeater-list="lst">
                                        <div data-repeater-item>
                                            <div class="form-group row tab-active">
                                                <label class="col-md-3 col-form-label" for="site_account_label_code">{{ trans('labels.transaction_date') }}</label>
                                                <div class="col-md-3">
                                                    <input type="text" id="transaction_date" name="transaction_date" value="{{$transaction_date}}" class="form-control datepicker" required autocomplete="off" autocomplete="nope" />
                                                </div>
                                            </div>
                                            <div class="form-group row tab-active">
                                                <label class="col-md-3 col-form-label" for="remarks_label_color">{{ trans('labels.current_rate') }}</label>
                                                <div class="col-md-3">
                                                    <input type="text" class="form-control current_rate" name="current_rate" value="{{$current_rate}}">
                                                </div>
                                            </div>
                                            <div class="form-group row">
                                                <label class="col-md-3 col-form-label" for="transfer_amount">{{ trans('labels.type_of_entry') }}</label>
                                                <div class="col-md-7">
                                                    <label class="radio-inline"><input class="data_entry_type" style="margin-right: 10px;" type="radio" name="data_entry_type" value="1" {!! (Request::input('id')!= '' && $report_data == null) ? 'checked' : '' !!} {{ ($report_data != null) ? 'disabled' : '' }}>{{ trans('labels.manual_date_entry') }}</label>
                                                    <label class="radio-inline"><input class="data_entry_type" style="margin-right: 10px;" type="radio" name="data_entry_type" value="2" {!! (Request::input('id')!= '' && $report_data != null) ? 'checked' : '' !!} {{ (Request::input('id')!= '' && $report_data == null) ? 'disabled' : '' }}>{{ trans('labels.mapping_date_entry') }}</label>
                                                    <label class="radio-inline"><input class="data_entry_type" style="margin-right: 10px;" type="radio" name="data_entry_type" value="3" {!! (Request::input('id')!= '' && $report_data == null) ? 'disabled' : '' !!}>{{ trans('labels.tabular_entry') }}</label>
                                                </div>
                                            </div>
                                            <div class="mapping_module" style="display: none;">
                                                <div class="form-group row">
                                                    <label class="col-md-3 col-form-label" for="remarks_label_color">{{ trans('labels.mapping_column') }}</label>
                                                    <div class="col-md-8 input-group">
                                                        <span class="input-group-addon total_column">{{ trans('labels.total_turnover') }} = </span>
                                                        <input class="form-control" type="text" name="column_name_total"
                                                               id="column_name_total" value="{{$column_name_total}}">
                                                        <span class="input-group-addon column_number">{{ trans('labels.column_number') }} = </span>
                                                        <input class="form-control" type="number" name="turn_over_number"
                                                               id="turn_over_number" value="{{$turn_over_number}}">
                                                    </div>
                                                </div>
                                                <div class="form-group row">
                                                    <label class="col-md-3 col-form-label" for="remarks_label_color"></label>
                                                    <div class="col-md-8 input-group">
                                                        <span class="input-group-addon total_column">{{ trans('labels.total_profit/loss') }} = </span>
                                                        <input class="form-control" type="text" name="column_name_profit_loss"
                                                               id="column_name_profit_loss"
                                                               value="{{$column_name_profit_loss}}">
                                                        <span class="input-group-addon column_number">{{ trans('labels.column_number') }} = </span>
                                                        <input class="form-control" type="number" name="profit_loss_number"
                                                               id="profit_loss_number" value="{{$profit_loss_number}}">
                                                    </div>
                                                </div>
                                                <div class="form-group row">
                                                    <label class="col-md-3 col-form-label" for="report_data">{{ trans('labels.report_data') }}</label>
                                                    <div class="col-md-6">
                                                    <textarea class="form-control" name="report_data" id="report_data"
                                                              rows="7" cols="50">{!! $report_data !!}</textarea>
                                                    </div>
                                                </div>
                                            </div>
                                            <div class="manual_module" style="display: none;">
                                                <div class="form-group row">
                                                    <label class="col-md-3 col-form-label" for="total_turn_over">{{ trans('labels.total_turnover') }}</label>
                                                    <div class="col-md-3 input-group">
                                                        <input class="form-control" type="text" name="total_turn_over"
                                                               id="total_turn_over" value="{{$total_turnover_amount}}">
                                                    </div>
                                                </div>
                                                <div class="form-group row">
                                                    <label class="col-md-3 col-form-label" for="total_profit_loss">{{ trans('labels.total_profit/loss') }}</label>
                                                    <div class="col-md-3 input-group">
                                                        <input class="form-control" type="text" name="total_profit_loss"
                                                               id="total_profit_loss" value="{{$total_profit_loss}}">
                                                    </div>
                                                </div>
                                            </div>
                                            <div class="tabular-section" style="display: none;">
                                                <div class="row form-group">
                                                    <div class="col-sm-12">
                                                        <table class="table table-bordered table-responsive-lg">
                                                            <thead>
                                                            </thead>
                                                            <tbody>
                                                            </tbody>
                                                        </table>
                                                    </div>
                                                </div>
                                            </div>
                                            <div class="row form-group">
                                                <div class="col-md-3 offset-3">
                                                    <button data-repeater-delete type="button" class="btn btn-sm btn-danger"><i class="fa fa-trash"></i> {{ trans('labels.delete') }}</button>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                    @if(empty(Request::input('id')))
                                        <button data-repeater-create type="button" class="btn btn-sm btn-primary bt-add"><i class="fa fa-plus"></i> {{ trans('labels.add') }}</button>
                                    @endif
                                </form>
                        <br/>
                        @if(isset($site_account_tran_details))
                            @if(isset($record_calculated) && $record_calculated == 0)
                                @if(\App\Helpers\GeneralFunctions::check_edit_permission('site_account_transaction_form'))
                                    <img src="{{ url('img/loading.gif') }}" class="loading_gif"
                                         style="height: 26px !important; display: none;">
                                    <a href="javascript:void(0)" class="btn btn-primary save_record">{{ trans('labels.save') }}</a>
                                @endif
                            @else
                                <div class="alert alert-warning">
                                    <p>{{ trans('labels.sorry_cannot_delete') }}. </p>
                                </div>
                            @endif
                        @elseif(\App\Helpers\GeneralFunctions::check_add_permission('site_account_transaction_form'))
                            <img src="{{ url('img/loading.gif') }}" class="loading_gif"
                                 style="height: 26px !important; display: none;">
                            <a href="javascript:void(0)" class="btn btn-primary save_record">{{ trans('labels.save') }}</a>
                        @endif
                    </div>
                </div>
            </div>
            {{--<div class="col-lg-12">
                @if(isset($site_account_tran_details))
                    @if(isset($record_calculated) && $record_calculated == 0)
                        @if(\App\Helpers\GeneralFunctions::check_edit_permission('site_account_transaction_form'))
                        <img src="{{ url('img/loading.gif') }}" class="loading_gif"
                             style="height: 26px !important; display: none;">
                        <a href="javascript:void(0)" class="btn btn-primary save_record">{{ trans('labels.save') }}</a>
                            @endif
                    @else
                        <div class="alert alert-warning">
                            <p>{{ trans('labels.sorry_cannot_delete') }}. </p>
                        </div>
                    @endif
                @elseif(\App\Helpers\GeneralFunctions::check_add_permission('site_account_transaction_form'))
                    <img src="{{ url('img/loading.gif') }}" class="loading_gif"
                         style="height: 26px !important; display: none;">
                    <a href="javascript:void(0)" class="btn btn-primary save_record">{{ trans('labels.save') }}</a>
                @endif
            </div>--}}
        </div>
    </div>
@endsection
@section('js')
    <script src="{{ asset('vendor/repeater/jquery.repeater.min.js') }}"></script>
    <script src="{{ asset('vendor/bootstrap-datetimepicker/bootstrap-datepicker.min.js') }}"></script>
    <script type="text/javascript">
        $(document).ready(function () {
                    @if(Request::input('id'))
            var req_id = "{{Request::input('id')}}";
                    @else
            var req_id = null;

            @endif

            function get_account_codes() {
                var site_name = $('.site_name').val();
                $('.loading_gif_drop_down').show();
                $.ajax({
                    type: 'GET',
                    url: "{{ url('/admin/company/site/account/transaction/get_account_code') }}",
                    data: {'site_name': site_name, 'req_id': req_id},
                    success: function (data) {
                        if (data.status == 'success') {
                            $('.site_account_code').html(data.result);
                        }
                        $('.loading_gif_drop_down').hide();
                    }
                });
            }

            $(document).on('click', '.save_record', function () {
                $('.loading_gif').show();
                var data = $('.site_save_form').serialize();
                var list = '';
                $.ajax({
                    type: 'POST',
                    url: "{{ url('/admin/company/site/account/transaction/record/validation') }}",
                    data: data,
                    success: function (data) {
                        if (data.status == 'success') {
                            $('.site_save_form').submit()[0];
                        }
                        else {
                            var errorArray = data.msg_data;
                            console.log(errorArray);
                            errorArray.forEach(function (e) {
                                list = list + '<li>' + e + '</li>';
                            });
                            $('#msg-list').html(list);
                            $('.msg-box').addClass("alert-danger").show();
                        }
                        $("html, .container").animate({scrollTop: 0}, 600);
                        $('.loading_gif').hide();
                    }
                });
            });
            /*----------  Subsection for getting current rate of the Account Selected  ----------*/
            $(document).on('change', '.site_account_code', function () {
                $('.loading_gif_drop_down_2').show();
                $.ajax({
                    type: 'GET',
                    url: "{{ url('/admin/company/site/account/transaction/get_current_rate') }}",
                    data: {'id': $(this).val()},
                    success: function (data) {
                        if (data.status == 'success') {
                            //console.log(data);
                            $('.current_rate').val(data.result);
                            }
                        $('.loading_gif_drop_down_2').hide();
                    }
                });
            });
            /*----------  Subsection for checking if Data entry type is Manual or Mapping one  ----------*/
            $(document).on('click', '.data_entry_type', function () {
                $(this).parent().closest('.row').siblings('div.mapping_module').hide();
                $(this).parent().closest('.row').siblings('div.manual_module').hide();
                $(this).parent().closest('.row').siblings('div.tabular-section').hide();
                $('.bt-add').prop('disabled', false);
                $('#transaction_date').parent().closest('.row').show();
                if ($(this).val() == 1)
                {
                    $('#partner_list').hide();
                    $('.tab-active').show();
                    $(this).parent().closest('.row').siblings('div.manual_module').show();
                }
                else if($(this).val() == 2)
                {
                    $('.tab-active').show();
                    $(this).parent().closest('.row').siblings('.mapping_module').show();
                }
                else if($(this).val() == 3)
                {
                    $(this).parent().closest('.row').siblings('div.tabular-section').show();
                    $('.bt-add').prop('disabled', true);
                    $('#transaction_date').parent().closest('.row').hide();
                    $('.tab-active').hide();
                    $('#partner_list').show();
                }
            });
            /*---------Ajax to get site account for partner----------*/
            $(document).on('change', 'select[name="tab_partner"]', function () {
                if($(this)[0].selectedIndex !== 0)
                {
                    $.ajax({
                        url: '{{ route("tabular.data") }}',
                        type: 'GET',
                        data: {id: $(this).val()},
                        success:function (data) {
                            if (data.status == 'success') {
                                var startDate = new Date(data.start_date).getTime();
                                var endDate = new Date(data.end_date).getTime();
                                var thead = '<th>Site Account</th><th>Rate</th>';
                                var count = 0;
                                var tbody = '';
                                $.each(data.site_acc, function (key, value) {
                                    console.log(data.tx_dates[key]);
                                    tbody += '<tr><td>'+ value +'</td>';
                                    tbody += '<td><input type="number" step="any" name="rate['+ key +']" value="'+ data.tx_dates[key]["rate"] +'"  class="form-control"/></td>';
                                    var assignStartDate = new Date(data.tx_dates[key][0]).getTime();
                                    var assignEndDate = (data.tx_dates[key][1] == 0) ? new Date(data.end_date).getTime() : new Date(data.tx_dates[key][1]).getTime();
                                    while (startDate <= endDate)
                                    {
                                        if(count == 0)
                                        {
                                            thead += '<th>'+ new Date(startDate).toDateString() + '</th>';
                                        }
                                        // ensure that only add elements in assignment duration of site account to partner
                                        if(startDate >= assignStartDate && startDate <= assignEndDate)
                                        {
                                            // check if already any transaction on this date does not exist in database
                                            if($.inArray(startDate, data.tx_dates[key]["tx"]) == -1)
                                            {
                                                console.log(startDate);
                                                console.log(data.tx_dates[key]['tx']);
                                                tbody += '<td><input type="number" class="form-control" name="t_over['+ startDate/1000 +']" placeholder="Turnover" /><input type="number" class="form-control" name="t_profit['+ startDate/1000 +']" placeholder="ProfitLoss"/><input type="hidden" name="site_acc['+ startDate/1000 +']" value="'+ key +'"></td>';
                                            }
                                            else
                                            {
                                                tbody += '<td></td>';
                                            }
                                        }
                                        else
                                        {
                                            tbody += '<td></td>';
                                        }
                                        // adding 24 hours to startDate
                                        startDate = startDate + 24 * 60 * 60 * 1000;
                                    }
                                    tbody += '</tr>';
                                    startDate = new Date(data.start_date).getTime();
                                    count++;
                                })
                                $('thead').html(thead);
                                $('tbody').html(tbody);
                            }
                        }
                    });
                }
            });


            @if(Request::input('id')!= '')
            @if($report_data != null)
            $('.mapping_module').show();
            @else
            $('.manual_module').show();
            @endif
            @endif

            $(document).on('change', '.site_name', function () {
                get_account_codes();
            });
            get_account_codes();

            // repeater //
            $('.repeater').repeater({
                isFirstItemUndeletable: true,
                show:function () {
                    $(this).slideDown();
                    $('.datepicker').datepicker({
                        format: 'yyyy/mm/dd',
                    });
                }
            });
        });
    </script>
@endsection
