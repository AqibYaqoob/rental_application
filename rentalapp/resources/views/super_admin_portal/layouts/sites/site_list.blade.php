@extends('super_admin_portal.layouts.app') 
@section('content')
@php
  $delete_url_path = '/admin/site/delete/';
@endphp
  <ol class="breadcrumb">
    <li class="breadcrumb-item">Home</li>
    <li class="breadcrumb-item"><a href="#">Admin</a></li>
    <li class="breadcrumb-item active">Site List</li>
    <!-- Breadcrumb Menu-->
    <li class="breadcrumb-menu d-md-down-none">
      <div class="btn-group" role="group" aria-label="Button group with nested dropdown">
        <a class="btn" href="{{ url('admin/site/list') }}"><i class="icon-graph"></i> &nbsp;Site List</a>
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
              <i class="fa fa-align-justify"></i> Site List
            </div>
            <div class="card-body">
              <table class="table table-responsive-sm table-bordered table-striped table-sm">
                <thead>
                  <tr>
                    <th>#</th>
                    <th>Site Name</th>
                    <th>Tenant</th>
                    <th>Remarks</th>
                    <th>Created Date</th>
                    {{--<th>Action</th>--}}
                  </tr>
                </thead>
                <tbody>
                @php $count = 1; @endphp
                  @foreach($record as $key => $value)
                  <tr>
                    <td> {{ $count++ }}</td>
                    <td>{{$value['SiteName']}}</td>
                    <td>{{ $value->tenants->TenantName }}</td>
                    <td>{{$value['Remarks']}}</td>
                    <td>{{date_format(new DateTime($value['created_at']), 'jS F Y g:ia') }}</td>
                    {{--<td>
                    	@php
                        $edit_url_path = '/admin/site_form?id='.GeneralFunctions::encryptString($value['Id']);
                      @endphp
                      <div class="btn-group" role="group">
                          <button id="btnGroupDrop1" type="button" class="btn btn-secondary btn-sm dropdown-toggle" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
                              Action
                          </button>
                          <div class="dropdown-menu" aria-labelledby="btnGroupDrop1">
                              <a href="{{url($edit_url_path)}}" class="dropdown-item"><i class="fa fa-edit"></i> Edit</a>
                              <a href="javascript:void(0)" class="dropdown-item delete_btn" id="{{$value['Id']}}" ><i class="fa fa-trash"></i> Delete</a>
                          </div>
                      </div>
                    </td>--}}
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
<div class="remodal" data-remodal-id="delete_modal" data-remodal-options="hashTracking: false, closeOnOutsideClick: false">
  <button data-remodal-action="close" class="remodal-close"></button>
  <h1>Alert Info Box</h1>
  <p>
    Are you sure, you want to delete the record
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