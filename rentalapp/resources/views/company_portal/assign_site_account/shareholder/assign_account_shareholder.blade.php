@extends('company_portal.layouts.app')
@section('content')
    <ol class="breadcrumb">
        <li class="breadcrumb-item">Home</li>
        <li class="breadcrumb-item"><a href="#">Admin</a></li>
        <li class="breadcrumb-item active">Dashboard</li>
        <!-- Breadcrumb Menu-->
        <li class="breadcrumb-menu d-md-down-none">
            <div class="btn-group" role="group" aria-label="Button group with nested dropdown">
                <a class="btn" href="{{ url('admin/company/dashboard')}}"><i class="icon-graph"></i> &nbsp;Dashboard</a>
            </div>
        </li>
    </ol>
    <div class="container-fluid">
        @if ($errors->any())
            <div class="alert alert-danger">
                <ul>
                    @foreach ($errors->all() as $error)
                        <li>{{ $error }}</li>
                    @endforeach
                </ul>
            </div>
        @endif
        @if(session('success'))
            <div class="alert alert-success">
                {{session('success')}}
            </div>
        @endif
        @if(session('error'))
            <div class="alert alert-danger">
                {{session('error')}}
            </div>
        @endif
        <div class="row">
            <div class="col-md-12">
                <div class="card">
                    <div class="card-header">
                        <i class="fa fa-align-justify"></i>
                        {{ trans('labels.assign_account_to_shareholder') }}
                    </div>
                    <div class="card-body">
                        <form action="{{route('assign.account.shareholder')}}" method="post" class="form-horizontal">
                            {{csrf_field()}}
                            <div class="form-group row">
                                <div class="col-md-2">{{ trans('labels.select_site_account_code') }}</div>
                                <div class="col-md-9">
                                    <select name="siteAccount" class="form-control">
                                        @foreach($siteAccounts as $siteAccount)
                                            <option value="{{$siteAccount->Id}}">{{$siteAccount->SiteAccountCode}}</option>
                                        @endforeach
                                    </select>
                                </div>
                            </div>
                            <div class="form-group row">
                                <div class="col-md-2">{{ trans('labels.select_partner') }}</div>
                                <div class="col-md-9">
                                    <select name="partner" class="form-control">
                                        @foreach($partners as $partner)
                                            <option value="{{$partner->id}}">{{$partner->Username}}</option>
                                        @endforeach
                                    </select>
                                </div>
                            </div>
                            <div class="form-group row">
                                <div class="col-md-2">{{ trans('labels.win_percent') }}</div>
                                <div class="col-md-9 input-group">
                                    {{Form::number('winPercent', null, ['class' => 'form-control', 'step' => 'any'])}}
                                    <span class="input-group-addon">%</span>
                                </div>
                            </div>
                            <div class="form-group row">
                                <div class="col-md-2">{{ trans('labels.lose_percent') }}</div>
                                <div class="col-md-9 input-group">
                                    {{Form::number('losePercent', null, ['class' => 'form-control', 'step' => 'any'])}}
                                    <span class="input-group-addon">%</span>
                                </div>
                            </div>
                            <div class="form-group row">
                                <div class="col-md-2">{{ trans('labels.total_commission_percent') }}</div>
                                <div class="col-md-9 input-group">
                                    {{Form::number('TotalCommissionPercent', null, ['class' => 'form-control', 'step' => 'any'])}}
                                    <span class="input-group-addon">%</span>
                                </div>
                            </div>
                            <div class="form-group row">
                                <div class="col-md-2">{{ trans('labels.total_turnover_percent') }}</div>
                                <div class="col-md-9 input-group">
                                    {{Form::number('TotalTurnoverPercentForCommission', null, ['class' => 'form-control', 'step' => 'any'])}}
                                    <span class="input-group-addon">%</span>
                                </div>
                            </div>
                            <div class="col-md-9 offset-2 text-center">
                                <button type="submit" class="btn btn-md btn-primary">{{ trans('labels.assign') }}</button>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection
