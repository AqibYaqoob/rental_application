@extends('company_portal.layouts.app')
@section('content')
    <ol class="breadcrumb">
        <li class="breadcrumb-item"><a href="{{ url('admin/company/dashboard')}}">{{ trans('labels.dashboard') }}</a></li>
        <li class="breadcrumb-item active">{{ trans('labels.edit_site_account') }}</li>
        <!-- Breadcrumb Menu-->
        <li class="breadcrumb-menu d-md-down-none">
            <div class="btn-group" role="group" aria-label="Button group with nested dropdown">
                <a class="btn" href="#"><i class="icon-graph"></i> &nbsp;{{ $balance . ' '. $currencyName }}</a>
            </div>
        </li>
    </ol>
    <div class="container-fluid">
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
                        {{ trans('labels.edit_partner_site_account') }}
                    </div>
                    <div class="card-body">
                        <form action="{{route('partner.account.update')}}" method="post" class="form-horizontal">
                            {{csrf_field()}}
                            <input type="hidden" name="id" value="{{\Illuminate\Support\Facades\Crypt::encryptString($partnerAccount->Id)}}" />
                            <div class="form-group row">
                                <div class="col-md-2">{{ trans('labels.select_site_account') }}</div>
                                <div class="col-md-9">
                                    <select name="siteAccount" class="form-control">
                                        @foreach($siteAccounts as $siteAccount)
                                            <option value="{{$siteAccount->Id}}" {{ ($partnerAccount->SiteAccountId == $siteAccount->Id) ? 'Selected' : '' }}>{{$siteAccount->SiteAccountCode}}</option>
                                        @endforeach
                                    </select>
                                </div>
                            </div>
                            <div class="form-group row">
                                <div class="col-md-2">{{ trans('labels.select_partner') }}</div>
                                <div class="col-md-9">
                                    <select name="partner" class="form-control">
                                        @foreach($partners as $partner)
                                            <option value="{{$partner->id}}" {{ ($partnerAccount->PartnerId == $partner->id) ? 'Selected' : ''}}>{{$partner->Username}}</option>
                                        @endforeach
                                    </select>
                                </div>
                            </div>
                            <div class="col-md-9 offset-2 text-center">
                                <button type="submit" class="btn btn-md btn-primary">{{ trans('labels.change_assignment') }}</button>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection