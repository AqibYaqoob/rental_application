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

  <title>Account Activation Screen</title>

  <!-- Icons -->
  <link href="{{url('vendors/css/font-awesome.min.css')}}" rel="stylesheet">
  <link href="{{url('vendors/css/simple-line-icons.min.css')}}" rel="stylesheet">

  <!-- Main styles for this application -->
  <link href="{{url('css/style.css')}}" rel="stylesheet">

  <!-- Styles required by this views -->

</head>
<body class="app flex-row align-items-center">
  <div class="container">
    <div class="row justify-content-center">
      <div class="col-md-6">
        <div class="card mx-4">
          <div class="card-body p-4">
            <h1>{{trans('labels.activate_account')}}</h1>
            <p class="text-muted">{{trans('labels.please_confirm_to_pay_for_account_activation')}}</p>
            <form action="{{url('/admin/payment/details')}}" method="POST">
              <div class="input-group mb-3">
                <span class="input-group-addon">{{trans('labels.user_name')}}</span>
                {{ csrf_field() }}
                <input type="text" class="form-control" value="{{$user_details['Username']}}" name="user_name" readonly="">
              </div>

              <div class="input-group mb-3">
                <span class="input-group-addon">{{trans('labels.user_email')}}</span>
                <input type="text" class="form-control" value="{{$user_details['EmailAddress']}}" name="user_email" readonly="">
                <input type="hidden" name="user_id" value="{{$user_details['id']}}">
              </div>

              <div class="input-group mb-3">
                <span class="input-group-addon">{{trans('labels.total_amount')}}</span>
                <input type="text" class="form-control" value="9.99 /-" name="amount" readonly="">
              </div>

              <button type="submit" class="btn btn-block btn-success">{{trans('labels.save')}}</button>
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
