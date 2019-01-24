@extends('super_admin_portal.layouts.app')
@section('content')
    @php
      $payment_name = null;
      $description = null;
        if(isset($payments_details)){
            $payment_name = $packages_details['payment_name'];
            $description = $packages_details['description'];
        }
    @endphp
    <ol class="breadcrumb">
        <li class="breadcrumb-item"><a href="{{ url('admin/dashboard')}}">{{ trans('labels.dashboard') }}</a></li>
        <li class="breadcrumb-item active">{{ trans('labels.payments') }}</li>
    </ol>

    <div class="container-fluid">
        <div class="row">
            @include('errors.flash_message')
        </div>
        <div class="row">
            <div class="col-lg-12">
                <div class="card">
                    <div class="card-header">
                        <i class="fa fa-align-justify"></i> {{ trans('labels.payments') }}
                    </div>
                    <div class="card-body">
                        <form action="{{url('/admin/payments/save/record')}}" method="post"
                              class="form-horizontal payments_save_form">
                            <div class="form-group row">
                                <label class="col-md-3 col-form-label" for="payment_name">{{ trans('labels.payment_name') }}</label>
                                <div class="col-md-9">
                                    {{ csrf_field() }}
                                    <input type="hidden" name="id" value="{{Request::input('id')}}">
                                    <input type="text" id="payment_name" name="payment_name" class="form-control payment_name" placeholder="Payment Name" value="{{$payment_name}}">
                                </div>
                            </div>
                            <div class="form-group row">
                                <label class="col-md-3 col-form-label" for="description">{{ trans('labels.description') }}</label>
                                <div class="col-md-9">
                                     <textarea id="description" name="description" class="form-control description">{{$description}}</textarea>
                                </div>
                            </div>
                        </form>
                        <div class="col-lg-6 offset-6">
                            @if(GeneralFunctions::check_add_permission('payments_form') || GeneralFunctions::check_edit_permission('payments_form'))
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
                var data = $('.payments_save_form').serialize();
                var list = '';
                $.ajax({
                    type: 'POST',
                    url: "{{ url('/admin/payments/record/validation') }}",
                    data: data,
                    success: function (data) {
                        if (data.status == 'success') {
                            $('.payments_save_form').submit()[0];
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
