@extends('super_admin_portal.layouts.app')
@section('content')
    @php
      $product_name = '';
      $category = '';
        if(isset($raw_item_details)){
            $product_name = $raw_item_details['product_name'];
            $category = $raw_item_details['category'];
        }
    @endphp
    <ol class="breadcrumb">
        <li class="breadcrumb-item"><a href="{{ url('admin/dashboard')}}">{{ trans('labels.dashboard') }}</a></li>
        <li class="breadcrumb-item active">{{ trans('labels.raw_items') }}</li>
    </ol>

    <div class="container-fluid">
        <div class="row">
            @include('errors.flash_message')
        </div>
        <div class="row">
            <div class="col-lg-12">
                <div class="card">
                    <div class="card-header">
                        <i class="fa fa-align-justify"></i> {{ trans('labels.raw_items') }}
                    </div>
                    <div class="card-body">
                        <form action="{{url('/admin/raw_item/save/record')}}" method="post"
                              class="form-horizontal raw_item_save_form">
                            <div class="form-group row">
                                <label class="col-md-3 col-form-label" for="product_name">{{ trans('labels.product_name') }}</label>
                                <div class="col-md-9">
                                    {{ csrf_field() }}
                                    <input type="hidden" name="id" value="{{Request::input('id')}}">
                                    <input type="text" id="product_name" name="product_name" class="form-control product_name" placeholder="Item Name" value="{{$product_name}}">
                                </div>
                            </div>
                            <div class="form-group row">
                                <label class="col-md-3 col-form-label" for="category">{{ trans('labels.item_category') }}</label>
                                <div class="col-md-9">
                                    <select class="form-control" name="category">
                                        <option value="">{{ trans('labels.choose_option') }}</option>
                                        @foreach($category_record as $key => $value)
                                            <option value="{{$value['id']}}" {!! $category ? 'selected' : ''!!}>{{$value['category_name']}}</option>
                                        @endforeach
                                    </select>
                                </div>
                            </div>
                        </form>
                        <div class="col-lg-6 offset-6">
                            @if(GeneralFunctions::check_add_permission('raw_item_form') || GeneralFunctions::check_edit_permission('raw_item_form'))
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
                var data = $('.raw_item_save_form').serialize();
                var list = '';
                $.ajax({
                    type: 'POST',
                    url: "{{ url('/admin/raw_item/record/validation') }}",
                    data: data,
                    success: function (data) {
                        if (data.status == 'success') {
                            $('.raw_item_save_form').submit()[0];
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
