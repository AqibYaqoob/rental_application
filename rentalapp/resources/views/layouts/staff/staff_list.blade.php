@extends( Auth::user()->Roles == 2 ? 'company_portal.layouts.app' :  (Auth::user()->Roles == 1 ? 'super_admin_portal.layouts.app' : 'partners_portal.layouts.app'))
@section('content')
  @php
    $counter = 1;
    if(Auth::user()->Roles == 1){
        $url = '/admin/staff/roles';
        $delete_url_path = '/admin/delete_staff/';
    }
    else if(Auth::user()->Roles == 2){
        $url = '/admin/company/staff/roles';
        $delete_url_path = '/admin/company/delete_staff/';
    }
    else{
        $url = '/admin/company/staff/roles';
    }
  @endphp
  <ol class="breadcrumb">
    <li class="breadcrumb-item">Home</li>
    <li class="breadcrumb-item"><a href="#">Admin</a></li>
    <li class="breadcrumb-item active">Staff List</li>
    <!-- Breadcrumb Menu-->
    <li class="breadcrumb-menu d-md-down-none">
      <div class="btn-group" role="group" aria-label="Button group with nested dropdown">
        <a class="btn" href="{{ url('admin/company/staff/member/list')}}"><i class="icon-graph"></i> &nbsp;Staff List</a>
      </div>
    </li>
  </ol>
  <!-- Main Content of the Page -->
  <div class="container-fluid">
    @include('errors.flash_message')
    <div class="row">
      <div class="col-lg-12">
        <div class="card">
          <div class="card-header">
            <i class="fa fa-align-justify"></i> {{ trans('labels.staff_list') }}
          </div>
          <div class="card-body">
            <table class="table table-responsive-sm table-bordered table-striped table-sm">
              <thead>
              <tr>
                <th>#</th>
                <th>{{ trans('labels.username') }}</th>
                <th>{{ trans('labels.full_name') }}</th>
                <th>{{ trans('labels.date_registered') }}</th>
                <th>{{ trans('labels.role_description') }}</th>
                <th>{{ trans('labels.status') }}</th>
                <th>{{ trans('labels.action') }}</th>
              </tr>
              </thead>
              <tbody>
              @if(isset($staff) && count($staff)>0)
                @foreach($staff as $member)
                  <tr>
                    <td>{{$counter}}</td>
                    <td>{{$member['Username']}}</td>
                    <td>
                      {{ucwords($member['staff_members']['staff_name'])}}
                    </td>
                    <td>{!! GeneralFunctions::convertToDateTimeToString($member['created_at']) !!}</td>
                    <td>
                      {{ucwords($member['staff_members']['user_roles']['description'])}}
                    </td>
                    <td>
                      @if($member['AccountStatus'] == 0)
                        <span class="badge badge-danger">{{ trans('labels.inactive') }}</span>
                      @else
                        <span class="badge badge-success">{{ trans('labels.active') }}</span>
                      @endif
                    </td>
                    <td>
                      @php
                        if(Auth::user()->Roles == 1){
                            $edit_url_path = '/admin/staff/form?id='.GeneralFunctions::encryptString($member['id']);
                        }
                        elseif(Auth::user()->role_id == 2){
                            $edit_url_path = '/admin/company/staff/form?id='.GeneralFunctions::encryptString($member['id']);
                        }
                        else{
                            $edit_url_path = '/admin/company/staff/form?id='.GeneralFunctions::encryptString($member['id']);
                        }
                      @endphp
                      <div class="btn-group" role="group">
                        <button id="btnGroupDrop1" type="button" class="btn btn-secondary btn-sm dropdown-toggle" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
                          {{ trans('labels.action') }}
                        </button>
                        <div class="dropdown-menu" aria-labelledby="btnGroupDrop1">
                          <a href="{{url($edit_url_path)}}" class="dropdown-item"><i class="fa fa-edit"></i>&nbsp; {{ trans('labels.edit') }}</a>
                          <a href="javascript:void(0)" id="{{$member['id']}}" class="dropdown-item delete_btn"><i class="fa fa-trash"></i>&nbsp; {{ trans('labels.delete') }}</a>
                        </div>
                      </div>
                    </td>
                  </tr>
                @endforeach
              @endif
              </tbody>
            </table>
          </div>
        </div>
      </div>
      <!--/.col-->
    </div>
  </div>
  <!-- Popup For Deletion -->
  <div class="remodal" data-remodal-id="delete_modal" data-remodal-options="hashTracking: false, closeOnOutsideClick: false">
    <button data-remodal-action="close" class="remodal-close"></button>
    <h1>{{ trans('labels.alert_info_box') }}</h1>
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