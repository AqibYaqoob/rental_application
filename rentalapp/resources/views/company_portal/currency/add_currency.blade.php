@extends('company_portal.layouts.app')
@section('content')
    <ol class="breadcrumb">
        <li class="breadcrumb-item"><a href="{{ url('admin/company/dashboard')}}">{{ trans('labels.dashboard') }}</a></li>
        <li class="breadcrumb-item active">{{ trans('labels.add_currency') }}</li>
        <!-- Breadcrumb Menu-->
        <li class="breadcrumb-menu d-md-down-none">
            <div class="btn-group" role="group" aria-label="Button group with nested dropdown">
                <a class="btn" href="#"><i class="icon-graph"></i> &nbsp;{{ $balance . ' '. $currencyName }}</a>
            </div>
        </li>
    </ol>
    <!-- Main Content of the Page -->
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
                        {{ trans('labels.add_currency') }}
                    </div>
                    <div class="card-body">
                        <form action="{{route('currency.add')}}" method="post" class="form-horizontal">
                            <input type="hidden" name="_token" value="{{csrf_token()}}"/>
                            <div class="row form-group">
                                <div class="col-md-2">{{ trans('labels.currency') }}</div>
                                <div class="col-md-9">
                                    <select name="currency_name" class="form-control">
                                        <option>Select Currency</option>
                                        @foreach($currencies as $currency)
                                            <option value="{{ $currency->id }}">{{ $currency->currency.'    ('.$currency->code.')' }}</option>
                                        @endforeach
                                    </select>
                                </div>
                            </div>
                            <div class="row form-group">
                                <div class="col-md-2">{{ trans('labels.current_rate') }}</div>
                                <div class="col-md-9">
                                    <input type="number" step="any" name="rate" value="" class="form-control" placeholder="0.00"/>
                                </div>
                            </div>
                            {{--<div class="form-check checkbox">
                                <input type="checkbox" name="isbase" value="1"/>
                                <label for="isbase" class="col-form-label">Base Currency</label>
                            </div>--}}
                            <div class="col-md-9 offset-2 text-center">
                                @if(GeneralFunctions::check_add_permission('currency_form'))
                                    <button type="submit" class="btn btn-md btn-primary">{{ trans('labels.add') }}</button>
                                @endif
                            </div>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection