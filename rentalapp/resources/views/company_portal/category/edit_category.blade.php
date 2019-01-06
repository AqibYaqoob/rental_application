@extends('company_portal.layouts.app')
@section('content')
    <ol class="breadcrumb">
        <li class="breadcrumb-item"><a href="{{ url('admin/company/dashboard')}}">{{ trans('labels.dashboard') }}</a></li>
        <li class="breadcrumb-item active">{{ trans('labels.edit_category') }}</li>
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
                        {{ trans('labels.edit_category') }}
                    </div>
                    <div class="card-body">
                        <form action="{{ route('category.update') }}" method="post">
                            {{ csrf_field() }}
                            <input type="hidden" name="id" value="{{ \Illuminate\Support\Facades\Crypt::encryptString($category->id) }}" />
                            <div class="row form-group">
                                <div class="col-md-3">
                                    <label>{{ trans('labels.category_name') }}</label>
                                </div>
                                <div class="col-md-9">
                                    <input type="text" name="category" value="{{ $category->name }}" class="form-control" />
                                </div>
                            </div>
                            <div class="row form-group">
                                <div class="col-md-3">
                                    <label>{{ trans('labels.description') }}</label>
                                </div>
                                <div class="col-md-9">
                                    <textarea name="description" class="form-control" placeholder="Write Description">{{ $category->description }}</textarea>
                                </div>
                            </div>
                            <div class="col-md-3 offset-7">
                                @if(GeneralFunctions::check_edit_permission('cat_add'))
                                    <input type="submit" name="submit" value="{{ trans('labels.update') }}" class="btn btn-primary btn-md"/>
                                @endif
                            </div>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection