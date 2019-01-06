@extends( Auth::user()->Roles == 2 ? 'company_portal.layouts.app' :  (Auth::user()->Roles == 1 ? 'super_admin_portal.layouts.app' : 'partner_portal.layout.app'))
@section('css')
    <style type="text/css">
        .fund_transfer label {
            padding-left: 20px !important;
        }

        .fund_transfer label input[type="radio"] {
            margin: 10px !important;
        }

        .deposite_section {
            display: none;
        }

        .withdraw_section {
            display: none;
        }

        .p2p_section {
            display: none;
        }

        .amount_options {
            margin-right: 10px;
        }

        .modal-content {
            border: 5px solid rgba(146, 28, 28, 0.2) !important;
        }

        .modal-header {
            background-color: #deb17c !important;
        }
    </style>
@endsection
@section('content')
    @php
        $amount = '';
        if(isset($edit_version)){
          $amount = $transaction_details['Amount'];
        }
    @endphp
    <ol class="breadcrumb">
        <li class="breadcrumb-item"><a href="{{ url('admin/company/dashboard')}}">{{ trans('labels.dashboard') }}</a></li>
        <li class="breadcrumb-item active">{{ trans('labels.transfer_funds') }}</li>
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
        @if(isset($edit_version))
            <div class="row">
                <div class="col-lg-12">
                    <div class="card-header">
                        <i class="fa fa-align-justify"></i> Transactions List
                    </div>
                    <div class="card-body">
                        <div class="card">
                            <div class="card-header">
                            </div>
                            <div class="card-body">
                                <table class="table table-responsive-sm table-bordered table-striped table-sm">
                                    <thead>
                                    <tr>
                                        <th>Source</th>
                                        <th>Remark</th>
                                        <th>In Currecncies</th>
                                        <th>Amount in Currencies</th>
                                        <th>Amount in Base Currency</th>
                                        <th>Transaction Date</th>
                                    </tr>
                                    </thead>
                                    <tbody>
                                    @foreach($responseResult as $key => $value)
                                        <tr>
                                            <td>{{$value['source']}}</td>
                                            <td>{{$value['remarks']}}</td>
                                            <td>{{$value['in_currency']}}</td>
                                            <td>{{$value['amount_in_different_currencies']}}</td>
                                            <td>{{$value['amount_in_base_currency']}}</td>
                                            <td>{!! GeneralFunctions::convertToDateTimeToString($value['created_at']) !!}</td>
                                        </tr>
                                    @endforeach
                                    </tbody>
                                </table>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        @endif

        <div class="row">
            <div class="col-lg-12">
                <div class="card-header">
                    <i class="fa fa-align-justify"></i> {{ trans('labels.select_option_for_account') }}
                </div>
                <div class="card-body">
                    <div class="card">
                        <div class="card-header">
                        </div>
                        <div class="card-body">
                            <form class="fund_transfer" autocomplete="off">
                                <label class="radio-inline">
                                    @if(isset($transaction_details['transfer_type']))
                                        <input class="fund_transfer_class" type="radio" name="optradio"
                                               value="1" {!! isset($transaction_details) ? (($transaction_details['AccountStatus'] == 2 && $transaction_details['transfer_type'] == 1) ? 'checked' : 'disabled') : '' !!}>{{ trans('labels.deposit') }}
                                    @else
                                        <input class="fund_transfer_class" type="radio" name="optradio"
                                               value="1" {!! isset($transaction_details) ? ($transaction_details['AccountStatus'] == 2 ? 'checked' : 'disabled') : '' !!}>{{ trans('labels.deposit') }}
                                    @endif
                                </label>
                                <label class="radio-inline">
                                    @if(isset($transaction_details['transfer_type']))
                                        <input class="fund_transfer_class" type="radio" name="optradio"
                                               value="2" {!! isset($transaction_details) ? (($transaction_details['AccountStatus'] == 1 && $transaction_details['transfer_type'] == 1) ? 'checked' : 'disabled') : '' !!}>{{ trans('labels.withdraw') }}
                                    @else
                                        <input class="fund_transfer_class" type="radio" name="optradio"
                                               value="2" {!! isset($transaction_details) ? ($transaction_details['AccountStatus'] == 1 ? 'checked' : 'disabled') : '' !!}>{{ trans('labels.withdraw') }}
                                    @endif
                                </label>
                                <label class="radio-inline">
                                    <input class="fund_transfer_class" type="radio" name="optradio"
                                           value="3" {!! isset($transaction_details) ? (isset($transaction_details['transfer_type']) ? ($transaction_details['transfer_type'] == 2 ? 'checked' : 'disabled')  : 'disabled') : '' !!}>{{ trans('labels.partner_to_partner') }}
                                </label>
                                <label class="radio-inline"> <img src="{{ url('img/loading.gif') }}" class="loading_gif"
                                                                  style="height: 26px !important; display: none;"></label>
                            </form>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <div class="row">
            <!-- Section for Deposit Form -->
            <div class="col-lg-12 deposite_section">
                <div class="card">
                    <div class="card-header">
                        <i class="fa fa-align-justify"></i> {{ trans('labels.amount_deposit') }}
                    </div>
                    <div class="card-body">
                        <div class="card">
                            <div class="card-header">
                            </div>
                            <div class="card-body">
                                <form class="form-horizontal amount_deposit" autocomplete="off">
                                    <div class="form-group row">
                                        <label class="col-md-3 col-form-label"
                                               for="account_balance">{{ trans('labels.your_account_balance') }}</label>
                                        <div class="col-md-7">
                                            <p class="account_balance"></p>
                                        </div>
                                    </div>
                                    <div class="form-group row">
                                        <label class="col-md-3 col-form-label"
                                               for="account_base_currency">{{ trans('labels.currency') }}</label>
                                        <div class="col-md-7">
                                            <p class="account_base_currency"></p>
                                        </div>
                                    </div>
                                    <div class="form-group row">
                                        <label class="col-md-3 col-form-label"
                                               for="partner_account">{{ trans('labels.account_for_deposit') }}</label>
                                        <div class="col-md-7">
                                            {{ csrf_field() }}
                                            <input type="hidden" name="transfer_type" value="1">
                                            <select class="form-control partner_account" name="partner_account">
                                                <option value="">{{ trans('labels.choose_partners_account') }}</option>
                                                @foreach($partners_account as $key => $value)
                                                    <option value="{{$value['id']}}">{{$value['Username']}}</option>
                                                @endforeach
                                            </select>
                                        </div>
                                    </div>
                                    @if(isset($edit_version))
                                        <div class="form-group row">
                                            <label class="col-md-3 col-form-label" for="transfer_amount"></label>
                                            <div class="col-md-7">
                                                <label class="radio-inline"><input class="amount_options" type="radio"
                                                                                   name="settlement_type"
                                                                                   value="1">{{ trans('labels.settle_amount_funds') }}
                                                </label>
                                                <label class="radio-inline"><input class="amount_options" type="radio"
                                                                                   name="settlement_type"
                                                                                   value="2">{{ trans('labels.settle_amount_remove') }}
                                                </label>
                                            </div>
                                        </div>
                                    @endif
                                    <div class="form-group row">
                                        <label class="col-md-3 col-form-label"
                                               for="transfer_amount">{{ trans('labels.amount') }}</label>
                                        <div class="col-md-7 input-group">
                                            <span class="input-group-addon settle_symbol"></span>
                                            <input type="text" name="transfer_amount"
                                                   class="form-control deposit_amount">
                                            <span class="input-group-addon transfer_in_which_currency"></span>
                                        </div>
                                    </div>
                                    <div class="form-group row">
                                        <label class="col-md-3 col-form-label"
                                               for="password">{{ trans('labels.password') }}</label>
                                        <div class="col-md-7 input-group">
                                            <span class="input-group-addon"><i class="fa fa-lock"></i></span>
                                            <input style="display:none">
                                            <input type="password" name="password" class="form-control password"
                                                   autocomplete="off">
                                        </div>
                                    </div>
                                    @if(isset($edit_version))
                                        <div class="form-group row">
                                            <label class="col-md-3 col-form-label"
                                                   for="transfer_amount">{{ trans('labels.add_updating_remark') }}</label>
                                            <div class="col-md-7">
                                                <textarea class="form-control transfer_remark" id="transfer_remark"
                                                          name="transfer_remark"></textarea>
                                                <input type="hidden" class="transfer_edit_transaction"
                                                       name="transfer_edit_transaction"
                                                       value="{{$transaction_details['id']}}">
                                            </div>
                                        </div>
                                    @endif
                                    <div class="form-group row">
                                        <label class="col-md-3 col-form-label"></label>
                                        <div class="col-md-7">
                                            @if(GeneralFunctions::check_add_permission('send_cash_balance'))
                                                <a href="javascript:void(0)"
                                                   class="btn btn-primary save_deposit_record">{{ trans('labels.save') }}</a>
                                                <div class="col-md-2">
                                                    <img src="{{ url('img/loading.gif') }}" class="loading_gif_deposit"
                                                         style="height: 26px !important; display: none;">
                                                </div>
                                            @endif
                                        </div>
                                    </div>
                                </form>
                            </div>
                            <div class="card-footer">
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            <!-- End Section for Deposit Form -->

            <!-- Section for Withdraw Form -->
            <div class="col-lg-12 withdraw_section">
                <div class="card">
                    <div class="card-header">
                        <i class="fa fa-align-justify"></i> {{ trans('labels.amount_withdraw') }}
                    </div>
                    <div class="card-body">
                        <div class="card">
                            <div class="card-header">
                            </div>
                            <div class="card-body">
                                <form class="form-horizontal amount_withdraw" autocomplete="off">
                                    <div class="form-group row">
                                        <label class="col-md-3 col-form-label"
                                               for="wh_account_balance">{{ trans('labels.account_holder_balance') }}</label>
                                        <div class="col-md-7">
                                            <p class="wh_account_balance"></p>
                                        </div>
                                    </div>
                                    <div class="form-group row">
                                        <label class="col-md-3 col-form-label"
                                               for="wh_account_base_currency">{{ trans('labels.account_holder_currency') }}</label>
                                        <div class="col-md-7">
                                            <p class="wh_account_base_currency"></p>
                                        </div>
                                    </div>
                                    <div class="form-group row">
                                        <label class="col-md-3 col-form-label"
                                               for="wh_partner_account">{{ trans('labels.withdraw_from_account') }}</label>
                                        <div class="col-md-7">
                                            {{ csrf_field() }}
                                            <input type="hidden" name="transfer_type" value="2">
                                            <select class="form-control wh_partner_account" name="wh_partner_account">
                                                <option value="">{{ trans('labels.choose_partners_account') }}</option>
                                                @foreach($partners_account as $key => $value)
                                                    <option value="{{$value['id']}}">{{$value['Username']}}</option>
                                                @endforeach
                                            </select>
                                        </div>
                                        <div class="col-md-2">
                                            <img src="{{ url('img/loading.gif') }}" class="loading_gif_wh_partner"
                                                 style="height: 26px !important; display: none;">
                                        </div>
                                    </div>
                                    @if(isset($edit_version))
                                        <div class="form-group row">
                                            <label class="col-md-3 col-form-label" for="transfer_amount"></label>
                                            <div class="col-md-7">
                                                <label class="radio-inline"><input class="amount_options" type="radio"
                                                                                   name="settlement_type"
                                                                                   value="1">{{ trans('labels.settle_amount_funds') }}
                                                </label>
                                                <label class="radio-inline"><input class="amount_options" type="radio"
                                                                                   name="settlement_type"
                                                                                   value="2">{{ trans('labels.settle_amount_remove') }}
                                                </label>
                                            </div>
                                        </div>
                                    @endif
                                    <div class="form-group row">
                                        <label class="col-md-3 col-form-label"
                                               for="transfer_amount">{{ trans('labels.amount') }}</label>
                                        <div class="col-md-7 input-group">
                                            <span class="input-group-addon settle_symbol"></span>
                                            <input type="text" name="wh_transfer_amount"
                                                   class="form-control withdraw_amount">
                                            <span class="input-group-addon withdraw_in_which_currency"
                                                  autocomplete="off"></span>
                                        </div>
                                    </div>
                                    <div class="form-group row">
                                        <label class="col-md-3 col-form-label"
                                               for="password">{{ trans('labels.password') }}</label>
                                        <div class="col-md-7 input-group">
                                            <span class="input-group-addon"><i class="fa fa-lock"></i></span>
                                            <input style="display:none">
                                            <input type="password" name="password" class="form-control password"
                                                   autocomplete="off">
                                        </div>
                                    </div>
                                    @if(isset($edit_version))
                                        <div class="form-group row">
                                            <label class="col-md-3 col-form-label"
                                                   for="transfer_amount">{{ trans('labels.add_updating_remark') }}</label>
                                            <div class="col-md-7">
                                                <textarea class="form-control wh_remark" id="wh_remark"
                                                          name="wh_remark"></textarea>
                                                <input type="hidden" class="wh_edit_transaction"
                                                       name="wh_edit_transaction"
                                                       value="{{$transaction_details['id']}}">
                                            </div>
                                        </div>
                                    @endif
                                    <div class="form-group row">
                                        <label class="col-md-3 col-form-label"></label>
                                        <div class="col-md-7">
                                            @if(GeneralFunctions::check_add_permission('send_cash_balance'))
                                                <a href="javascript:void(0)"
                                                   class="btn btn-primary save_withdraw_record">{{ trans('labels.save') }}</a>
                                                <div class="col-md-2">
                                                    <img src="{{ url('img/loading.gif') }}" class="loading_gif_withdraw"
                                                         style="height: 26px !important; display: none;">
                                                </div>
                                            @endif
                                        </div>
                                    </div>
                                </form>
                            </div>
                            <div class="card-footer">
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            <!-- End Section for Withdraw Form -->

            <!-- Section for Partner to Partner Form -->
            <div class="col-lg-12 p2p_section">
                <div class="card">
                    <div class="card-header">
                        <i class="fa fa-align-justify"></i> {{ trans('labels.P2P') }}
                    </div>
                    <div class="card-body">
                        <div class="card">
                            <div class="card-header">
                            </div>
                            <div class="card-body">
                                <form class="form-horizontal p2p_form">
                                    <div class="form-group row">
                                        <label class="col-md-3 col-form-label"
                                               for="p2p_account_balance">{{ trans('labels.account_holder_balance') }}</label>
                                        <div class="col-md-7">
                                            <p class="p2p_account_balance"></p>
                                        </div>
                                    </div>
                                    <div class="form-group row">
                                        <label class="col-md-3 col-form-label"
                                               for="p2p_account_base_currency">{{ trans('labels.account_holder_currency') }}</label>
                                        <div class="col-md-7">
                                            <p class="p2p_account_base_currency"></p>
                                        </div>
                                    </div>
                                    <div class="form-group row">
                                        <label class="col-md-3 col-form-label"
                                               for="p2p_partner_account">{{ trans('labels.transfer_from_amount') }}</label>
                                        <div class="col-md-7">
                                            {{ csrf_field() }}
                                            <select class="form-control p2p_partner_account" name="p2p_partner_account">
                                                <option value="">Choose Partners Account</option>
                                                @foreach($partners_account as $key => $value)
                                                    <option value="{{$value['id']}}">{{$value['Username']}}</option>
                                                @endforeach
                                            </select>
                                        </div>
                                        <div class="col-md-2">
                                            <img src="{{ url('img/loading.gif') }}" class="loading_gif_p2p_partner"
                                                 style="height: 26px !important; display: none;">
                                        </div>
                                    </div>
                                    <div class="form-group row">
                                        <label class="col-md-3 col-form-label"
                                               for="p2p_to_partner_account">{{ trans('labels.deposit_in_account') }}</label>
                                        <div class="col-md-7">
                                            <input type="hidden" name="transfer_type" value="3">
                                            <select class="form-control p2p_to_partner_account"
                                                    name="p2p_to_partner_account">
                                                <option value="">{{ trans('labels.choose_partners_account') }}</option>
                                                @foreach($partners_account as $key => $value)
                                                    <option value="{{$value['id']}}">{{$value['Username']}}</option>
                                                @endforeach
                                            </select>
                                        </div>
                                    </div>
                                    <div class="form-group row">
                                        <label class="col-md-3 col-form-label"
                                               for="password">{{ trans('labels.password') }}</label>
                                        <div class="col-md-7 input-group">
                                            <span class="input-group-addon"><i class="fa fa-lock"></i></span>
                                            <input style="display:none">
                                            <input type="password" name="password" class="form-control password"
                                                   autocomplete="off">
                                        </div>
                                    </div>
                                    <div class="form-group row">
                                        <label class="col-md-3 col-form-label"
                                               for="p2p_transfer_amount">{{ trans('labels.amount') }}</label>
                                        <div class="col-md-7 input-group">
                                            <input type="text" name="p2p_transfer_amount"
                                                   class="form-control p2p_transfer_amount">
                                            <span class="input-group-addon p2p_in_which_currency"></span>
                                        </div>
                                    </div>
                                    <div class="form-group row">
                                        <label class="col-md-3 col-form-label"></label>
                                        <div class="col-md-7">
                                            @if(GeneralFunctions::check_add_permission('send_cash_balance'))
                                                <a href="javascript:void(0)"
                                                   class="btn btn-primary save_p2p_record">{{ trans('labels.save') }}</a>
                                                <div class="col-md-2">
                                                    <img src="{{ url('img/loading.gif') }}" class="loading_gif_p2p"
                                                         style="height: 26px !important; display: none;">
                                                </div>
                                            @endif
                                        </div>
                                    </div>
                                </form>
                            </div>
                            <div class="card-footer">
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            <!-- End Section for Partner to Partner Form -->
        </div>
    </div>

    <!-- Modal Box for Positive Fund Transfer -->
    <!-- Modal Box For Showing the Convertion of Fund Transfer and Permission to Proceed Transaction-->
    <div class="modal fade confirmation_deposit_funds" id="confirmation_deposit_funds" role="dialog">
        <div class="modal-dialog modal-lg">

            <!-- Modal content-->
            <div class="modal-content">
                <div class="modal-header">
                    <h4 class="modal-title">{{ trans('labels.confirm_of_fund_transfer') }}</h4>
                    <button type="button" class="close" data-dismiss="modal">&times;</button>
                </div>
                <div class="modal-body">
                    <div class="container">
                        <div class="col-xs-12">
                            <div class="invoice-title">
                                <h2>{{ trans('labels.transaction_details') }}</h2>
                            </div>
                            <hr>
                            <div class="row">
                                <div class="col-md-6">
                                    <address>
                                        <strong>{{ trans('labels.from_account_username') }}</strong>
                                        <p class="source_user_name"></p>
                                    </address>
                                </div>
                                <div class="col-xs-6 text-right">
                                    <address>
                                        <strong>{{ trans('labels.deposited_account_username') }}</strong>
                                        <p class="target_user_name"></p>
                                    </address>
                                </div>
                            </div>
                        </div>
                        <div class="row">
                            <div class="col-md-12">
                                <div class="panel panel-default">
                                    <div class="panel-heading">
                                        <h3 class="panel-title">
                                            <strong>{{ trans('labels.transaction_summery') }}</strong></h3>
                                    </div>
                                    @if(isset($edit_version))
                                        <div class="alert alert-info">
                                            {{ trans('labels.update_transaction_account_mode') }}
                                        </div>
                                    @endif
                                    <div class="panel-body">
                                        <div class="table-responsive">
                                            <table class="table table-condensed">
                                                <thead>
                                                <tr>
                                                    <td><strong>{{ trans('labels.from_currency') }}</strong></td>
                                                    <td><strong>{{ trans('labels.from_currency_rate') }}</strong></td>
                                                    <td><strong>{{ trans('labels.transfer_amount') }}</strong></td>
                                                    <td><strong>{{ trans('labels.to_currency') }}</strong></td>
                                                    <td class="text-center">
                                                        <strong>{{ trans('labels.currency_rate') }}</strong></td>
                                                    <td class="text-center">
                                                        <strong>{{ trans('labels.total_transfer_amount') }}</strong>
                                                    </td>
                                                </tr>
                                                </thead>
                                                <tbody>
                                                <!-- foreach ($order->lineItems as $line) or some such thing here -->
                                                <tr>
                                                    <td class="text-center from_currency"></td>
                                                    <td class="text-center from_currency_rate"></td>
                                                    <td class="text-center transfer_amount"></td>
                                                    <td class="text-center to_currency"></td>
                                                    <td class="text-right currency_rate"></td>
                                                    <td class="text-right total_transfer"></td>
                                                </tr>
                                                </tbody>
                                            </table>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="modal-footer">
                    <form class="transfer_account_details_form" name="transfer_account_details_form"
                          id="transfer_account_details_form" method="POST"
                          action="{{ url('/admin/company/transfer/funds') }}">
                        {{ csrf_field() }}
                        @if(isset($edit_version))
                            <input type="hidden" name="settle_account_type" class="settle_account_type">
                            <input type="hidden" name="wh_remark" class="full_remark">
                            <input type="hidden" name="edit_version" value="1">
                            <input type="hidden" name="correlation_id" value="{{$responseResult[0]['CorrelationId']}}">
                        @endif
                        <input type="hidden" name="source_id" class="fund_transfer_source_id">
                        <input type="hidden" name="target_id" class="fund_transfer_target_id">
                        <input type="hidden" name="source_currency_id" class="fund_transfer_source_currency_id">
                        <input type="hidden" name="source_currency_rate" class="fund_transfer_source_currency_rate">
                        <input type="hidden" name="target_currency_id" class="fund_transfer_target_currency_id">
                        <input type="hidden" name="source_amount" class="fund_transfer_source_amount">
                        <input type="hidden" name="target_currency_rate" class="fund_transfer_target_currency_rate">
                        <input type="hidden" name="target_amount" class="fund_transfer_target_amount">
                        <input type="hidden" name="transfer_type" class="fund_transfer_transfer_type">
                        <button type="submit" class="btn btn-primary">{{ trans('labels.send') }}</button>
                        <button type="button" class="btn btn-default"
                                data-dismiss="modal">{{ trans('labels.close') }}</button>
                    </form>
                </div>
            </div>

        </div>
    </div>
    <!-- End Modal Box For Showing -->

    <!-- Modal Box for Showing the Convertion for Negative Transactions (Specifically Used in Edit Mode) -->
    <div class="modal fade confirmation_reverse_funds" id="confirmation_reverse_funds" role="dialog">
        <div class="modal-dialog modal-lg">

            <!-- Modal content-->
            <div class="modal-content">
                <div class="modal-header">
                    <h4 class="modal-title">{{ trans('labels.confirm_of_fund_transfer') }}</h4>
                    <button type="button" class="close" data-dismiss="modal">&times;</button>
                </div>
                <div class="modal-body">
                    <div class="container">
                        <div class="col-xs-12">
                            <div class="invoice-title">
                                <h2>{{ trans('labels.transaction_details') }}</h2>
                            </div>
                            <hr>
                            <div class="row">
                                <div class="col-md-6">
                                    <address>
                                        <strong>{{ trans('labels.from_account_username') }}</strong>
                                        <p class="target_user_name"></p>
                                    </address>
                                </div>
                                <div class="col-xs-6 text-right">
                                    <address>
                                        <strong>{{ trans('labels.deposited_account_username') }}</strong>
                                        <p class="source_user_name"></p>
                                    </address>
                                </div>
                            </div>
                        </div>
                        <div class="row">
                            <div class="col-md-12">
                                <div class="panel panel-default">
                                    <div class="panel-heading">
                                        <h3 class="panel-title">
                                            <strong>{{ trans('labels.transaction_summery') }}</strong></h3>
                                    </div>
                                    @if(isset($edit_version))
                                        <div class="alert alert-info">
                                            {{ trans('labels.reversing_transaction_mode') }}
                                        </div>
                                    @endif
                                    <div class="panel-body">
                                        <div class="table-responsive">
                                            <table class="table table-condensed">
                                                <thead>
                                                <tr>
                                                    <td><strong>{{ trans('labels.from_currency') }}</strong></td>
                                                    <td><strong>{{ trans('labels.from_currency_rate') }}</strong></td>
                                                    <td><strong>{{ trans('labels.transfer_amount') }}</strong></td>
                                                    <td><strong>{{ trans('labels.to_currency') }}</strong></td>
                                                    <td class="text-center">
                                                        <strong>{{ trans('labels.currency_rate') }}</strong></td>
                                                    <td class="text-center">
                                                        <strong>{{ trans('labels.total_transfer_amount') }}</strong>
                                                    </td>
                                                </tr>
                                                </thead>
                                                <tbody>
                                                <!-- foreach ($order->lineItems as $line) or some such thing here -->
                                                <tr>
                                                    <td class="text-center to_currency"></td>
                                                    <td class="text-right currency_rate"></td>
                                                    <td class="text-right total_transfer"></td>
                                                    <td class="text-center from_currency"></td>
                                                    <td class="text-center from_currency_rate"></td>
                                                    <td class="text-center transfer_amount"></td>
                                                </tr>
                                                </tbody>
                                            </table>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="modal-footer">
                    <form class="reverse_account_details_form" name="reverse_account_details_form"
                          id="reverse_account_details_form" method="POST"
                          action="{{ url('/admin/company/transfer/funds') }}">
                        {{ csrf_field() }}
                        @if(isset($edit_version))
                            <input type="hidden" name="settle_account_type" class="settle_account_type">
                            <input type="hidden" name="wh_remark" class="full_remark">
                            <input type="hidden" name="edit_version" value="1">
                            <input type="hidden" name="correlation_id" value="{{$responseResult[0]['CorrelationId']}}">
                        @endif
                        <input type="hidden" name="source_id" class="fund_transfer_source_id">
                        <input type="hidden" name="target_id" class="fund_transfer_target_id">
                        <input type="hidden" name="source_currency_id" class="fund_transfer_source_currency_id">
                        <input type="hidden" name="source_currency_rate" class="fund_transfer_source_currency_rate">
                        <input type="hidden" name="target_currency_id" class="fund_transfer_target_currency_id">
                        <input type="hidden" name="source_amount" class="fund_transfer_source_amount">
                        <input type="hidden" name="target_amount" class="fund_transfer_target_amount">
                        <input type="hidden" name="target_currency_rate" class="fund_transfer_target_currency_rate">
                        <input type="hidden" name="transfer_type" class="fund_transfer_transfer_type">
                        <button type="submit" class="btn btn-primary">{{ trans('labels.send') }}</button>
                        <button type="button" class="btn btn-default"
                                data-dismiss="modal">{{ trans('labels.close') }}</button>
                    </form>
                </div>
            </div>

        </div>
    </div>
    <!-- End of Modal Box Showing -->
