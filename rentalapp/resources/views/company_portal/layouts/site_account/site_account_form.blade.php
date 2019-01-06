@extends('company_portal.layouts.app')
@section('content')
    @php
        $siteId = null;
        $currencyId = null;
        $account_code = '';
        $account_label = '';
        $total_turnover = '';
        $max_single_bet = '';
        $credit = '';
        $per_bet = '';
        $remark_label = '';
        $remarks = '';
        $categoryId = '';
        $shareholder = collect([]);
        $assignment = null;
        if(isset($site_details)){
          $siteId = $site_details['SiteId'];
          $currencyId = $site_details['CurrencyId'];
          $account_code = $site_details['SiteAccountCode'];
          $account_label = $site_details['SiteAccountLabelColor'];
          $total_turnover = GeneralFunctions::toPercentage($site_details['TotalTurnoverPercent']);
          $max_single_bet = $site_details['MaxSingleBet'];
          $credit = $site_details['credit'];
          $per_bet = $site_details['per_bet'];
          $remark_label = $site_details['RemarksLabelColor'];
          $remarks = $site_details['Remarks'];
          $categoryId = $site_details['category_id'];
          $shareholder = $shareholders;
          $assignment = $assignments;
        }
    @endphp
    <ol class="breadcrumb">
        <li class="breadcrumb-item"><a href="{{ url('admin/company/dashboard')}}">{{ trans('labels.dashboard') }}</a></li>
        <li class="breadcrumb-item active">{{ trans('labels.add_site_account') }}</li>
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
                        <div class="card">
                            <div class="card-header">
                            </div>
                            <div class="card-body">
                                <form action="{{url('/admin/company/site/account/save/record')}}" method="post" class="repeater form-horizontal site_save_form">
                                    <div class="form-group row">
                                        <label class="col-md-3 col-form-label" for="site_name">{{ trans('labels.select_site_name') }}</label>
                                        <div class="col-md-9">
                                            {{ csrf_field() }}
                                            <input type="hidden" name="id" value="{{Request::input('id')}}">
                                            <select class="form-control" name="site_name">
                                                <option value="">{{ trans('labels.choose_option') }}</option>
                                                @foreach($sites as $key => $value)
                                                    <option value="{{$value['Id']}}" {!! $siteId ? 'selected' : ''!!}>{{$value['SiteName']}}</option>
                                                @endforeach
                                            </select>
                                        </div>
                                    </div>
                                    <div class="row form-group">
                                        <div class="col-md-3">
                                            <label>{{ trans('labels.select_category') }}</label>
                                        </div>
                                        <div class="col-md-9">
                                            <select name="category" class="form-control">
                                                <option value="">{{ trans('labels.choose_option') }}</option>
                                                @foreach($category as $cat)
                                                    <option value="{{ $cat->id }}" {{ ($cat->id == $categoryId) ? 'selected' : '' }}>{{ $cat->name }}</option>
                                                @endforeach
                                            </select>
                                        </div>
                                    </div>
                                    <div class="form-group row">
                                        <label class="col-md-3 col-form-label" for="currency">{{ trans('labels.select_currency') }}</label>
                                        <div class="col-md-9">
                                            <select class="form-control" name="currency">
                                                <option value="">{{ trans('labels.choose_option') }}</option>
                                                @foreach($currencies as $key => $value)
                                                    <option value="{{$value['Id']}}" {!! ($currencyId ==  $value['Id']) ? 'selected' : ''!!}>{{ $value['currency_list']['currency'].' ('.$value['currency_list']['code'].')' }}</option>
                                                @endforeach
                                            </select>
                                        </div>
                                    </div>
                                    <div class="form-group row">
                                        <label class="col-md-3 col-form-label" for="site_account_code">{{ trans('labels.site_account_code') }}</label>
                                        <div class="col-md-9">
                                            <input type="text" class="form-control" name="site_account_code" value="{{$account_code}}">
                                        </div>
                                    </div>
                                    <div class="form-group row">
                                        <label class="col-md-3 col-form-label" for="site_account_label_code">{{ trans('labels.site_account_label_color') }}</label>
                                        <div class="col-md-9">
                                            <input type="text" class="form-control" name="site_account_label_code" value="{{$account_label}}">
                                        </div>
                                    </div>
                                    <div class="form-group row">
                                        <label class="col-md-3 col-form-label" for="total_ternover_percent">{{ trans('labels.total_turnover_percent') }}</label>
                                        <div class="col-md-3 input-group">
                                            <input type="number" class="form-control" name="total_ternover_percent" value="{{$total_turnover}}">
                                            <span class="input-group-addon">%</span>
                                        </div>
                                    </div>
                                    <div class="form-group row">
                                        <label class="col-md-3 col-form-label" for="max_single_bet">{{ trans('labels.max_single_bet') }}</label>
                                        <div class="col-md-3">
                                            <input type="number" class="form-control" name="max_single_bet" value="{{$max_single_bet}}">
                                        </div>
                                    </div>
                                    <div class="form-group row">
                                        <div class="col-md-3">{{ trans('labels.credit') }}</div>
                                        <div class="col-md-3"><input type="number" class="form-control" name="credit" value="{{ $credit }}" /></div>
                                    </div>
                                    <div class="form-group row">
                                        <div class="col-md-3">{{ trans('labels.per_bet_value') }}</div>
                                        <div class="col-md-3"><input type="number" class="form-control" name="per_bet" value="{{ $per_bet }}" /></div>
                                    </div>
                                    <div class="form-group row">
                                        <label class="col-md-3 col-form-label" for="remarks_label_color">{{ trans('labels.remarks_label_color') }}</label>
                                        <div class="col-md-9">
                                            <input type="text" class="form-control" name="remarks_label_color" value="{{$remark_label}}">
                                        </div>
                                    </div>
                                    <div class="form-group row">
                                        <label class="col-md-3 col-form-label" for="remarks">{{ trans('labels.remarks') }}</label>
                                        <div class="col-md-9">
                                            <textarea id="remarks" name="remarks" class="form-control remarks">{{$remarks}}</textarea>
                                        </div>
                                    </div>
                                    <div class="row form-group">
                                        <div class="col-md-3 offset-3"><label for="assignTo"><input type="checkbox" name="assignTo" value="1" {{ (!is_null($assignment)) ? "checked" : "" }} /> Assign To Partner</label></div>
                                    </div>
                                    <div class="warp" style="display: {{ (empty($assignment) == false) ? 'block' : 'none' }}">
                                        @if(!is_null($assignment))
                                            <input type="hidden" name="assignId" value="{{ \Illuminate\Support\Facades\Crypt::encryptString($assignment->Id) }}" />
                                        @endif
                                        <div class="form-group row">
                                            <div class="col-md-3">{{ trans('labels.select_partner') }}</div>
                                            <div class="col-md-9">
                                                <select name="partner" class="form-control">
                                                    @foreach($partners as $partner)
                                                        <option value="{{$partner->id}}" {{ ((!is_null($assignment)) && $assignment->PartnerId == $partner->id) ? "selected" : "" }}>{{$partner->Username}}</option>
                                                    @endforeach
                                                </select>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="row form-group">
                                        <div class="col-md-3 offset-3"><label for="shareHolder"><input type="checkbox" name="shareHolder" value="1" {{ ($shareholder->count() != 0) ? "checked" : "" }} /> Add ShareHolder</label></div>
                                    </div>
                                    <div class="wrapper" style="display: {{ ($shareholder->count() != 0) ? 'block' : 'none' }};">

                                        <div data-repeater-list="lst">
                                            @if($shareholder->count() != 0)
                                                @foreach($shareholder as $item)
                                                    <div data-repeater-item>
                                                        <input type="hidden" name="shareId" value="{{ \Illuminate\Support\Facades\Crypt::encryptString($item->Id) }}" />
                                                        <div class="form-group row">
                                                            <div class="col-md-3">{{ trans('labels.select_partner') }}</div>
                                                            <div class="col-md-9">
                                                                <select name="partner" class="form-control">
                                                                    @foreach($partners as $partner)
                                                                        <option value="{{$partner->id}}" {{ ($item->PartnerId == $partner->id) ? "selected" : "" }}>{{$partner->Username}}</option>
                                                                    @endforeach
                                                                </select>
                                                            </div>
                                                        </div>
                                                        <div class="form-group row">
                                                            <div class="col-md-3">{{ trans('labels.win_percent') }}</div>
                                                            <div class="col-md-9 input-group">
                                                                {{Form::number('winPercent', ($item->WinPercent*100) ?? "", ['class' => 'form-control', 'step' => 'any'])}}
                                                                <span class="input-group-addon">%</span>
                                                            </div>
                                                        </div>
                                                        <div class="form-group row">
                                                            <div class="col-md-3">{{ trans('labels.lose_percent') }}</div>
                                                            <div class="col-md-9 input-group">
                                                                {{Form::number('losePercent', ($item->LosePercent*100) ?? "", ['class' => 'form-control', 'step' => 'any'])}}
                                                                <span class="input-group-addon">%</span>
                                                            </div>
                                                        </div>
                                                        <div class="form-group row">
                                                            <div class="col-md-3">{{ trans('labels.total_commission_percent') }}</div>
                                                            <div class="col-md-9 input-group">
                                                                {{Form::number('TotalCommissionPercent', ($item->TotalCommissionPercent*100) ?? "", ['class' => 'form-control', 'step' => 'any'])}}
                                                                <span class="input-group-addon">%</span>
                                                            </div>
                                                        </div>
                                                        <div class="form-group row">
                                                            <div class="col-md-3">{{ trans('labels.total_turnover_percent') }}</div>
                                                            <div class="col-md-9 input-group">
                                                                {{Form::number('TotalTurnoverPercentForCommission', ($item->TotalTurnoverPercentForCommission*100) ?? "", ['class' => 'form-control', 'step' => 'any'])}}
                                                                <span class="input-group-addon">%</span>
                                                            </div>
                                                        </div>
                                                        <div class="row form-group">
                                                            <div class="col-md-3 offset-3">
                                                                <button data-repeater-delete type="button" class="btn btn-sm btn-danger"><i class="fa fa-trash"></i> {{ trans('labels.delete') }}</button>
                                                            </div>
                                                        </div>
                                                    </div>
                                                @endforeach
                                            @else
                                                <div data-repeater-item>
                                                    <div class="form-group row">
                                                        <div class="col-md-3">{{ trans('labels.select_partner') }}</div>
                                                        <div class="col-md-9">
                                                            <select name="partner" class="form-control">
                                                                @foreach($partners as $partner)
                                                                    <option value="{{$partner->id}}">{{$partner->Username}}</option>
                                                                @endforeach
                                                            </select>
                                                        </div>
                                                    </div>
                                                    <div class="form-group row">
                                                        <div class="col-md-3">{{ trans('labels.win_percent') }}</div>
                                                        <div class="col-md-9 input-group">
                                                            {{Form::number('winPercent', null, ['class' => 'form-control', 'step' => 'any'])}}
                                                            <span class="input-group-addon">%</span>
                                                        </div>
                                                    </div>
                                                    <div class="form-group row">
                                                        <div class="col-md-3">{{ trans('labels.lose_percent') }}</div>
                                                        <div class="col-md-9 input-group">
                                                            {{Form::number('losePercent', null, ['class' => 'form-control', 'step' => 'any'])}}
                                                            <span class="input-group-addon">%</span>
                                                        </div>
                                                    </div>
                                                    <div class="form-group row">
                                                        <div class="col-md-3">{{ trans('labels.total_commission_percent') }}</div>
                                                        <div class="col-md-9 input-group">
                                                            {{Form::number('TotalCommissionPercent', null, ['class' => 'form-control', 'step' => 'any'])}}
                                                            <span class="input-group-addon">%</span>
                                                        </div>
                                                    </div>
                                                    <div class="form-group row">
                                                        <div class="col-md-3">{{ trans('labels.total_turnover_percent') }}</div>
                                                        <div class="col-md-9 input-group">
                                                            {{Form::number('TotalTurnoverPercentForCommission', null, ['class' => 'form-control', 'step' => 'any'])}}
                                                            <span class="input-group-addon">%</span>
                                                        </div>
                                                    </div>
                                                    <div class="row form-group">
                                                        <div class="col-md-3 offset-3">
                                                            <button data-repeater-delete type="button" class="btn btn-sm btn-danger"><i class="fa fa-trash"></i> {{ trans('labels.delete') }}</button>
                                                        </div>
                                                    </div>
                                                </div>
                                            @endif
                                        </div>

                                        <button data-repeater-create type="button" class="btn btn-sm btn-primary"><i class="fa fa-plus"></i> {{ trans('labels.add') }}</button>
                                    </div>
                                </form>
                            </div>
                            <div class="card-footer">
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            <div class="col-lg-6">
                @if(GeneralFunctions::check_add_permission('site_account_form') || GeneralFunctions::check_edit_permission('site_account_form'))
                    <img src="{{ url('img/loading.gif') }}" class="loading_gif" style="height: 26px !important; display: none;">
                    <a href="javascript:void(0)" class="btn btn-primary save_record">{{ trans('labels.save') }}</a>
                @endif
            </div>
        </div>
    </div>
