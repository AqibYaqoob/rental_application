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

  <title>Login</title>

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
      <div class="col-md-8">
        <div class="card-group">
          <div class="card p-4">
            <div class="card-body">
              <h1>{{ trans('labels.login') }}</h1>
              <p class="text-muted">Sign In to your account</p>
              <form action="{{url('/admin/login/user')}}" method="POST">
                <div class="input-group mb-3">
                  <span class="input-group-addon"><i class="icon-user"></i></span>
                  <input type="text" class="form-control" placeholder="{{trans('labels.user_name')}} or Email" name="user_name">
                  {{ csrf_field() }}
                </div>
                <div class="input-group mb-4">
                  <span class="input-group-addon"><i class="icon-lock"></i></span>
                  <input type="password" class="form-control" placeholder="{{trans('labels.password')}}" name="password">
                </div>
                <div class="row">
                  <div class="col-6">
                    <button type="submit" class="btn btn-primary px-4">{{ trans('labels.login') }}</button>
                  </div>
                  <div class="col-6 text-right">
                    <a href="{{ route('password.request') }}" class="btn btn-link px-0 password_reset">{{ trans('labels.forget_password') }}</a>
                  </div>
                </div>
              </form>
            </div>
          </div>
          <div class="card text-white bg-primary py-5 d-md-down-none" style="width:44%">
            <div class="card-body text-center">
              <div>
                <h2>{{ trans('labels.sign_up') }}</h2>
                <p>{{ trans('labels.sign_up_description') }}</p>
                <br>
                <h3>In Progress</h3>
                <!-- <a href="{{url('admin/register')}}" class="btn btn-primary active mt-3">{{ trans('labels.register_now') }}</a> -->
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
  <script type="text/javascript">
    $(document).ready(function(){
      $(document).on('click', '.password_reset', function(){
        // console.log("{{ url('admin/password/reset') }}");

        // window.location.href = "{{ url('admin/password/reset') }}";
      });
    });
  </script>
</body>
</html>
