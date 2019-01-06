@extends( Auth::user()->Roles == 2 ? 'company_portal.layouts.app' :  (Auth::user()->Roles == 1 ? 'super_admin_portal.layouts.app' : 'partners_portal.layouts.app'))
@section('content')
  <ol class="breadcrumb">
    <li class="breadcrumb-item">Home</li>
    <li class="breadcrumb-item"><a href="#">Admin</a></li>
    <li class="breadcrumb-item active">Audit Trail</li>
    <!-- Breadcrumb Menu-->
    <li class="breadcrumb-menu d-md-down-none">
      <div class="btn-group" role="group" aria-label="Button group with nested dropdown">
        @if(Auth::user()->Roles == 1)
          <a class="btn" href="{{ url('admin/audit_trail')}}"><i class="icon-graph"></i> &nbsp;Auditing List</a>
        @else
          <a class="btn" href="{{ url('admin/company/audit_trail')}}"><i class="icon-graph"></i> &nbsp;Auditing List</a>
        @endif
      </div>
    </li>
  </ol>
  <!-- Main Content of the Page -->
  <div class="container-fluid">
    <div class="row">
      <div class="col-lg-12">
        <div class="card">
          <div class="card-header">
            <i class="fa fa-align-justify"></i> Audit List
          </div>
          <div class="card-body">
            <div>
              <table class="table table-responsive table-bordered table-striped table-sm audit_table">
              <thead>
              <tr>
                <th>User Name</th>
                <th>Action Taken</th>
                <th>Auditable Type</th>
                <th>Old Value</th>
                <th>New Value</th>
                <th>IP Address</th>
                <th>Date</th>
                <th>User Agent</th>
              </tr>
              </thead>
              <tbody>
              @if(isset($audit_details) && count($audit_details)>0)
                @foreach($audit_details as $record)
                  <tr>
                    <td>{{$record['user']['Username']}}</td>
                    <td>
                      {{$record['event']}}
                    </td>
                    <td>
                      {{$record['auditable_type']}}
                    </td>
                    <td>
                      {{$record['old_values']}}
                    </td>
                    <td>
                      {{$record['new_values']}}
                    </td>
                    <td>
                      {{$record['ip_address']}}
                    </td>
                    <td>{!! GeneralFunctions::convertToDateTimeToString($record['created_at']) !!}</td>
                    <td>
                      {{$record['user_agent']}}
                    </td>
                  </tr>
                @endforeach
              @endif
              </tbody>
            </table>
            </div>
          </div>
        </div>
      </div>
      <!--/.col-->
    </div>
  </div>
@endsection
