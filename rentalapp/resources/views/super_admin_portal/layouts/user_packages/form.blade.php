@extends('super_admin_portal.layouts.app')
@section('content')
    @php
      $package_name = null;
      $description = null;
      $properties_range = null;
      $amount = null;
        if(isset($packages_details)){
            $package_name = $packages_details['package_name'];
            $description = $packages_details['description'];
            $properties_range = $packages_details['properties_range'];
            $amount = $packages_details['amount'];
        }
    @endphp
    <ol class="breadcrumb">
        <li class="breadcrumb-item"><a href="{{ url('admin/dashboard')}}">{{ trans('labels.dashboard') }}</a></li>
        <li class="breadcrumb-item active">{{ trans('labels.user_packages') }}</li>
    </ol>

    <div class="container-fluid">
        <div class="row">
            @include('errors.flash_message')
        </div>
        <div class="row">
            <div class="col-lg-12">
                <div class="card">
                    <div class="card-header">
                        <i class="fa fa-align-justify"></i> {{ trans('labels.user_packages') }}
                    </div>
                    <div class="card-body">
                        <form action="{{url('/admin/packages/save/record')}}" method="post"
                              class="form-horizontal packages_save_form">
                            <div class="form-group row">
                                <label class="col-md-3 col-form-label" for="package_name">{{ trans('labels.package_name') }}</label>
                                <div class="col-md-9">
                                    {{ csrf_field() }}
                                    <input type="hidden" name="id" value="{{Request::input('id')}}">
                                    <input type="text" id="package_name" name="package_name" class="form-control package_name" placeholder="Package Name" value="{{$package_name}}">
                                </div>
                            </div>
                            <div class="form-group row">
                                <label class="col-md-3 col-form-label" for="description">{{ trans('labels.description') }}</label>
                                <div class="col-md-9">
                                     <textarea id="description" name="description" class="form-control description">{{$description}}</textarea>
                                </div>
                            </div>
                            <div class="form-group row">
                                <label class="col-md-3 col-form-label" for="properties_range">{{ trans('labels.properties_range') }}</label>
                                <div class="col-md-9">
                                    <input type="number" id="properties_range" name="properties_range" class="form-control properties_range" placeholder="Property Range" value="{{$properties_range}}">
                                </div>
                            </div>
                            <div class="form-group row">
                                <label class="col-md-3 col-form-label" for="amount">{{ trans('labels.amount') }}</label>
                                <div class="col-md-9">
                                    <input type="text" id="amount" name="amount" class="form-control amount" placeholder="Amount" value="{{$amount}}">
                                </div>
                            </div>
                        </form>
                        <div class="col-lg-6 offset-6">
                            @if(GeneralFunctions::check_add_permission('packages_form') || GeneralFunctions::check_edit_permission('packages_form'))
                                <img src="{{ url('img/loading.gif') }}" class="loading_gif" style="height: 26px !important; display: none;">
                                <a href="javascript:void(0)" class="btn btn-primary save_record">{{ trans('labels.save') }}</a>
                            @endif
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection
@section('js')
    <script type="text/javascript">
        $(document).ready(function () {
            $(document).on('click', '.save_record', function () {
                $('.loading_gif').show();
                var data = $('.packages_save_form').serialize();
                var list = '';
                $.ajax({
                    type: 'POST',
                    url: "{{ url('/admin/packages/record/validation') }}",
                    data: data,
                    success: function (data) {
                        if (data.status == 'success') {
                            $('.packages_save_form').submit()[0];
                        }
                        else {
                            var errorArray = data.msg_data;
                            errorArray.forEach(function (e) {
                                list = list + '<li>' + e + '</li>';
                            });

                            $('#msg-list').html(list);
                            $('.msg-box').addClass("alert-danger").show();
                        }
                        $("html, .container").animate({scrollTop: 0}, 600);
                        $('.loading_gif').hide();
                    }
                });
            });
        });
    </script>
@endsection
