@extends('company_portal.layouts.app')
@section('content')
    <ol class="breadcrumb">
        <li class="breadcrumb-item"><a href="{{ url('admin/company/dashboard')}}">{{ trans('labels.dashboard') }}</a></li>
        <li class="breadcrumb-item active">{{ trans('labels.category_add') }}</li>
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
        <div class="row">
            <div class="col-md-12">
                <div class="card">
                    <div class="card-header">
                        <i class="fa fa-align-justify"></i>
                        {{ trans('labels.category_add') }}
                    </div>
                    <div class="card-body">
                        <form action="{{ route('add.category') }}" method="post">
                            {{ csrf_field() }}
                            <div class="row form-group">
                                <div class="col-md-3">
                                    <label>{{ trans('labels.category_name') }}</label>
                                </div>
                                <div class="col-md-9">
                                    <input type="text" name="category" value="" class="form-control" />
                                </div>
                            </div>
                            <div class="row form-group">
                                <div class="col-md-3">
                                    <label>{{ trans('labels.description') }}</label>
                                </div>
                                <div class="col-md-9">
                                    <textarea name="description" class="form-control" placeholder="Write Description"></textarea>
                                </div>
                            </div>
                            <div class="col-md-3 offset-7">
                                @if(GeneralFunctions::check_add_permission('cat_add'))
                                    <input type="submit" name="submit" value="{{ trans('labels.add') }}" class="btn btn-primary btn-md"/>
                                @endif
                            </div>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection