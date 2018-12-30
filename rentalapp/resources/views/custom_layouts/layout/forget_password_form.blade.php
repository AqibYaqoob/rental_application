@extends('custom_layouts.layout.app')
@section('content')
  <!-- Main Content of the Page -->
  <div class="container-fluid">
    @include('errors.flash_message')
    <div class="row">
        <div class="col-lg-12">
          <div class="card">
            <div class="card-header">
              <i class="fa fa-align-justify"></i> Form to fill with New Password
            </div>
            <div class="card-body">
              @if($status)
                <form action="{{url('/new/password/save/record')}}" method="post"
                              class="form-horizontal">
                    <div class="form-group row">
                        <label class="col-md-3 col-form-label" for="password">Password</label>
                        <div class="col-md-9">
                            {{ csrf_field() }}
                            <input type="hidden" name="id" value="{{GeneralFunctions::encryptString($userId)}}">
                            <input type="password" id="password" name="password" class="form-control password" placeholder="Password">
                        </div>
                    </div>
                    <div class="form-group row">
                        <label class="col-md-3 col-form-label" for="password_confirmation">Confirm Password</label>
                        <div class="col-md-9">
                            <input type="password" id="password_confirmation" name="password_confirmation" class="form-control password_confirmation" placeholder="Confirm Password">
                        </div>
                    </div>
                    <div class="form-group row">
                        <div class="col-md-9">
                            <button class="btn btn-success" type="submit">Save</button>
                        </div>
                    </div>
                </form>
              @else
                <div class="col-sm-12">
                    <div class="alert alert-danger">
                        <p>You did not provided the valid emil.</p>
                    </div>
                </div>
              @endif
            </div>
          </div>
        </div>
        <!--/.col-->
      </div>
  </div>
@endsection
@section('js')
  <script type="text/javascript">
    $(document).ready(function(){
    });
  </script>
@endsection
