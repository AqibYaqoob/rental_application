
@extends( Auth::user()->Roles == 2 ? 'company_portal.layouts.app' :  (Auth::user()->Roles == 1 ? 'super_admin_portal.layouts.app' : 'partners_portal.layouts.app'))
@section('content')
<!-- Add user section starts from here -->
@php
$name = '';
$description = '';

if(count($role_info) > 0){
    $name = $role_info['name'];
    $description = $role_info['description'];
}
@endphp
<ol class="breadcrumb">
    <li class="breadcrumb-item">{{trans('labels.home')}}</li>
    <li class="breadcrumb-item"><a href="#">{{trans('labels.admin')}}</a></li>
    <li class="breadcrumb-item active">{{trans('labels.staff_roles')}}</li>
    <!-- Breadcrumb Menu-->
    <li class="breadcrumb-menu d-md-down-none">
      <div class="btn-group" role="group" aria-label="Button group with nested dropdown">
        <a class="btn" href="{{ url('admin/company/roles_form')}}"><i class="icon-graph"></i> &nbsp;{{trans('labels.staff_roles')}}</a>
      </div>
    </li>
</ol>
<!-- Main Content of the Page -->
<div class="container-fluid">
<div class="row">
    <div class="col-lg-12">
      <div class="card">
        <div class="card-header">
          <i class="fa fa-align-justify"></i> {{trans('labels.add_staff_roles')}}
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
                    <div class="col-sm-12">
                        <div class="card-box add_staff add_user_role">
                            @if(Auth::user()->Roles == 1)
                                <form id="permission_form" action="{{url('/admin/add/role_permissions')}}" method="POST">
                            @elseif(Auth::user()->Roles == 2)
                                <form id="permission_form" action="{{url('/admin/company/add/role_permissions')}}" method="POST">
                            @else
                                <form id="permission_form" action="{{url('/admin/company/add/role_permissions')}}" method="POST">
                            @endif
                                <div class="row">
                                    <div class="col-sm-4">
                                        <div class="form-group">
                                            <label class="form_label" for="userName">{{trans('labels.name')}} </label>
                                            <input type="hidden" name="id" value="{{Request::input('id')}}">
                                            <input name="name" placeholder="{{trans('labels.name')}}" class="form-control" type="text" value="{{$name}}">
                                            <input type="hidden" id="csrf_token" name="_token" value="{{ csrf_token() }}">
                                        </div>
                                    </div>
                                </div>
                                <div class="row">
                                    <div class="col-sm-4">
                                        <div class="form-group">
                                            <label class="form_label" for="userName">{{trans('labels.description')}} </label>
                                            <textarea name="description" placeholder="{{trans('labels.description')}}" class="form-control" type="text">{{$description}}</textarea>
                                        </div>
                                    </div>
                                </div>
                                <div class="row">
                                    <div class="col-sm-12">
                                        <div class="bottom_border">
                                            <h6 class="m-t-0 m-b-10 font-13"><b>{{trans('labels.select_features_for_enable')}}</b></h6>
                                        </div>
                                    </div>
                                </div>
                                <div class="row">
                                        <div class="col-md-8">
                                            <div class="bottom_border add_user_role_detail m-b-10">
                                                <p class="m-b-10 text-muted font-13">{{trans('labels.listing_portal_screens')}}</p>
                                                <div class="card-box add_user_role_box m-b-10">
                                                    <dl class="row bottom_border m-b-10">
                                                        <dt class="col-sm-2">{{trans('labels.add')}}</dt>
                                                        <dt class="col-sm-2">{{trans('labels.view')}}</dt>
                                                        <dt class="col-sm-2">{{trans('labels.edit')}}</dt>
                                                        <dt class="col-sm-2">{{trans('labels.delete')}}</dt>
                                                         <dt class="col-sm-2">{{trans('labels.screens')}}</dt>
                                                    </dl>
                                                    @php $i=0; @endphp
                                                    @foreach($screens as $key => $value)
                                                    <dl class="row bottom_border m-b-10">
                                                        <dt class="col-sm-2">
                                                            <span class=" m-b-0 m-t-10">
                                                                <div class="checkbox checkbox-success checkbox-single">
                                                                    <input id="leasing.{{$i}}" type="checkbox" name="permissions[<?php echo $value['code']; ?>][add]"
                                                                    @php
                                                                    if(isset($roles_details[$value['code']])){
                                                                        if(isset($roles_details[$value['code']]['add'])){
                                                                            echo 'checked';
                                                                        }
                                                                    }
                                                                    @endphp
                                                                    >
                                                                    <label for="leasing.{{$i}}"></label>
                                                                </div>
                                                            </span>
                                                        </dt>
                                                        <dt class="col-sm-2">
                                                            <span class=" m-b-0 m-t-10">
                                                                <div class="checkbox checkbox-success checkbox-single">
                                                                    <input id="leasing.{{$i + 1}}" type="checkbox" name="permissions[<?php echo $value['code']; ?>][view]" @php
                                                                    if(isset($roles_details[$value['code']])){
                                                                        if(isset($roles_details[$value['code']]['view'])){
                                                                            echo 'checked';
                                                                        }
                                                                    }
                                                                    @endphp
                                                                    >
                                                                    <label for="leasing.{{$i + 1}}"></label>
                                                                </div>
                                                            </span>
                                                        </dt>
                                                        <dt class="col-sm-2">
                                                            <span class=" m-b-0 m-t-10">
                                                                <div class="checkbox checkbox-success checkbox-single">
                                                                    <input id="leasing.{{$i + 2}}" type="checkbox" name="permissions[<?php echo $value['code']; ?>][edit]"
                                                                    @php
                                                                    if(isset($roles_details[$value['code']])){
                                                                        if(isset($roles_details[$value['code']]['edit'])){
                                                                            echo 'checked';
                                                                        }
                                                                    }
                                                                    @endphp
                                                                    >
                                                                    <label for="leasing.{{$i + 2}}"></label>
                                                                </div>
                                                            </span>
                                                        </dt>
                                                        <dt class="col-sm-2">
                                                            <span class=" m-b-0 m-t-10">
                                                                <div class="checkbox checkbox-success checkbox-single">
                                                                    <input id="leasing.{{$i + 3}}" type="checkbox" name="permissions[<?php echo $value['code']; ?>][delete]"
                                                                    @php
                                                                    if(isset($roles_details[$value['code']])){
                                                                        if(isset($roles_details[$value['code']]['delete'])){
                                                                            echo 'checked';
                                                                        }
                                                                    }
                                                                    @endphp
                                                                    >
                                                                    <label for="leasing.{{$i + 3}}"></label>
                                                                </div>
                                                            </span>
                                                        </dt>
                                                        <dd class="col-sm-4">{{ $value['name'] }}</dd>
                                                    </dl>
                                                    @php $i++; @endphp
                                                    @endforeach
                                                </div>
                                            </div>

                                        </div>
                                        <div class="col-sm-12">
                                            <a href="javascript:void(0)" class="btn btn-success save_button">{{trans('labels.save')}}</a>
                                            <img src="{{ URL::to('img/loading.gif') }}" class="loading_gif" style="height: 26px !important; display: none;">
                                        </div>
                                </div>
                            </form>
                        </div>
                    </div>
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
@endsection

