@extends('company_portal.layouts.app')
@section('content')
    <ol class="breadcrumb">
        <li class="breadcrumb-item">Home</li>
        <li class="breadcrumb-item"><a href="#">Admin</a></li>
        <li class="breadcrumb-item active">Staff List</li>
        <!-- Breadcrumb Menu-->
        <li class="breadcrumb-menu d-md-down-none">
            <div class="btn-group" role="group" aria-label="Button group with nested dropdown">
                <a class="btn" href="{{ url('admin/company/staff_list')}}"><i class="icon-graph"></i> &nbsp;Company List</a>
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
        <div class="row">
            <div class="col-md-12">
                <div class="card">
                    <div class="card-header">
                        <i class="fa fa-align-justify"></i> {{ trans('labels.edit_currency') }}
                    </div>
                    <div class="card-body">
                        <form action="{{route('currency.update')}}" method="post" class="form-horizontal">
                            <input type="hidden" name="id" value="{{ \Illuminate\Support\Facades\Crypt::encryptString($editable->Id) }}" />
                            <input type="hidden" name="_token" value="{{csrf_token()}}" />
                            <div class="form-group row">
                                <div class="col-md-2">
                                    <label for="currency_name" class="col-form-label">{{ trans('labels.currency_name') }}</label>
                                </div>
                                <div class="col-md-9">
                                    <select name="currency_name" class="form-control" disabled>
                                        @foreach($currencies as $currency)
                                            <option value="{{ $currency->id }}" {{ ($currency->id == $editable->currency_id) ? 'selected' : '' }}>{{ $currency->currency.'    ('.$currency->code.')' }}</option>
                                        @endforeach
                                    </select>
                                </div>
                            </div>
                            <div class="row form-group">
                                <div class="col-md-2">
                                    <label for="currency_name" class="col-form-label">{{ trans('labels.current_rate') }}</label>
                                </div>
                                <div class="col-md-9">
                                    <input type="number" step="any" name="rate" value="" class="form-control" placeholder="0.00"/>
                                </div>
                            </div>
                            <div class="col-md-9 offset-2 text-center">
                                <button type="submit" class="btn btn-md btn-primary">{{ trans('labels.update') }}</button>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </div>
    @endsection