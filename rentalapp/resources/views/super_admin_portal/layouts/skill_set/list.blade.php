@extends('super_admin_portal.layouts.app')
@section('content')
    @php
        $delete_url_path = '/admin/skills/delete/';
    @endphp
    <ol class="breadcrumb">
        <li class="breadcrumb-item"><a href="{{ url('admin/dashboard')}}">{{ trans('labels.dashboard') }}</a></li>
        <li class="breadcrumb-item active">{{ trans('labels.skills_set_list') }}</li>
        <!-- Breadcrumb Menu-->
    </ol>
    <!-- Main Content of the Page -->
    <div class="container-fluid">
        @include('errors.flash_message')
        <div class="row">
            <div class="col-lg-12">
                <div class="card">
                    <div class="card-header">
                        <i class="fa fa-align-justify"></i> {{ trans('labels.skills_set_list') }}
                    </div>
                    <div class="card-body">
                        <table class="table table-responsive-sm table-bordered table-striped table-sm">
                            <thead>
                            <tr>
                                <th>{{ trans('labels.skill_name') }}</th>
                                <th>{{ trans('labels.skill_description') }}</th>
                                <th>{{ trans('labels.created_date') }}</th>
                                <th>{{ trans('labels.action') }}</th>
                            </tr>
                            </thead>
                            <tbody>
                            @foreach($record as $key => $value)
                                <tr>
                                    <td>{{$value['skill_name']}}</td>
                                    <td>{{$value['skill_description']}}</td>
                                    <td>{!! GeneralFunctions::convertToDateTimeToString($value['created_at']) !!}</td>
                                    <td>
                                        @php
                                            $edit_url_path = '/admin/skills?id='.GeneralFunctions::encryptString($value['id']);
                                        @endphp
                                        <div class="btn-group" role="group">
                                            <button id="btnGroupDrop1" type="button"
                                                    class="btn btn-secondary btn-sm dropdown-toggle"
                                                    data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
                                                Action
                                            </button>
                                            <div class="dropdown-menu" aria-labelledby="btnGroupDrop1">
                                                @if(GeneralFunctions::check_edit_permission('skills_list'))
                                                    <a href="{{url($edit_url_path)}}" class="dropdown-item"><i class="fa fa-edit"></i> {{ trans('labels.edit') }}</a>
                                                @endif
                                                @if(GeneralFunctions::check_delete_permission('skills_list'))
                                                    <a href="javascript:void(0)" class="dropdown-item delete_btn" id="{{$value['id']}}"><i class="fa fa-trash"></i> {{ trans('labels.delete') }}</a>
                                                @endif
                                            </div>
                                        </div>
                                    </td>
                                </tr>
                            @endforeach
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
            <!--/.col-->
        </div>
    </div>

    <!-- Modal for Deletion of the Record -->
    <div class="remodal" data-remodal-id="delete_modal"
         data-remodal-options="hashTracking: false, closeOnOutsideClick: false">
        <button data-remodal-action="close" class="remodal-close"></button>
        <h1>{{ trans('labels.remove_skills') }}</h1>
        <p>
            {{ trans('labels.a.y.s.y.w.t.d.t.r') }}
        </p>
        <form id="delete_form" action="{{ url($delete_url_path) }}" method="POST">
            <input type="hidden" name="record_uuid" id="remodal_record_uuid">
            {{ csrf_field() }}
        </form>
        <br>
        <button data-remodal-action="cancel" class="remodal-cancel">{{ trans('labels.cancel') }}</button>
        <button data-remodal-action="confirm" class="remodal-confirm">{{ trans('labels.ok') }}</button>
    </div>
@endsection
@section('js')
    <script type="text/javascript">
        $(document).ready(function () {
            var record_uuid;
            $(document).on('click', '.delete_btn', function () {
                record_uuid = $(this).attr('id');
                $('#remodal_record_uuid').val(record_uuid);
                var inst = $('[data-remodal-id=delete_modal]').remodal();
                inst.open();
            });
            $(document).on('confirmation', '.remodal', function () {
                $('#delete_form').submit()[0];
            });
        });
    </script>
@endsection
