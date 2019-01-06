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
         @include('errors.flash_message')
        <div class="row">
            <div class="col-md-12">
                <div class="card">
                    <div class="card-header">
                        <i class="fa fa-align-justify"></i>
                        {{ trans('labels.assign_account_to_partner') }}
                    </div>
                    <div class="card-body">
                        <form action="{{route('assign.account')}}" method="post" class="form-horizontal">
                            {{csrf_field()}}
                            <div class="form-group row">
                                <div class="col-md-2">{{ trans('labels.select_site_account') }}</div>
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
