@extends( Auth::user()->Roles == 2 ? 'company_portal.layouts.app' :  (Auth::user()->Roles == 1 ? 'super_admin_portal.layouts.app' : 'partner_portal.layout.app'))
@section('content')
    @php
        $fullName = '';
        $mobileNumber = '';
        $homeNumber = '';
        $userName = '';
        $userEmail = '';
        if(isset($profile_details) && count($profile_details) > 0){
            $userName = $profile_details['Username'];
            $userEmail = $profile_details['email'];
            if($profile_details['staff_members'] != null){
                $fullName = $profile_details['staff_members']['staff_name'];
                $mobileNumber = $profile_details['staff_members']['mobile_number'];
                $homeNumber = $profile_details['staff_members']['home_number'];
            }
        }
    @endphp
    <ol class="breadcrumb">
        <li class="breadcrumb-item">Home</li>
        <li class="breadcrumb-item"><a href="#">Admin</a></li>
        <li class="breadcrumb-item active">Profile Setting</li>
        <!-- Breadcrumb Menu-->
        <li class="breadcrumb-menu d-md-down-none">
            <div class="btn-group" role="group" aria-label="Button group with nested dropdown">
                <a class="btn" href="{{ url('admin/company/staff_form')}}"><i class="icon-graph"></i> &nbsp;Profile Setting</a>
            </div>
        </li>
    </ol>

    <div class="container-fluid">
        @if(session('error'))
            <div class="alert alert-danger">
                {{session('error')}}
            </div>
        @endif
        <div class="row">
            @include('errors.flash_message')
            @if(\Session::has('error'))
                <div class="alert alert-danger">
                    <button type="button" class="close" data-dismiss="alert" aria-label="Close">
                        <span aria-hidden="true">&times;</span>
                    </button>
                    <p>{{ \Session::get('error')}}</p>
                </div>
            @endif
        </div>
        <div class="row">
            <div class="col-lg-6">
                <div class="card">
                    <div class="card-header">
                        <i class="fa fa-align-justify"></i> Contact Information
                    </div>
                    <div class="card-body">
                        <div class="card">
                            <div class="card-header">
                            </div>
                            <div class="card-body">
                                <form action="" method="post" class="form-horizontal contact_info_form">
                                    <div class="form-group row">
                                        <label class="col-md-3 col-form-label" for="p-full_name">Full Name</label>
                                        <div class="col-md-9">
                                            {{ csrf_field() }}
                                            <input type="text" id="p-full_name" name="p-full_name" class="form-control p-full_name" placeholder="Full Name" value="{{$fullName}}">
                                        </div>
                                    </div>
                                    <div class="form-group row">
                                        <label class="col-md-3 col-form-label" for="p-mobile_number">Mobile Number</label>
                                        <div class="col-md-9">
                                            <input type="text" id="p-mobile_number" name="p-mobile_number" class="form-control p-mobile_number" placeholder="Mobile Number" value="{{$mobileNumber}}">
                                        </div>
                                    </div>
                                    <div class="form-group row">
                                        <label class="col-md-3 col-form-label" for="p-mobile_number">Home Number</label>
                                        <div class="col-md-9">
                                            <input type="text" id="p-home_number" name="p-home_number" class="form-control p-home_number" placeholder="Home Number" value="{{$homeNumber}}">
                                        </div>
                                    </div>
                                    <div class="form-group row">
                                        <label class="col-md-3 col-form-label"></label>
                                        <div class="col-md-9">
                                        </div>
                                    </div>
                                </form>
                            </div>
                            <div class="card-footer">
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            <div class="col-lg-6">
                <div class="card">
                    <div class="card-header">
                        <i class="fa fa-align-justify"></i> Authentication Information
                    </div>
                    <div class="card-body">
                        <div class="card">
                            <div class="card-header">
                            </div>
                            <div class="card-body">
                                <form action="" method="post" class="form-horizontal auth_info_form">
                                    <div class="form-group row">
                                        <label class="col-md-3 col-form-label" for="p-user_name">Username</label>
                                        <div class="col-md-9">
                                            <input type="text" id="p-user_name" name="p-user_name" class="form-control p-user_name" placeholder="Username" value="{{$userName}}">
                                        </div>
                                    </div>
                                    <div class="form-group row">
                                        <label class="col-md-3 col-form-label" for="p-email">Email</label>
                                        <div class="col-md-9">
                                            <input type="email" id="p-email" name="p-email" class="form-control p-email" placeholder="Email Address" value="{{$userEmail}}">
                                        </div>
                                    </div>
                                    <div class="form-group row">
                                        <label class="col-md-3 col-form-label"></label>
                                        <div class="col-md-9">
                                            <!-- <a href="javascript:void(0)" class="btn btn-primary">Save</a>
                                            <a href="javascript:void(0)" class="btn btn-warning">Change Password</a> -->
                                            <a href="javascript:void(0)" class="alert-link change">Change Password</a>
                                        </div>
                                    </div>
                                </form>
                            </div>
                            <div class="card-footer">
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            <div class="col-lg-6">
                <img src="{{ url('img/loading.gif') }}" class="loading_gif" style="height: 26px !important; display: none;">
                <a href="javascript:void(0)" class="btn btn-primary save_profile_record">Save</a>
                <!-- <a href="javascript:void(0)" class="btn btn-warning">Change Password</a> -->
            </div>
        </div>
    </div>
    {{--Modal--}}
    <div class="remodal" data-remodal-id="delete_modal" data-remodal-options="hashTracking: false, closeOnOutsideClick: false">
        <button data-remodal-action="close" class="remodal-close"></button>
        <h1>Change Password</h1>
        {{--<p>
            Are You Sure to delete it
        </p>--}}
        @php
        $user = \Illuminate\Support\Facades\Auth::user();
        if($user->Roles == 1)
        $route = 'admin.change.password';
        elseif($user->Roles == 2)
        $route = 'company.change.password';
        elseif($user->Roles == 3)
        $route = 'partner.change.password';
        @endphp
        <form id="state_form" action="{{ route($route) }}" method="POST" class="card-body">
            <input type="hidden" id="data-id" name="id" value="{{\Illuminate\Support\Facades\Auth::user()->id}}"/>
            {{ csrf_field() }}
            <div class="form-group row">
                <div class="col-md-3">
                    <label>Old Password</label>
                </div>
                <div class="col-md-8">
                    <div class="input-group eye" id="show_hide_password">
                        <input class="form-control" type="password" name="old_password">
                        <div class="input-group-addon">
                            <a href=""><i class="fa fa-eye-slash" aria-hidden="true"></i></a>
                        </div>
                    </div>
                </div>
            </div>

            <div class="form-group row">
                <div class="col-md-3">
                    <label>New Password</label>
                </div>
                <div class="col-md-8">
                    <div class="input-group eye" id="show_hide_password">
                        <input class="form-control" type="password" name="password">
                        <div class="input-group-addon">
                            <a href=""><i class="fa fa-eye-slash" aria-hidden="true"></i></a>
                        </div>
                    </div>
                </div>
            </div>
            <div class="form-group row">
                <div class="col-md-3">
                    <label>Confirm Password</label>
                </div>
                <div class="col-md-8">
                    <div class="input-group eye" id="show_hide_password">
                        <input class="form-control" type="password" name="password_confirmation">
                        <div class="input-group-addon">
                            <a href=""><i class="fa fa-eye-slash" aria-hidden="true"></i></a>
                        </div>
                    </div>
                </div>
            </div>
        </form>
        <br>
        <div class="row">
            <div class="col-md-8 offset-3">
                <button data-remodal-action="cancel" class="remodal-cancel">Cancel</button>
                <button data-remodal-action="confirm" class="remodal-confirm">OK</button>
            </div>
        </div>
    </div>
