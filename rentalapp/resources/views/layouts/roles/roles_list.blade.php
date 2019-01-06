@extends( Auth::user()->Roles == 2 ? 'company_portal.layouts.app' :  (Auth::user()->Roles == 1 ? 'super_admin_portal.layouts.app' : 'partners_portal.layouts.app'))
@section('content')
    @php
        if(Auth::user()->Roles == 1){
          $delete_url_path = '/admin/delete_role';
        }
        elseif(Auth::user()->Roles == 2){
          $delete_url_path = '/admin/company/delete_role';
        }
        else{
          $delete_url_path = '/admin/company/delete_role';
        }
    @endphp
    <ol class="breadcrumb">
        <li class="breadcrumb-item">{{trans('labels.home')}}</li>
        <li class="breadcrumb-item"><a href="#">{{trans('labels.admin')}}</a></li>
        <li class="breadcrumb-item active">{{trans('labels.admin')}} Roles List</li>
        <!-- Breadcrumb Menu-->
        <li class="breadcrumb-menu d-md-down-none">
            <div class="btn-group" role="group" aria-label="Button group with nested dropdown">
                <a class="btn" href="{{ url('admin/company/roles_list')}}"><i class="icon-graph"></i> &nbsp;Roles List</a>
            </div>
        </li>
    </ol>
    <div class="container-fluid">
        @include('errors.flash_message')
        <div class="row">
            <div class="col-lg-12">
                <div class="card">
                    <div class="card-header">
                        <i class="fa fa-align-justify"></i> {{ trans('labels.roles_list') }}
                    </div>
                    <div class="card-body">
                        <div class="card">
                            <div class="card-header">
                            </div>
                            <div class="card-body">
                                <table class="table table-responsive-sm table-bordered table-striped table-sm">
                                    <thead>
                                    <tr>
                                        <th>#</th>
                                        <th>{{ trans('labels.name') }}</th>
                                        <th>{{ trans('labels.created_at') }}</th>
                                        <th>{{ trans('labels.action') }}</th>
                                    </tr>
                                    </thead>
                                    <tbody>
                                    @foreach($roles as $key => $role)
                                        <tr>
                                            <td>{{$role['id']}}</td>
                                            <td>
                                                {{ucwords($role['name'])}}
                                            </td>
                                            <td>{!! GeneralFunctions::convertToDateTimeToString($role['created_at']) !!}</td>
                                            <td>
                                                @php
                                                    if(Auth::user()->Roles == 1){
                                                      $edit_url_path = '/admin/roles_form?id='.GeneralFunctions::encryptString($role['id']);
                                                    }
                                                    elseif(Auth::user()->Roles == 2){
                                                      $edit_url_path = '/admin/company/roles_form?id='.GeneralFunctions::encryptString($role['id']);
                                                    }
                                                    else{
                                                      $edit_url_path = '/admin/partners/roles_form?id='.GeneralFunctions::encryptString($role['id']);
                                                    }
                                                @endphp
                                                <div class="btn-group" role="group">
                                                    <button id="btnGroupDrop1" type="button" class="btn btn-secondary btn-sm dropdown-toggle" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
                                                        Action
                                                    </button>
                                                    <div class="dropdown-menu" aria-labelledby="btnGroupDrop1">
                                                        @if(\App\Helpers\GeneralFunctions::check_edit_permission('edit.role'))
                                                            <a href="{{url($edit_url_path)}}" class="dropdown-item"><i class="fa fa-edit"></i>&nbsp; {{ trans('labels.edit') }}</a>
                                                        @endif
                                                        @if(\App\Helpers\GeneralFunctions::check_delete_permission('edit.role'))
                                                            <a href="javascript:void(0)" id="{{$role['id']}}" class="dropdown-item delete_btn"><i class="fa fa-trash"></i>&nbsp; {{ trans('labels.delete') }}</a>
                                                        @endif
                                                    </div>
                                                </div>
                                            </td>
                                        </tr>
                                    @endforeach
                                    </tbody>
                                </table>
                            </div>
                            <div class="card-footer">
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <div class="remodal" data-remodal-id="delete_modal" data-remodal-options="hashTracking: false, closeOnOutsideClick: false">
        <button data-remodal-action="close" class="remodal-close"></button>
        <h1>{{ trans('labels.remove_role') }}</h1>
        <p>
            {{ trans('labels.a.y.s.y.w.t.d.t.r') }}
        </p>
        <form id="delete_form" action="{{ url($delete_url_path) }}" method="POST">
            <input type="hidden" name="record_uuid" id="remodal_record_uuid">
            {{ csrf_field() }}
        </form>
        <br>
        <button data-remodal-action="cancel" class="remodal-cancel">Cancel</button>
        <button data-remodal-action="confirm" class="remodal-confirm">OK</button>
    </div>
@endsection
@section('js')
    <script type="text/javascript">
        $(document).ready(function(){
            var record_uuid;
            $(document).on('click', '.delete_btn', function(){
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