@endsection
@section('js')
    <script type="text/javascript">
        $(document).ready(function () {
            function transfer_in_partners_account(id) {
                $.ajax({
                    type: 'GET',
                    url: "{{url('/admin/company/get/tenant/amount/currency')}}",
                    data: {'partner_id': id},
                    success: function (data) {
                        if (data.status == 'success') {
                            $('.account_balance').html(Number(parseFloat(data.account_balance).toFixed(2)).toLocaleString('en') + '/- ' + data.base_currency);
                            $('.account_base_currency').html(data.base_currency);
                            $('.transfer_in_which_currency').html('Please Quote Amount in Account Holder Currency ' + data.base_currency);
                            $('.deposite_section').show();
                        }
                        else {

                        }
                        $("html, .container").animate({scrollTop: 0}, 600);
                        $('.loading_gif').hide();
                    }
                });
            }

            $(document).on('change', '.fund_transfer_class', function () {
                if ($(this).val() == 1) {
                    $('.withdraw_section').hide();
                    $('.p2p_section').hide();
                    $('.loading_gif').show();
                    transfer_in_partners_account($(this).val());
                }
                if ($(this).val() == 2) {
                    $('.deposite_section').hide();
                    $('.p2p_section').hide();
                    $('.withdraw_section').show();
                }
                if ($(this).val() == 3) {
                    $('.deposite_section').hide();
                    $('.withdraw_section').hide();
                    $('.p2p_section').show();
                }
            });
            $(document).on('click', '.save_deposit_record', function () {
                var deposit_form_data = $('.amount_deposit').serialize();
                $('.loading_gif_deposit').show();
                var list = '';
                $.ajax({
                    type: 'POST',
                    url: "{{url('/admin/company/deposit/amount')}}",
                    data: deposit_form_data,
                    success: function (data) {
                        if (data.status == 'success') {
                            $('.source_user_name').html(data.result.tenant_detail.Username);
                            $('.target_user_name').html(data.result.partners_detail.Username);
                            $('.from_currency').html(data.result.from_currency);
                            $('.from_currency_rate').html(data.result.from_currency_rate);
                            $('.to_currency').html(data.result.to_currency);
                            $('.transfer_amount').html(Math.abs(data.result.transfer_amount));
                            $('.currency_rate').html(data.result.current_rate);
                            $('.total_transfer').html(Math.abs(Number(parseFloat(data.result.converted_transfer_amount_in_partners_account).toFixed(2)).toLocaleString('en')));
                            /*----------  Adding Transaction Details in Form for Submission  ----------*/
                            $('.fund_transfer_source_currency_id').val(data.result.from_currency_id);
                            $('.fund_transfer_target_currency_id').val(data.result.to_currency_id);
                            $('.fund_transfer_source_currency_rate').val(data.result.from_currency_rate);
                            $('.fund_transfer_target_currency_rate').val(data.result.current_rate);
                            $('.fund_transfer_source_amount').val(data.result.transfer_amount);
                            $('.fund_transfer_target_amount').val(data.result.converted_transfer_amount_in_partners_account);
                            $('.fund_transfer_source_id').val(data.result.tenant_detail.id);
                            $('.fund_transfer_target_id').val(data.result.partners_detail.id);
                            $('.fund_transfer_transfer_type').val(1);
                            $('.full_remark').val(data.result.transfer_remark);
                            if ($('.settle_account_type').val() == 2) {
                                $('.confirmation_reverse_funds').modal('show');
                            }
                            else {
                                $('.confirmation_deposit_funds').modal('show');
                            }
                        }
                        else {
                            var errorArray = data.msg_data;
                            errorArray.forEach(function (e) {
                                list = list + '<li>' + e + '</li>';
                            });

                            $('#msg-list').html(list);
                            $('.msg-box').addClass("alert-danger").show();
                        }
                        $("html, .container").animate({scrollTop: 0}, 600);
                        $('.loading_gif_deposit').hide();
                    }
                });
            });

            /**
             *
             * Section for the Withdraw Functionality (Get Partner Account Details)
             *
             */
            function withdraw_partners_account(partners_id) {
                $.ajax({
                    type: 'GET',
                    url: "{{url('/admin/company/get/partner/amount/currency')}}",
                    data: {'partner_id': partners_id},
                    success: function (data) {
                        if (data.status == 'success') {
                            $('.wh_account_balance').html(Number(parseFloat(data.result.partnerBalance).toFixed(2)).toLocaleString('en'));
                            $('.wh_account_base_currency').html(data.result.partnerBaseCurrency);
                            $('.withdraw_in_which_currency').html('Please Quote Amount in Account Holder Currency ' + data.result.partnerBaseCurrency);
                        }
                    }
                });
            }

            $(document).on('change', '.wh_partner_account', function () {
                $('.loading_gif_wh_partner').show();
                if ($(this).val() != '') {
                    withdraw_partners_account($(this).val());
                    $('.loading_gif_wh_partner').hide();
                }
                else {
                    $('.wh_account_balance').html('');
                    $('.wh_account_base_currency').html('');
                    $('.withdraw_amount').val('');
                    $('.loading_gif_wh_partner').hide();
                }
            });

            $(document).on('click', '.save_withdraw_record', function () {
                var withdraw_form_data = $('.amount_withdraw').serialize();
                $('.loading_gif_withdraw').show();
                var list = '';
                $.ajax({
                    type: 'POST',
                    url: "{{url('/admin/company/withdraw/amount')}}",
                    data: withdraw_form_data,
                    success: function (data) {
                        if (data.status == 'success') {
                            $('.source_user_name').html(data.result.partners_detail.Username);
                            $('.target_user_name').html(data.result.tenant_detail.Username);
                            $('.from_currency').html(data.result.from_currency);
                            $('.from_currency_rate').html(data.result.from_currency_rate);
                            $('.to_currency').html(data.result.to_currency);
                            $('.transfer_amount').html(Math.abs(data.result.transfer_amount));
                            $('.currency_rate').html(data.result.current_rate);
                            $('.total_transfer').html(Math.abs(Number(parseFloat(data.result.converted_transfer_amount_in_tenant_account).toFixed(2)).toLocaleString('en')));
                            /*----------  Adding Transaction Details in Form for Submission  ----------*/
                            $('.fund_transfer_source_currency_id').val(data.result.from_currency_id);
                            $('.fund_transfer_target_currency_id').val(data.result.to_currency_id);
                            $('.fund_transfer_source_currency_rate').val(data.result.from_currency_rate);
                            $('.fund_transfer_target_currency_rate').val(data.result.current_rate);
                            $('.fund_transfer_source_amount').val(data.result.transfer_amount);
                            $('.fund_transfer_target_amount').val(data.result.converted_transfer_amount_in_tenant_account);
                            $('.fund_transfer_source_id').val(data.result.partners_detail.id);
                            $('.fund_transfer_target_id').val(data.result.tenant_detail.id);
                            $('.fund_transfer_transfer_type').val(2);
                            $('.full_remark').val(data.result.wh_remark);
                            if ($('.settle_account_type').val() == 2) {
                                $('.confirmation_reverse_funds').modal('show');
                            }
                            else {
                                $('.confirmation_deposit_funds').modal('show');
                            }
                            $('.loading_gif_withdraw').hide();
                        }
                        else {
                            var errorArray = data.msg_data;
                            errorArray.forEach(function (e) {
                                list = list + '<li>' + e + '</li>';
                            });

                            $('#msg-list').html(list);
                            $('.msg-box').addClass("alert-danger").show();
                        }
                        $("html, .container").animate({scrollTop: 0}, 600);
                        $('.loading_gif_deposit').hide();
                    }
                });
            });
            /**
             *
             * Section for the Withdraw Functionality (Get Partner Account Details)
             *
             */
            $(document).on('change', '.p2p_partner_account', function () {
                $('.loading_gif_p2p_partner').show();
                if ($(this).val() != '') {
                    $.ajax({
                        type: 'GET',
                        url: "{{url('/admin/company/get/partner/amount/currency')}}",
                        data: {'partner_id': $(this).val()},
                        success: function (data) {
                            if (data.status == 'success') {
                                $('.p2p_account_balance').html(Number(parseFloat(data.result.partnerBalance).toFixed(2)).toLocaleString('en'));
                                $('.p2p_account_base_currency').html(data.result.partnerBaseCurrency);
                                $('.p2p_in_which_currency').html('Please Quote Amount in Account Holder Currency ' + data.result.partnerBaseCurrency);
                            }
                            $('.loading_gif_p2p_partner').hide();
                        }
                    });
                }
                else {
                    $('.p2p_account_balance').html('');
                    $('.p2p_account_base_currency').html('');
                    $('.p2p_transfer_amount').val('');
                    $('.loading_gif_p2p_partner').hide();
                }
            });

            $(document).on('click', '.save_p2p_record', function () {
                var p2p_form_data = $('.p2p_form').serialize();
                $('.loading_gif_withdraw').show();
                var list = '';
                $.ajax({
                    type: 'POST',
                    url: "{{url('/admin/company/p2p/transfer/amount')}}",
                    data: p2p_form_data,
                    success: function (data) {
                        if (data.status == 'success') {
                            $('.source_user_name').html(data.result.source_partners_detail.Username);
                            $('.target_user_name').html(data.result.target_partners_detail.Username);
                            $('.from_currency').html(data.result.from_currency);
                            $('.from_currency_rate').html(data.result.from_currency_rate);
                            $('.to_currency').html(data.result.to_currency);
                            $('.transfer_amount').html(data.result.transfer_amount);
                            $('.currency_rate').html(data.result.current_rate);
                            $('.total_transfer').html(Number(parseFloat(data.result.converted_transfer_amount_in_partners_account).toFixed(2)).toLocaleString('en'));
                            /*----------  Adding Transaction Details in Form for Submission  ----------*/
                            $('.fund_transfer_source_currency_id').val(data.result.from_currency_id);
                            $('.fund_transfer_target_currency_id').val(data.result.to_currency_id);
                            $('.fund_transfer_source_currency_rate').val(data.result.from_currency_rate);
                            $('.fund_transfer_target_currency_rate').val(data.result.current_rate);
                            $('.fund_transfer_source_amount').val(data.result.transfer_amount);
                            $('.fund_transfer_target_amount').val(data.result.converted_transfer_amount_in_partners_account);
                            $('.fund_transfer_source_id').val(data.result.source_partners_detail.id);
                            $('.fund_transfer_target_id').val(data.result.target_partners_detail.id);
                            $('.fund_transfer_transfer_type').val(3);
                            $('.confirmation_deposit_funds').modal('show');
                        }
                        else {
                            var errorArray = data.msg_data;
                            errorArray.forEach(function (e) {
                                list = list + '<li>' + e + '</li>';
                            });

                            $('#msg-list').html(list);
                            $('.msg-box').addClass("alert-danger").show();
                        }
                        $("html, .container").animate({scrollTop: 0}, 600);
                        $('.loading_gif_deposit').hide();
                    }
                });
            });


            /*=============================================
             =            Edit Section for Transactions   =
             =============================================*/

                    @if(isset($edit_version))
                    @if(isset($transaction_details['transfer_type']))
                    @if($transaction_details['AccountStatus'] == 2 && $transaction_details['transfer_type'] == 1)

                    @elseif($transaction_details['AccountStatus'] == 1 && $transaction_details['transfer_type'] == 1)

                    @else

                    @endif
                    @else
                    @if($transaction_details['AccountStatus'] == 2)
            var partner_id = '{!! $responseResult[0]['source_id'] !!}';
            transfer_in_partners_account(1);
            $('.deposite_section').show();
            $('.partner_account').val(partner_id);
                    @else
            var partner_id = '{!! $responseResult[0]['source_id'] !!}';
            withdraw_partners_account(partner_id);
            $('.withdraw_section').show();
            $('.wh_partner_account').val(partner_id);
            @endif
            @endif
            @endif

            /**
             *
             * Section for Common Functionalities
             *
             */
            /*----------  Check for add or remove funds  ----------*/
            $('.amount_options').on('click', function () {
                if ($(this).val() == 1) {
                    $('.settle_symbol').text('+');
                    $('.settle_account_type').val(1);
                }
                else {
                    $('.settle_symbol').text('-');
                    $('.settle_account_type').val(2);
                }
            });


            /*=====  End Edit Section for Transactions  ======*/

        });
    </script>
@endsection  	