@extends('super_admin_portal.layouts.app')
@section('content')
    @php
      $skill_name = '';
      $skill_description = '';
        if(isset($skills_details)){
            $skill_name = $skills_details['skill_name'];
            $skill_description = $skills_details['skill_description'];
        }
    @endphp
    <ol class="breadcrumb">
        <li class="breadcrumb-item"><a href="{{ url('admin/dashboard')}}">{{ trans('labels.dashboard') }}</a></li>
        <li class="breadcrumb-item active">{{ trans('labels.skills') }}</li>
    </ol>

    <div class="container-fluid">
        <div class="row">
            @include('errors.flash_message')
        </div>
        <div class="row">
            <div class="col-lg-12">
                <div class="card">
                    <div class="card-header">
                        <i class="fa fa-align-justify"></i> {{ trans('labels.skills') }}
                    </div>
                    <div class="card-body">
                        <form action="{{url('/admin/skills/save/record')}}" method="post"
                              class="form-horizontal skills_save_form">
                            <div class="form-group row">
                                <label class="col-md-3 col-form-label" for="skill_name">{{ trans('labels.skill_name') }}</label>
                                <div class="col-md-9">
                                    {{ csrf_field() }}
                                    <input type="hidden" name="id" value="{{Request::input('id')}}">
                                    <input type="text" id="skill_name" name="skill_name" class="form-control skill_name" placeholder="Skill Name" value="{{$skill_name}}">
                                </div>
                            </div>
                            <div class="form-group row">
                                <label class="col-md-3 col-form-label" for="skill_description">{{ trans('labels.skill_description') }}</label>
                                <div class="col-md-9">
                                    <textarea id="skill_description" name="skill_description" class="form-control skill_description">{{$skill_description}}</textarea>
                                </div>
                            </div>
                        </form>
                        <div class="col-lg-6 offset-6">
                            @if(GeneralFunctions::check_add_permission('skills_form') || GeneralFunctions::check_edit_permission('skills_form'))
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
                var data = $('.skills_save_form').serialize();
                var list = '';
                $.ajax({
                    type: 'POST',
                    url: "{{ url('/admin/skills/record/validation') }}",
                    data: data,
                    success: function (data) {
                        if (data.status == 'success') {
                            $('.skills_save_form').submit()[0];
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
