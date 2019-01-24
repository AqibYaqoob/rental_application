@extends('super_admin_portal.layouts.app')
@section('content')
  <ol class="breadcrumb">
      <li class="breadcrumb-item">{{trans('labels.home')}}</li>
      <li class="breadcrumb-item"><a href="#">{{trans('labels.super_admin')}}</a></li>
      <li class="breadcrumb-item active">{{trans('labels.dashboard')}}</li>
      <!-- Breadcrumb Menu-->
      <li class="breadcrumb-menu d-md-down-none">
        <div class="btn-group" role="group" aria-label="Button group with nested dropdown">
          <a class="btn" href="{{ url('admin/company/dashboard')}}"><i class="icon-graph"></i> &nbsp;{{trans('labels.dashboard')}}</a>
        </div>
      </li>
   </ol>
     <!-- Main Content of the Page -->
     @include('errors.flash_message')
     <div class="container-fluid">
       <div class="row">
              <div class="col-md-3">
                  <div class="card">
                      <div class="card-body">
                          <div class="d-flex">
                              <span class="align-self-center p-3 m-top-7">
                                  <i class="fa fa-handshake-o fa-3x"></i>
                              </span>
                              <div class="align-self-center">
                                  <h6 class="text-muted m-t-10 m-b-0">Active Total Staff Members</h6>
                                  <h2 class="m-t-0">{!! count($active_staff_members) !!}</h2>
                              </div>
                          </div>
                      </div>
                  </div>
              </div>
       </div>
       <div class="animated fadeIn">
            <div class="row">
                <div class="col-md-12">
                    <div class="card">
                        <div class="card-header">
                            <i class="fa fa-align-justify"></i>
                            List of Active Land Loards
                        </div>
                        <div class="card-body">
                            <table class="table table-responsive-sm table-bordered table-striped table-sm">
                                <thead>
                                <tr>
                                  <th>Details</th>
                                  <th>User Name</th>
                                  <th>Full Name</th>
                                  <th>Date registered</th>
                                  <th>Email</th>
                                </tr>
                                </thead>
                                <tbody>
                                  @foreach($ownersRecord as $key => $value)
                                <tr>
                                   @php
                                      $url = '/admin/user_details?id='.GeneralFunctions::encryptString($value['id']);
                                    @endphp
                                    <td><a href="{{ url($url)}}"><i class="fa fa-info"></i> Details</a></td>
                                    <td>{{$value['Username']}}</td>
                                    <td>{{$value['name']}}</td>
                                    <td>{!! GeneralFunctions::convertToDateTimeToString($value['created_at']) !!}</td>
                                    <td>{{$value['email']}}</td>
                                </tr>
                            @endforeach
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>
            </div>
            <div class="row">
                <div class="col-md-12">
                    <div class="card">
                        <div class="card-header">
                            <i class="fa fa-align-justify"></i>
                            List of Active Contractors
                        </div>
                        <div class="card-body">
                            <table class="table table-responsive-sm table-bordered table-striped table-sm">
                                <thead>
                                <tr>
                                  <th>Details</th>
                                  <th>User Name</th>
                                  <th>Full Name</th>
                                  <th>Date registered</th>
                                  <th>Email</th>
                                </tr>
                                </thead>
                                <tbody>
                                  @foreach($contractorRecord as $key => $value)
                                <tr>
                                    @php
                                      $url = '/admin/user_details?id='.GeneralFunctions::encryptString($value['id']);
                                    @endphp
                                    <td><a href="{{ url($url) }}"><i class="fa fa-info"></i> Details</a></td>
                                    <td>{{$value['Username']}}</td>
                                    <td>{{$value['name']}}</td>
                                    <td>{!! GeneralFunctions::convertToDateTimeToString($value['created_at']) !!}</td>
                                    <td>{{$value['email']}}</td>
                                </tr>
                            @endforeach
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>
            </div>
        </div>
     </div>
     {{--Modal--}}
    <div class="remodal" data-remodal-id="delete_modal" data-remodal-options="hashTracking: false, closeOnOutsideClick: false">
        <button data-remodal-action="close" class="remodal-close"></button>
        <h1 class="status_title"></h1>

        <form id="state_form" action="{{ url('admin/update/account/status') }}" method="POST">
            <input type="hidden" class="record_id" name="id" />
            <input type="hidden" id="state" name="state" value="" />
            {{ csrf_field() }}
            <textarea style="display: none;" id="reason" name="reason" class="form-control reason" placeholder="Write a Reason" required></textarea>
            <div class="approve_account_details" style="display: none;">
              <div class="form-check">
                <label class="form-check-label">
                  <input type="radio" class="form-check-input" name="account_type" value="1">Free Account
                </label>
              </div>
              <div class="form-check">
                <label class="form-check-label">
                  <input type="radio" class="form-check-input" name="account_type" value="2">Paid Account
                </label>
              </div>
            </div>
        </form>
        <br>
        <button data-remodal-action="cancel" class="remodal-cancel">Cancel</button>
        <button data-remodal-action="confirm" class="remodal-confirm">OK</button>
    </div>
@endsection

@section('js')
    <script type="text/javascript">
        $(document).ready(function(){
            $(document).on('click', '.account_status', function(){
                var state = $(this).attr('data-state');
                $('#state').val(state);
                $('.reason').show();
                $('.record_id').val($(this).attr('id'));
                if(state == 1)
                {
                  $('.status_title').text('Approve Account');
                  $('.approve_account_details').show();
                  $('.reason').hide();
                    // $('[data-remodal-action=confirm]').text('Activate');
                }
                else{
                  $('.status_title').text('Reject Account');
                  $('.approve_account_details').hide();
                  $('.reason').show();
                }
                var inst = $('[data-remodal-id=delete_modal]').remodal();
                inst.open();
            });
            $(document).on('confirmation', '.remodal', function () {
                $('#state_form').submit()[0];
            });
        });
    </script>
@endsection