@endsection
@section('js')
    <script src="{{ asset('vendor/repeater/jquery.repeater.min.js') }}"></script>
    <script type="text/javascript">
        $(document).ready(function(){
            $(document).on('click', '.save_record', function(){
                $('.loading_gif').show();
                var data = $('.site_save_form').serialize();
                var list = '';
                $.ajax({
                    type: 'POST',
                    url: "{{ url('/admin/company/site/account/record/validation') }}",
                    data: data,
                    success:function(data){
                        if(data.status == 'success')
                        {
                            $('.site_save_form').submit()[0];
                        }
                        else{
                            var errorArray = data.msg_data;
                            errorArray.forEach(function(e){
                                list = list +'<li>'+e+'</li>';
                            });
                            $('#msg-list').html(list);
                            $('.msg-box').addClass("alert-danger").show();
                        }
                        $("html, .container").animate({ scrollTop: 0 }, 600);
                        $('.loading_gif').hide();
                    }
                });
            });
            // showing partner list for assignment
            $('input[name="assignTo"]').change(function(){
                if(this.checked)
                {
                    $('.warp').fadeIn('slow').show();
                }
                else if(!this.checked)
                {
                    $('.warp').fadeOut('slow').hide();
                }
            });
            // assign shareHolder
            $('input[name="shareHolder"]').change(function(){
                if(this.checked)
                {
                    $('.wrapper').fadeIn('slow').show();
                }
                else if(!this.checked)
                {
                    $('.wrapper').fadeOut('slow').hide();
                }
            });
            // repeater //
            $('.repeater').repeater({
                isFirstItemUndeletable: true,
                show:function () {
                    $(this).slideDown();
                }
            });
        });
    </script>
@endsection
