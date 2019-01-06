@extends('super_admin_portal.layouts.app')
@section('content')
    @php
      $name = '';
      $address = '';
      $phone_number = '';
        if(isset($supplier_details)){
            $name = $supplier_details['name'];
            $address = $supplier_details['address'];
            $phone_number = $supplier_details['phone_number'];
        }
    @endphp
    <ol class="breadcrumb">
        <li class="breadcrumb-item"><a href="{{ url('admin/dashboard')}}">{{ trans('labels.dashboard') }}</a></li>
        <li class="breadcrumb-item active">{{ trans('labels.suppliers') }}</li>
    </ol>

    <div class="container-fluid">
        <div class="row">
            @include('errors.flash_message')
        </div>
        <div class="row">
            <div class="col-lg-12">
                <div class="card">
                    <div class="card-header">
                        <i class="fa fa-align-justify"></i> {{ trans('labels.suppliers') }}
                    </div>
                    <div class="card-body">
                        <form action="{{url('/admin/supplier/save/record')}}" method="post"
                              class="form-horizontal supplier_save_form">
                            <div class="form-group row">
                                <label class="col-md-3 col-form-label" for="name">{{ trans('labels.name') }}</label>
                                <div class="col-md-9">
                                    {{ csrf_field() }}
                                    <input type="hidden" name="id" value="{{Request::input('id')}}">
                                    <input type="text" id="name" name="name" class="form-control name" placeholder="Name" value="{{$name}}">
                                </div>
                            </div>
                            <div class="form-group row">
                                <label class="col-md-3 col-form-label" for="phone_number">{{ trans('labels.phone_number') }}</label>
                                <div class="col-md-9">
                                    <input type="text" id="phone_number" name="phone_number" class="form-control phone_number" placeholder="Phone Number" value="{{$phone_number}}">
                                </div>
                            </div>
                            <div class="form-group row">
                                <label class="col-md-3 col-form-label" for="address">{{ trans('labels.address') }}</label>
                                <div class="col-md-9">
                                    <textarea id="address" name="address" class="form-control address">{{$address}}</textarea>
                                </div>
                            </div>
                        </form>
                        <div class="col-lg-6 offset-6">
                            @if(GeneralFunctions::check_add_permission('supplier_form') || GeneralFunctions::check_edit_permission('supplier_form'))
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
                var data = $('.supplier_save_form').serialize();
                var list = '';
                $.ajax({
                    type: 'POST',
                    url: "{{ url('/admin/supplier/record/validation') }}",
                    data: data,
                    success: function (data) {
                        if (data.status == 'success') {
                            $('.supplier_save_form').submit()[0];
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
