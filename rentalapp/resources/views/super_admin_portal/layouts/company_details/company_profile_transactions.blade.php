@extends('super_admin_portal.layouts.app')
@section('content')
    <ol class="breadcrumb">
        <li class="breadcrumb-item">Home</li>
        <li class="breadcrumb-item"><a href="#">Admin</a></li>
        <li class="breadcrumb-item active">Company Profile</li>
    </ol>
    <!-- Main Content of the Page -->
    <div class="container-fluid">
        <div class="row">
            <div class="col-lg-12">
                <div class="card">
                    <div class="card-header">
                        <i class="fa fa-align-justify"></i> Company Transaction Detail
                    </div>
                    <div class="card-body">
                        <div class="container-fluid">
                          <div class="animated fadeIn">
                            <div class="email-app mb-4">
                              @include('super_admin_portal.layouts.company_details.company_detail_side_bar')
                              <main class="message">
                                <div class="details">
                                  <div class="title">{!! $company['company_name']['TenantName'] !!}</div>
                                  <div class="header">
                                    <img class="avatar" src="{{ url('img/avatars/custome.png') }}">
                                    <div class="from">
                                      <span>{!! $company['Username']!!}</span>
                                      {!! $company['EmailAddress']!!}
                                    </div>
                                    <div class="date">Register Date, <b>{!! GeneralFunctions::convertToDateTimeToString($company['created_at']) !!}</b>
                                    <br>
                                    Base Currency, <b>{!! $company['currency']['CurrencyName'] !!}</b>
                                    </div>
                                  </div>
                                  <div class="content">
                                    <!-- <blockquote>
                                      Lorem ipsum dolor sit amet, consectetur adipisicing elit, sed do eiusmod tempor incididunt ut labore et dolore magna aliqua. Ut enim ad minim veniam, quis nostrud exercitation ullamco laboris nisi ut aliquip ex ea commodo consequat. Duis aute irure dolor
                                      in reprehenderit in voluptate velit esse cillum dolore eu fugiat nulla pariatur. Excepteur sint occaecat cupidatat non proident, sunt in culpa qui officia deserunt mollit anim id est laborum.
                                    </blockquote> -->
                                  </div>
                                  <div class="attachments">
                                    <h4>Transaction List</h4>
                                    <div class="card">
                                      <div class="card-body">
                                        <div style="overflow-x:auto;">
                                          <table class="table table-responsive-sm table-bordered table-striped table-sm">
                                            <thead>
                                              <tr>
                                                <th>#</th>
                                                <th>Transaction Id</th>
                                                <th>Source</th>
                                                <th>Transaction</th>
                                                <th>In Currecncies</th>
                                                <th>Amount in Currencies</th>
                                                <th>Amount in Base Currency</th>
                                                <th>Remarks</th>
                                                <th>Transaction Date</th>
                                              </tr>
                                            </thead>
                                            <tbody>
                                              @foreach($responseResult as $key => $value)
                                              <tr  {!! $value['reference_transaction_id'] != null ? 'style="background-color: #c7c344;"' : '' !!}>
                                                <td>{{$value['no']}}</td>
                                                <td>{{$value['transaction_id']}}</td>
                                                <td>{{$value['source']}}</td>
                                                <td>{{$value['transaction']}}</td>
                                                <td>{{$value['in_currency']}}</td>
                                                <td>{{$value['amount_in_different_currencies']}}</td>
                                                <td>{{$value['amount_in_base_currency']}}</td>
                                                @if($value['reference_transaction_id'] != null)
                                                  <td>{!! $value['remarks'].'. (Reference with Transaction '.$value['reference_transaction_id'].')' !!}</td>
                                                @else
                                                  <td>{!! $value['remarks'] !!}</td>
                                                @endif
                                                <td>{!! GeneralFunctions::convertToDateTimeToString($value['created_at']) !!}</td>
                                              </tr>
                                              @endforeach
                                            </tbody>
                                            <tfoot>
                                              <td></td>
                                                <td></td>
                                                <td></td>
                                                <td></td>
                                                <td></td>
                                                <td>Total</td>
                                                <td>{{$totalBalance}}</td>
                                                <td></td>
                                                <td></td>
                                            </tfoot>
                                          </table>
                                        </div>  
                                        <!-- </div> -->
                                      </div>
                                    </div>
                                  </div>
                                </div>
                              </main>
                            </div>
                          </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection
@section('js')
    <script type="text/javascript">
        $(document).ready(function(){
            $(document).on('click', '.state233', function(){
                var state = $(this).attr('data-state');
                $('#state').val(state);
                $('.record_id').val($(this).attr('id'));
                if(state == 1)
                {
                    $('#reason').remove();
                    $('[data-remodal-action=confirm]').text('Activate');
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