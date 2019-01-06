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
                        {{ trans('labels.edit_shareholder_account') }}
                    </div>
                    <div class="card-body">
                        <form action="{{route('shareHolder.assigned.update')}}" method="post" class="form-horizontal">
                            {{csrf_field()}}
                            <input type="hidden" name="id" value="{{ \Illuminate\Support\Facades\Crypt::encryptString($shareholder->Id) }}" />
                            <div class="form-group row">
                                <div class="col-md-2">{{ trans('labels.select_site_account') }}</div>
                                <div class="col-md-9">
                                    <select name="siteAccount" class="form-control">
                                        @foreach($siteAccounts as $siteAccount)
                                            <option value="{{$siteAccount->Id}}" {{ ($shareholder->SiteAccountId == $siteAccount->Id) ? 'Selected' : '' }}>{{$siteAccount->SiteAccountCode}}</option>
                                        @endforeach
                                    </select>
                                </div>
                            </div>
                            <div class="form-group row">
                                <div class="col-md-2">{{ trans('labels.select_partner') }}</div>
                                <div class="col-md-9">
                                    <select name="partner" class="form-control">
                                        @foreach($partners as $partner)
                                            <option value="{{$partner->id}}" {{ ($shareholder->PartnerId == $siteAccount->Id) ? 'Selected' : '' }}>{{$partner->Username}}</option>
                                        @endforeach
                                    </select>
                                </div>
                            </div>
                            <div class="form-group row">
                                <div class="col-md-2">{{ trans('labels.win_percent') }}</div>
                                <div class="col-md-9 input-group">
                                    {{Form::number('winPercent', ($shareholder->WinPercent*100), ['class' => 'form-control', 'step' => 'any'])}}
                                    <span class="input-group-addon">%</span>
                                </div>
                            </div>
                            <div class="form-group row">
                                <div class="col-md-2">{{ trans('labels.lose_percent') }}</div>
                                <div class="col-md-9 input-group">
                                    {{Form::number('losePercent', ($shareholder->LosePercent*100), ['class' => 'form-control', 'step' => 'any'])}}
                                    <span class="input-group-addon">%</span>
                                </div>
                            </div>
                            <div class="form-group row">
                                <div class="col-md-2">{{ trans('labels.total_commission_percent') }}</div>
                                <div class="col-md-9 input-group">
                                    {{Form::number('TotalCommissionPercent', ($shareholder->TotalCommissionPercent*100), ['class' => 'form-control', 'step' => 'any'])}}
                                    <span class="input-group-addon">%</span>
                                </div>
                            </div>
                            <div class="form-group row">
                                <div class="col-md-2">{{ trans('labels.total_turnover_percent') }}</div>
                                <div class="col-md-9 input-group">
                                    {{Form::number('TotalTurnoverPercentForCommission', ($shareholder->TotalTurnoverPercentForCommission*100), ['class' => 'form-control', 'step' => 'any'])}}
                                    <span class="input-group-addon">%</span>
                                </div>
                            </div>
                            <div class="col-md-9 offset-2 text-center">
                                <button type="submit" class="btn btn-md btn-primary">{{ trans('update') }}</button>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection