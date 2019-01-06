@extends('company_portal.layouts.app')
@section('content')
    <ol class="breadcrumb">
        <li class="breadcrumb-item">Home</li>
        <li class="breadcrumb-item"><a href="#">Company</a></li>
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
                        {{ trans('labels.assigned_currencies') }}
                    </div>
                    <div class="card-body">
                        <table class="table table-responsive-sm table-bordered table-striped table-sm">
                            <thead>
                            <th>#</th>
                            <th>{{ trans('labels.partner') }}</th>
                            <th>{{ trans('labels.base_currency') }}</th>
                            <th>{{ trans('labels.created_date') }}</th>
                            </thead>
                            <tbody>
                            @php $count = 1; @endphp
                            @foreach($settings as $setting)
                                <tr>
                                    <td> {{ $count++ }}</td>
                                    <td> {{ $setting->Username }}</td>
                                    <td> {{(isset($setting->partner_settings) && $setting->partner_settings->settingName == 'base_currency') ? \App\Currency::find($setting->partner_settings->value)->CurrencyName : '' }}</td>
                                    <td> {{ (isset($setting->partner_settings)) ? $setting->partner_settings->created_at->format('jS F Y g:ia') : "" }}</td>
                                </tr>
                            @endforeach
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection
