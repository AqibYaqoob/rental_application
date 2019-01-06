@extends('company_portal.layouts.app')
@section('content')
    @php
        $name = '';
        $phone_number = '';
        $home_number = '';
        $email = '';
        $role_id = '';
        $rate = '';
        $base_currency = null;

        if(isset($record) && count($record) > 0){
            $name = $record['staff_members']['staff_name'];
            $phone_number = $record['staff_members']['mobile_number'];
            $home_number = $record['staff_members']['home_number'];
            $email = $record['EmailAddress'];
            $role_id = $record['staff_members']['role_id'];
            $rate = $record['interest_rate'];
            $base_currency = $partner_base_currency;
        }
    @endphp
    <ol class="breadcrumb">
        <li class="breadcrumb-item"><a href="{{ url('admin/company/dashboard')}}">{{ trans('labels.dashboard') }}</a></li>
        <li class="breadcrumb-item active">{{ trans('labels.add_partner') }}</li>
        <!-- Breadcrumb Menu-->
        <li class="breadcrumb-menu d-md-down-none">
            <div class="btn-group" role="group" aria-label="Button group with nested dropdown">
                <a class="btn" href="#"><i class="icon-graph"></i> &nbsp;{{ $balance . ' '. $currencyName }}</a>
            </div>
        </li>
    </ol>
    <!-- Main Content of the Page -->
    <div class="container-fluid">
        <div class="row">
            <div class="col-lg-12">
                <div class="card">
                    <div class="card-header">
                        <i class="fa fa-align-justify"></i> {{ trans('labels.add_partner') }}
                    </div>
                    <div class="card-body">
                        <div class="card">
                            <div class="card-header">
                            </div>
                            <div class="card-body">
                                <div class="msg-box alert" style="display: none;">
                                    <ul style="text-decoration: none;" id="msg-list">

                                    </ul>
                                </div>
                                @include('errors.flash_message')
                                <div class="row ">
                                    <form id="staff_form" action="{{url('admin/company/add/partner')}}" method="POST">
                                        <div class="col-sm-12">
                                            <div class="card-box add_staff ">
                                                <div class="row">

                                                    <div class="col-sm-12">
                                                        <h4 class="m-t-0 m-b-0">{{ trans('labels.contact_information') }}</h4>
                                                        <hr class="m-t-0">
                                                    </div>
                                                </div>
                                                <div class="row">

                                                    <div class="col-sm-8">
                                                        <div class="form-group">
                                                            <label class="form_label" for="userName">{{ trans('labels.name') }} (required)<span class="text-danger">*</span></label>
                                                            <input type="hidden" id="csrf_token" name="_token" value="{{ csrf_token() }}">
                                                            <input name="name" placeholder="Name" class="form-control" type="text" id="name" value="{{$name}}">
                                                        </div>
                                                    </div>
                                                </div>
                                                <div class="row">
                                                    <div class="col-sm-8">
                                                        <label class="form_label" for="companyName">{{ trans('labels.phone') }}</label>
                                                        <div class="phone_input input-group">
                                                            <span class="input-group-addon"><i class="fa fa-mobile"></i></span>
                                                            <input class="form-control"  type="text" id="phone_number" name="phone_number" value="{{$phone_number}}" placeholder="Mobile Number">
                                                        </div>
                                                        <div class=" phone_input input-group">
                                                            <span class="input-group-addon"><i class="fa fa-home"></i></span>
                                                            <input class="form-control"  type="text" name="home_number" value="{{$home_number}}" placeholder="Home Number">
                                                        </div>
                                                    </div>
                                                </div>

                                                <div class="clearfix"></div>
                                            </div>

                                            <div class="card-box add_staff ">
                                                <div class="row">

                                                    <div class="col-sm-12">
                                                        <h4 class="m-t-0 m-b-0">{{ trans('labels.partner_information') }}</h4>
                                                        <hr class="m-t-0">
                                                    </div>
                                                </div>
                                                <div class="row">
                                                    <div class="col-sm-8">
                                                        <label class="form_label" for="primaryemail">{{ trans('labels.email') }} (required)<span class="text-danger">*</span></label>
                                                        <div class=" form-group phone_input input-group">
                                                            <span class="input-group-addon"><i class="fa fa-envelope"></i></span>
                                                            <input type="hidden" name="id" value="{{Request::input('id')}}">
                                                            <input class="form-control"  type="text" name="email" value="{{$email}}">
                                                        </div>
                                                    </div>
                                                </div>
                                            </div>
                                            <div class="card-box add_staff ">
                                                <div class="row">

                                                    <div class="col-sm-12">
                                                        <h4 class="m-t-0 m-b-0">{{ trans('labels.partner_interest_rate') }}</h4>
                                                        <hr class="m-t-0">
                                                    </div>
                                                </div>
                                                <div class="row">
                                                    <div class="col-sm-8">
                                                        <label class="form_label" for="primaryemail">{{ trans('labels.rate') }} (required)<span class="text-danger">*</span></label>
                                                        <div class=" form-group phone_input input-group">
                                                            <span class="input-group-addon"><i class="fa fa-percent"></i></span>
                                                            <input class="form-control"  type="number" name="rate" value="{{$rate}}">
                                                        </div>
                                                    </div>
                                                </div>
                                            </div>
                                            <div class="card-box add_staff ">
                                                <div class="row">

                                                    <div class="col-sm-12">
                                                        <h4 class="m-t-0 m-b-0">{{ trans('labels.assign_base_currency_partner') }}</h4>
                                                        <hr class="m-t-0">
                                                    </div>
                                                </div>
                                                <div class="row">
                                                    <div class="col-sm-8">
                                                        <label class="form_label" for="primaryemail">{{ trans('labels.base_currency') }} (required)<span class="text-danger">*</span></label>
                                                        <div class=" form-group phone_input input-group">
                                                            <select class="form-control" name="assign_base_currency">
                                                                <option value="">Select Option</option>
                                                                @foreach($currency_list as $key => $value)
                                                                    <option value="{!! $value['Id'] !!}" {!! ($base_currency == $value['Id']) ? 'selected' : '' !!}>{!! $value['currencyList']['currency'].' ('.$value['currencyList']['code'].')' !!}</option>
                                                                @endforeach
                                                            </select>
                                                        </div>
                                                    </div>
                                                </div>
                                            </div>
                                            <div class="card-box add_staff m-b-10">
                                                <div class="row">

                                                    <div class="col-sm-12">
                                                        <h4 class="m-t-0 m-b-0">{{ trans('labels.inspections') }}</h4>
                                                        <hr class="m-t-0">
                                                    </div>
                                                </div>
                                                <div class="row">
                                                    <div class="col-md-12">
                                                        <p class="lead font-13 m-b-0 m-t-10">{{ trans('labels.W.y.c.s,w.w') }}:</p>
                                                        <p class="lead font-13 m-b-0 "><i class=" text-success fa fa-check"></i>
                                                            <span> {{ trans('labels.u.t.l.e.a.a.t.u') }}</span>
                                                        </p>
                                                        <p class="lead font-13 m-b-0 "><i class=" text-success fa fa-check"></i>
                                                            <span>{{ trans('labels.a.a.t.p') }}</span>
                                                        </p>
                                                        <p class="lead font-13 m-b-0 "><i class=" text-success fa fa-check"></i>
                                                            <span>{{ trans('labels.e.i.t.t.u.t.t.h.t.s.i') }}</span>
                                                        </p>
                                                    </div>
                                                </div>
                                            </div>

                                        </div>
                                        <div class="col-sm-12">
                                            @if(GeneralFunctions::check_add_permission('partner_form') || GeneralFunctions::check_edit_permission('partner_form'))
                                                <a href="javascript:void(0)" class="btn btn-success save_button">{{ trans('labels.save') }}</a>
                                                <img src="{{ URL::to('assets/images/loading.gif') }}" class="loading_gif" style="height: 26px !important; display: none;">
                                            @endif
                                        </div>
                                    </form>
                                </div>
                            </div>
                            <div class="card-footer">
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

@section('js')
    <script type="text/javascript">
        $(document).ready(function(){
            var url_path = "{{ url('/admin/company/check_partner_validations') }}";

            $(document).on('click', '.save_button', function(){
                data = $('#staff_form').serialize();
                $.ajax({
                    type: 'POST',
                    url: url_path,
                    data: data,
                    success:function(data){
                        if(data.status == 'success'){
                            $('#staff_form').submit()[0];
                        }
                        else{
                            var errorArray = data.msg_data;
                            var list = '';
                            errorArray.forEach(function(e){
                                list = list +'<li>'+e+'</li>';
                            });

                            $('#msg-list').append(list);
                            $('.msg-box').addClass("alert-danger").show();
                            $("html, .container").animate({ scrollTop: 0 }, 600);
                        }
                        $('.loading_gif').hide();
                    }
                });
            });
        })
    </script>
@endsection

<!-- end col-->


@endsection
