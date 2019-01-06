<!DOCTYPE html>
<html lang="en">
<head>

  <meta charset="utf-8">
  <meta http-equiv="X-UA-Compatible" content="IE=edge">
  <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no">
  <meta name="description" content="Alba Bootstrap 4 Admin Template">
  <meta name="author" content="Lukasz Holeczek">
  <meta name="keyword" content="Alba Bootstrap 4 Admin Template">
  <!-- <link rel="shortcut icon" href="assets/ico/favicon.png"> -->

  <title>Register</title>

  <!-- Icons -->
  <link href="{{url('vendors/css/font-awesome.min.css')}}" rel="stylesheet">
  <link href="{{url('vendors/css/simple-line-icons.min.css')}}" rel="stylesheet">

  <!-- Main styles for this application -->
  <link href="{{url('css/style.css')}}" rel="stylesheet">

  <!-- Styles required by this views -->

</head>
<body class="app flex-row align-items-center">
  <div class="container">
    @include('errors.flash_message')
    <div class="row justify-content-center">
      <div class="col-md-6">
        <div class="card mx-4">
          <div class="card-body p-4">
            <h1>{{trans('labels.register')}}</h1>
            <p class="text-muted">{{trans('labels.create_account')}}</p>
            <form action="{{url('/admin/register/user')}}" method="POST">
              <div class="input-group mb-3">
                <span class="input-group-addon"><i class="icon-building"></i></span>
                <input type="text" class="form-control" placeholder="{{trans('labels.company_name')}}" name="company_name">
              </div>

              <div class="input-group mb-3">
                <span class="input-group-addon"><i class="icon-user"></i></span>
                <input type="text" class="form-control" placeholder="{{trans('labels.user_name')}}" name="user_name">
              </div>

              <div class="input-group mb-3">
                <span class="input-group-addon">@</span>
                {{ csrf_field() }}
                <input type="text" class="form-control" placeholder="{{trans('labels.user_email')}}" name="user_email">
              </div>

              <div class="input-group mb-3">
                <span class="input-group-addon"><i class="icon-lock"></i></span>
                <input type="password" class="form-control" placeholder="{{trans('labels.password')}}" name="password">
              </div>

              <div class="input-group mb-4">
                <span class="input-group-addon"><i class="icon-lock"></i></span>
                <input type="password" class="form-control" placeholder="{{trans('labels.repeat_password')}}" name="password_confirmation">
              </div>

              <div class="input-group mb-4">
                <span class="input-group-addon"><i class="icon-clock"></i></span>
                <select name="time_zone" id="time_zone" class="form-control">
                  <option value="">{{trans('labels.choose_time_zone')}}</option>
                  @foreach(Config::get('constant.time_zone_dropdwn') as $key => $value)
                    <option value="{{$key}}">{{$value}}</option>
                  @endforeach
                </select>
              </div>

              <button type="submit" class="btn btn-block btn-success">{{trans('labels.create_account')}}</button>
            </form>
          </div>
          <div class="card-footer p-4">
            <div class="row">
              <div class="col-6">
                <a class="btn btn-block btn-warning" href="{{url('admin/login')}}">
                  <span>{{trans('labels.back_to_login')}}</span>
                </a>
              </div>
            </div>
          </div>
        </div>
      </div>
    </div>
  </div>

  <!-- Bootstrap and necessary plugins -->
  <script src="{{url('vendors/js/jquery.min.js')}}"></script>
  <script src="{{url('vendors/js/popper.min.js')}}"></script>
  <script src="{{url('vendors/js/bootstrap.min.js')}}"></script>

</body>
</html>