@section('js')
<script type="text/javascript">
    $(document).ready(function(){
        @if(Auth::user()->Roles == 1)
            var url_path = "{{ url('/admin/roles/permission_validation') }}";
        @elseif(Auth::user()->Roles == 2)
            var url_path = "{{ url('/admin/company/roles/permission_validation') }}";
        @else
            var url_path = "{{ url('/admin/company/permission_validation') }}";
        @endif
        $(document).on('click', '.save_button', function(){
            data = $('#permission_form').serialize();
            $('.loading_gif').show();
            $.ajax({
                type: 'POST',
                url: url_path,
                data: data,
                success:function(data){
                    if(data.status == 'success'){
                        $('#permission_form').submit()[0];
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
        $("input[name*='[edit]'], input[name*='[add]'], input[name*='[delete]']").change(function(){
            if($(this).is(':checked') == true){
                if($(this).closest(".row").find("input[name*='[view]']").is(':checked') == false)
                {
                    $(this).closest(".row").find("input[name*='[view]']").prop('checked', true);
                }
            }
        });

        $("input[name*='[view]']").change(function(){
            if($(this).is(':checked') == false){
                $(this).closest(".row").find("input[name*='[edit]'], input[name*='[add]'], input[name*='[delete]']").prop('checked', false);
            }
        });
    })
</script>
@endsection