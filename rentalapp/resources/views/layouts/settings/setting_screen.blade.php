@extends( Auth::user()->Roles == 2 ? 'company_portal.layouts.app' :  (Auth::user()->Roles == 1 ? 'super_admin_portal.layouts.app' : 'partner_portal.layout.app'))
@section('content')
@php
  $selectedTimeZone = '';
  if(isset($tenant_setting_details) && count($tenant_setting_details) > 0){
    $selectedTimeZone = $tenant_setting_details['ValueData'];
  }

  if(Auth::user()->Roles == 1){
    $tenantSettingsUrl = '/admin/settings/update';
    $currentUrl = '/admin/settings';
  }
  elseif(Auth::user()->Roles == 2){
    $tenantSettingsUrl = '/admin/company/settings/update';
    $currentUrl = '/admin/company/settings';
  }else{
    $tenantSettingsUrl = '/admin/partner/settings/update';
    $currentUrl = '/admin/partner/settings';
  }
@endphp
<ol class="breadcrumb">
    <li class="breadcrumb-item">Home</li>
    <li class="breadcrumb-item"><a href="#">Admin</a></li>
    <li class="breadcrumb-item active">Setting</li>
    <!-- Breadcrumb Menu-->
    <li class="breadcrumb-menu d-md-down-none">
      <div class="btn-group" role="group" aria-label="Button group with nested dropdown">
        <a class="btn" href="{{ url($currentUrl)}}"><i class="icon-graph"></i> &nbsp;Setting</a>
      </div>
    </li>
</ol>

<div class="container-fluid">
  <div class="row">
    @include('errors.flash_message')
    @if(\Session::has('error'))
    <div class="col-sm-12">
        <div class="alert alert-danger">
            <button type="button" class="close" data-dismiss="alert" aria-label="Close">
                <span aria-hidden="true">&times;</span>
            </button>
            <p>{{ \Session::get('error')}}</p>
        </div>
    </div>
  @endif
  </div>
  <div class="row">
      <div class="col-lg-6">
        <div class="card">
          <div class="card-header">
            <i class="fa fa-align-justify"></i> System Settings
          </div>
          <div class="card-body">
            <div class="card">
              <div class="card-header">
              </div>
              <div class="card-body">
                <form action="{{ url($tenantSettingsUrl) }}" method="post" class="form-horizontal contact_info_form">
                    <div class="form-group row">
                      <label class="col-md-3 col-form-label" for="p-full_name">System Time Zone</label>
                      <div class="col-md-9">
                        {{ csrf_field() }}
                        <select class="form-control" name="system_time_zone" id="system_time_zone">
                          <option value="">Choose Time Zone For the System</option>
                          @foreach((array) Config::get('constant.time_zone_dropdwn') as $key => $value)
                            <option value="{{$key}}" {!! $selectedTimeZone == $key ? 'selected' : '' !!}>{{$value}}</option>
                          @endforeach
                        </select>
                      </div>
                    </div>
                    <div class="form-group row">
                      <label class="col-md-3 col-form-label" for="p-mobile_number">Mobile Number</label>
                      <div class="col-md-9">
                        <button class="btn btn-primary" type="submit">Submit</button>
                      </div>
                    </div>
                </form>
              </div>
            <div class="card-footer">
            </div>
          </div>
        </div>
      </div>
    </div>
    @if(Auth::user()->Roles != 1)
      <div class="col-md-6">
          <div class="card">
              <div class="card-header">
                  <i class="fa fa-align-justify"></i>
                  Currency Chart
              </div>
              <div class="card-body">
                  <form action="{{route('update.currencies.tenant')}}" method="post">
                      {{csrf_field()}}
                      <input type="hidden" id="baseCurrency" name="baseCurrency" value="" />
                      <div class="row" style="margin-bottom: 25px">
                          <div class="col-md-3">Currency Name</div>
                          <div class="col-md-6 text-center">Rate</div>
                          <div class="col-md-3">
                              Base Currency
                          </div>
                      </div>
                      @php $flage = false; @endphp
                      @foreach((array)$currencies as $currency)
                          @if($currency->isBaseCurrency == 1)
                              @php $flage = true; @endphp
                          @endif
                      <div class="row form-group">
                          <div class="col-md-2">{{$currency->currencyList->currency}}</div>
                          <div class="col-md-8">
                              <input type="number" step="any" name="currency[{{$currency->Id}}]" value="{{$currency->CurrentRate}}" class="form-control" />
                          </div>
                          <div class="col-md-2">
                              <input class="base" type="radio" name="isBase" value="{{$currency->Id}}" {{ ($currency->isBaseCurrency == 1) ? 'checked' : '' }} {{ ($currency->isBaseCurrency != 1 && $flage == true) ? 'disabled' : '' }} />
                          </div>
                      </div>
                      @endforeach
                      @if($currencies->count() > 0)
                          <div class="col-md-9 offset-2 text-center">
                              <button type="submit" class="btn btn-primary btn-md">Update</button>
                          </div>
                      @endif
                  </form>
              </div>
          </div>
      </div>
    @endif
  </div>
</div>
@endsection
@section('js')
    <script>
        $(document).ready(function () {
            $('.base').on('change',function (e) {
                console.log($(this).val());
                $('#baseCurrency').val($(this).val());
            });
        });
    </script>
    @endsection