@endsection
@section('js')
    <script type="text/javascript">
        $(document).ready(function(){
            var url = '';
            @if(Auth::user()->Roles == 1)
                url = "{{url('/admin/profile/update')}}";
            @elseif(Auth::user()->Roles == 2)
                url = "{{url('/admin/company/profile/update')}}";
            @else
                url = "{{url('/admin/partner/profile/update')}}";
            @endif
            $(document).on('click', '.save_profile_record', function(){
                $('.loading_gif').show();
                var contact_info_form = $('.contact_info_form').serialize();
                var auth_info_form = $('.auth_info_form').serialize();
                var data = contact_info_form+'&'+auth_info_form;
                var list = '';
                $.ajax({
                    type: 'POST',
                    url: url,
                    data: data,
                    success:function(data){
                        if(data.status == 'success'){
                            list = list +'<li>Your Profile is Updated</li>';
                            $('#msg-list').html(list);
                            $('.msg-box').removeClass("alert-danger").addClass("alert-success").show();
                        }
                        else{
                            var errorArray = data.msg_data;
                            errorArray.forEach(function(e){
                                list = list +'<li>'+e+'</li>';
                            });

                            $('#msg-list').html(list);
                            $('.msg-box').removeClass("alert-success").addClass("alert-danger").show();
                        }
                        $("html, .container").animate({ scrollTop: 0 }, 600);
                        $('.loading_gif').hide();
                    }
                });
            });
            // change password
            $(document).on('click', '.change', function(){
                var id = $(this).attr('data-id');
                $('#data-id').val(id);
                var inst = $('[data-remodal-id=delete_modal]').remodal();
                inst.open();
            });
            $(document).on('confirmation', '.remodal', function () {
                $('#state_form').submit()[0];
            });
            // view password (eye)
            $("#show_hide_password a").on('click', function(event) {
                event.preventDefault();
                var element = $(this).parent().closest('.input-group').find('input');
                if(element.attr("type") == "text"){
                    element.attr('type', 'password');
                    $(this).closest('i').addClass( "fa-eye-slash" );
                    $(this).closest('i').removeClass( "fa-eye" );
                }
                else if(element.attr("type") == "password"){
                    element.attr('type', 'text');
                    $(this).closest('i').removeClass( "fa-eye-slash" );
                    $(this).closest('i').addClass( "fa-eye" );
                }
            });
        });
    </script>
@endsection
