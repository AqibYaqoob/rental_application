@extends('company_portal.layouts.app')
@section('content')
    <ol class="breadcrumb">
        <li class="breadcrumb-item"><a href="{{ url('admin/company/dashboard')}}">{{ trans('labels.dashboard') }}</a></li>
        <li class="breadcrumb-item active">{{ trans('labels.currecny_list') }}</li>
        <!-- Breadcrumb Menu-->
        <li class="breadcrumb-menu d-md-down-none">
            <div class="btn-group" role="group" aria-label="Button group with nested dropdown">
                <a class="btn" href="#"><i class="icon-graph"></i> &nbsp;{{ $balance . ' '. $currencyName }}</a>
            </div>
        </li>
    </ol>
    <!-- Main Content of the Page -->
    <div class="container-fluid">
        @if(session('success'))
            <div class="alert alert-success">
                {{session('success')}}
            </div>
        @endif
        @if(session('error'))
            <div class="alert alert-danger">
                {{ session('error') }}
            </div>
        @endif
        <div class="row">
            <div class="col-md-12">
                <div class="card">
                    <div class="card-header">
                        <i class="fa fa-align-justify"></i>
                        {{ trans('labels.currencies_list') }}
                    </div>
                    <div class="card-body">
                        <table class="table table-responsive-sm table-bordered table-striped table-sm">
                            <thead>
                            <th>{{ trans('labels.no') }}</th>
                            <th>{{ trans('labels.currency_name') }}</th>
                            <th>{{ trans('labels.created_date') }}</th>
                            <th>{{ trans('labels.base_currency') }}</th>
                            <th>{{ trans('labels.currency_rate') }}</th>
                            <th>{{ trans('labels.action') }}</th>
                            </thead>
                            <tbody>
                            @foreach($currencies as $currency)
                                <tr>
                                    <td>{{$currency->Id}}</td>
                                    <td>{{$currency->currencyList->currency.' ('.$currency->currencyList->code.')'}}</td>
                                    <td>{!! GeneralFunctions::convertToDateTimeToString($currency->created_at) !!}</td>
                                    <td>{{ ($currency->isBaseCurrency == 1) ? 'Yes' : 'No' }}</td>
                                    <td>{{ $currency->CurrentRate }}</td>
                                    <td>
                                        <div class="btn-group" role="group">
                                            <button id="btnGroupDrop1" type="button" class="btn btn-secondary btn-sm dropdown-toggle" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
                                                {{ trans('labels.action') }}
                                            </button>
                                            <div class="dropdown-menu" aria-labelledby="btnGroupDrop1">
                                                {{--@if(GeneralFunctions::check_edit_permission('currency.edit'))
                                                    <a href="{{route('currency.edit', ['id' => \Illuminate\Support\Facades\Crypt::encryptString($currency->Id)])}}" class="dropdown-item"><i class="fa fa-edit"></i> {{ trans('labels.edit') }}</a>
                                                @endif--}}
                                                @if(GeneralFunctions::check_delete_permission('currency_list'))
                                                    <a class="dropdown-item delete" data-id="{{\Illuminate\Support\Facades\Crypt::encryptString($currency->Id)}}" ><i class="fa fa-trash"></i> {{ trans('labels.delete') }}</a>
                                                @endif
                                            </div>
                                        </div>
                                    </td>
                                </tr>
                            @endforeach
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>
    </div>
    {{--Modal--}}
    <div class="remodal" data-remodal-id="delete_modal" data-remodal-options="hashTracking: false, closeOnOutsideClick: false">
        <button data-remodal-action="close" class="remodal-close"></button>
        <h1>{{ trans('labels.remove_currency') }}</h1>
        <p>
            {{ trans('labels.are_you_sure_to_delete_it') }}
        </p>
        <form id="state_form" action="{{ route('currency.delete') }}" method="POST">
            <input type="hidden" id="data-id" name="id" value=""/>
            {{ csrf_field() }}
        </form>
        <br>
        <button data-remodal-action="cancel" class="remodal-cancel">{{ trans('labels.cancel') }}</button>
        <button data-remodal-action="confirm" class="remodal-confirm">{{ trans('labels.ok') }}</button>
    </div>
@endsection
@section('js')
    <script type="text/javascript">
        $(document).ready(function(){
            $(document).on('click', '.delete', function(){
                var id = $(this).attr('data-id');
                $('#data-id').val(id);
                var inst = $('[data-remodal-id=delete_modal]').remodal();
                inst.open();
            });
            $(document).on('confirmation', '.remodal', function () {
                $('#state_form').submit()[0];
            });
        });
    </script>
@endsection