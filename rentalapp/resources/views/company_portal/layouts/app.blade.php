<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="utf-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no">
    <meta name="description" content="Prime - Bootstrap 4 Admin Template">
    <meta name="author" content="Łukasz Holeczek">
    <meta name="keyword"
          content="Bootstrap,Admin,Template,Open,Source,AngularJS,Angular,Angular2,jQuery,CSS,HTML,RWD,Dashboard,Vue,Vue.js,React,React.js">
    <link rel="shortcut icon" href="img/favicon.png">
    <title>{!! isset($title) ? trans('labels.'.$title)  : '' !!}</title>

    <!-- Icons -->
    <link href="{{ url('vendors/css/font-awesome.min.css') }}" rel="stylesheet">
    <link href="{{ url('vendors/css/simple-line-icons.min.css') }}" rel="stylesheet">

    <!-- Main styles for this application -->
    <link href="{{ url('css/style.css') }}" rel="stylesheet">

    <!-- Styles required by this views -->
    <link href="{{ url('vendors/css/daterangepicker.min.css') }}" rel="stylesheet">
    <link href="{{ url('vendors/css/gauge.min.css') }}" rel="stylesheet">
    <link href="{{ url('vendors/css/toastr.min.css') }}" rel="stylesheet">

    <!-- Styles for Popups -->
    <link href="{{ url('css/remodal.css') }}" rel="stylesheet">
    <link href="{{ url('css/remodal-default-theme.css') }}" rel="stylesheet">
    <link rel="stylesheet" type="text/css" href="{{ url('css/datatables.css') }}">
    <link rel="stylesheet" type="text/css" href="{{ url('css/buttons.dataTables.min.css') }}">
    <style type="text/css">
        a.buttons-collection {
            margin-left: 1em;
        }
    </style>
    @yield('css')
</head>
<body class="app header-fixed sidebar-fixed aside-menu-fixed aside-menu-hidden">
<header class="app-header navbar">
    <button class="navbar-toggler mobile-sidebar-toggler d-lg-none" type="button">
        <span class="navbar-toggler-icon"></span>
    </button>
    <a class="navbar-brand" href="#"></a>
    <button class="navbar-toggler sidebar-toggler d-md-down-none" type="button">
        <span class="navbar-toggler-icon"></span>
    </button>
    <ul class="nav navbar-nav ml-auto">
        <li class="nav-item dropdown">
            <a class="nav-link nav-link" data-toggle="dropdown" href="#" role="button" aria-haspopup="true"
               aria-expanded="false">
                @if(session('locale') != null)
                    <img src="{{ url('img/flags/'.session('locale').'.png') }}" class="img-avatar" alt="admin@bootstrapmaster.com">
                @else
                    <img src="{{ url('img/flags/en.png') }}" class="img-avatar" alt="admin@bootstrapmaster.com">
                @endif
            </a>
            <div class="dropdown-menu dropdown-menu-right">

                <a class="dropdown-item" href="{{ url('/change/language/chi') }}"><img style="margin-right: 10px;" src="{!! url('img/flags/chi.png') !!}">Chinese</a>
                <a class="dropdown-item" href="{{ url('/change/language/en') }}"><img style="margin-right: 10px;" src="{!! url('img/flags/en.png') !!}">English</a>
            </div>
        </li>
        <li class="nav-item dropdown">
            <a class="nav-link nav-link" data-toggle="dropdown" href="#" role="button" aria-haspopup="true"
               aria-expanded="false">
                <img src="{{ url('img/avatars/custome.png') }}" class="img-avatar" alt="admin@bootstrapmaster.com">
            </a>
            <div class="dropdown-menu dropdown-menu-right">
                <div class="dropdown-header text-center">
                    <strong>{{ Session::get('user_name') }}</strong>
                </div>
                <a class="dropdown-item" href="{{ url('/admin/company/profile') }}"><i class="fa fa-bell-o"></i>{{trans('labels.profile')}}</a>
                @if(Auth::user()->IsAdmin == 1)
                    <a class="dropdown-item" href="{{ url('/admin/company/settings') }}"><i class="fa fa-envelope-o"></i> {{trans('labels.settings')}}</a>
                @endif
                <a class="dropdown-item" href="{{ url('/admin/company/logout') }}"><i class="fa fa-lock"></i> {{trans('labels.logout')}}</a>
            </div>
        </li>
    </ul>
</header>
<div class="app-body">
    <div class="sidebar">
        <nav class="sidebar-nav">
            <ul class="nav">
                <li class="nav-item">
                    <a class="nav-link" href="{{ url('admin/company/dashboard') }}"><i class="icon-speedometer"></i>
                        {{trans('labels.dashboard')}} </a>
                </li>
                <li class="divider"></li>
                @if(Auth::user()->IsAdmin == 1)
                    <li class="nav-item nav-dropdown">
                        <a class="nav-link nav-dropdown-toggle" href="#"><i class="icon-puzzle"></i>{{trans('labels.user_managment')}}</a>
                        <ul class="nav-dropdown-items">
                            <li class="nav-item">
                                <a class="nav-link" href="{{ url('admin/company/roles_form') }}"><i
                                            class="icon-puzzle"></i>{{trans('labels.add_roles')}}</a>
                            </li>
                            <li class="nav-item">
                                <a class="nav-link" href="{{ url('admin/company/roles_list') }}"><i
                                            class="icon-puzzle"></i> {{trans('labels.roles_list')}}</a>
                            </li>
                            <li class="nav-item">
                                <a class="nav-link" href="{{ url('admin/company/staff/form') }}"><i
                                            class="icon-puzzle"></i> {{trans('labels.add_staff')}} </a>
                            </li>
                            <li class="nav-item">
                                <a class="nav-link" href="{{ url('admin/company/staff/member/list') }}"><i
                                            class="icon-puzzle"></i> {{trans('labels.staff_list')}} </a>
                            </li>
                        </ul>
                    </li>
                @endif
                @if(GeneralFunctions::check_view_permission('cat_add') || GeneralFunctions::check_view_permission('cat_list'))
                    <li class="nav-item nav-dropdown">
                        <a class="nav-link nav-dropdown-toggle" href="#"><i class="icon-puzzle"></i> {{trans('labels.category_management')}} </a>
                        <ul class="nav-dropdown-items">
                            @if(GeneralFunctions::check_view_permission('cat_add'))
                                <li class="nav-item">
                                    <a class="nav-link" href="{{ route('add.category.form') }}"><i class="icon-puzzle"></i>{{trans('labels.category_add')}} </a>
                                </li>
                            @endif
                            @if(GeneralFunctions::check_view_permission('cat_list'))
                                <li class="nav-item">
                                    <a class="nav-link" href="{{ route('category.list') }}"><i class="icon-puzzle"></i>{{trans('labels.category_list')}} </a>
                                </li>
                            @endif
                        </ul>
                    </li>
                @endif
                @if(GeneralFunctions::check_view_permission('partner_form') || GeneralFunctions::check_view_permission('partner_list') || GeneralFunctions::check_view_permission('base_currency_list'))
                    <li class="nav-item nav-dropdown">
                        <a class="nav-link nav-dropdown-toggle" href="#"><i class="icon-puzzle"></i> {{trans('labels.partners')}} </a>
                        <ul class="nav-dropdown-items">
                            @if(GeneralFunctions::check_view_permission('partner_form'))
                                <li class="nav-item">
                                    <a class="nav-link" href="{{ url('admin/company/partners/form') }}"><i class="icon-puzzle"></i>{{trans('labels.add_partner_account')}} </a>
                                </li>
                            @endif
                            @if(GeneralFunctions::check_view_permission('partner_list'))
                                <li class="nav-item">
                                    <a class="nav-link" href="{{ url('admin/company/partner/list') }}"><i class="icon-puzzle"></i> {{trans('labels.partner_list')}}</a>
                                </li>
                            @endif
                        </ul>
                    </li>
                @endif
                @if(GeneralFunctions::check_view_permission('add_site') || GeneralFunctions::check_view_permission('site_list'))
                    <li class="nav-item nav-dropdown">
                        <a class="nav-link nav-dropdown-toggle" href="#"><i class="icon-puzzle"></i>{{trans('labels.site_management')}}</a>
                        <ul class="nav-dropdown-items">
                            @if(\App\Helpers\GeneralFunctions::check_view_permission('add_site'))
                                <li class="nav-item">
                                    <a href="{{ url('admin/company/site_form') }}" class="nav-link"><i class="icon-puzzle"></i>{{trans('labels.add_site')}}</a>
                                </li>
                            @endif
                            @if(\App\Helpers\GeneralFunctions::check_view_permission('site_list'))
                                <li class="nav-item">
                                    <a href="{{ url('admin/company/site/list') }}" class="nav-link"><i class="icon-puzzle"></i>{{trans('labels.site_list')}}</a>
                                </li>
                            @endif
                        </ul>
                    </li>
                @endif
                @if(GeneralFunctions::check_view_permission('site_account_form') || GeneralFunctions::check_view_permission('site_account_list'))
                    <li class="nav-item nav-dropdown">
                        <a class="nav-link nav-dropdown-toggle" href="#"><i class="icon-puzzle"></i>{{trans('labels.site_account')}}</a>
                        <ul class="nav-dropdown-items">
                            @if(GeneralFunctions::check_view_permission('site_account_form'))
                                <li class="nav-item">
                                    <a class="nav-link" href="{{ url('admin/company/site_account_form')}}"><i class="icon-puzzle"></i> {{trans('labels.add_site_account')}} </a>
                                </li>
                            @endif
                            @if(GeneralFunctions::check_view_permission('site_account_list'))
                                <li class="nav-item">
                                    <a class="nav-link" href="{{ url('admin/company/site/account/list') }}"><i class="icon-puzzle"></i> {{trans('labels.account_list')}} </a>
                                </li>
                            @endif
                        </ul>
                    </li>
                @endif
                {{--<li class="nav-item nav-dropdown">
                    <a class="nav-link nav-dropdown-toggle" href="#"><i class="icon-puzzle"></i> {{trans('labels.assign_state_account')}}</a>
                    <ul class="nav-dropdown-items">
                        @if(GeneralFunctions::check_view_permission('partner_account_list'))
                            <li class="nav-item">
                                <a class="nav-link" href="{{ route('partner.account.list') }}"><i class="icon-puzzle"></i>{{trans('labels.list_partner_assigned')}} </a>
                            </li>
                        @endif
                        @if(GeneralFunctions::check_view_permission('partner_assign_account'))
                            <li class="nav-item">
                                <a class="nav-link" href="{{ route('partner.assign.form') }}"><i class="icon-puzzle"></i> {{trans('labels.assign_to_partner')}} </a>
                            </li>
                        @endif
                        @if(GeneralFunctions::check_view_permission('list_shareholder_assigned_account'))
                            <li class="nav-item">
                                <a class="nav-link" href="{{ route('shareHolder.account.list') }}"><i class="icon-puzzle"></i> {{trans('labels.list_share_holder_assign')}}</a>
                            </li>
                        @endif
                        @if(GeneralFunctions::check_view_permission('shareHolder_assign_account'))
                            <li class="nav-item">
                                <a class="nav-link" href="{{ route('shareHolder.assign.form') }}"><i class="icon-puzzle"></i>  {{trans('labels.assign_to_share_holder')}}</a>
                            </li>
                        @endif
                    </ul>
                </li>--}}
                @if(GeneralFunctions::check_add_permission('currency_form') || GeneralFunctions::check_view_permission('currency_list'))
                    <li class="nav-item nav-dropdown">
                        <a class="nav-link nav-dropdown-toggle" href="#"><i class="icon-puzzle"></i> {{trans('labels.currency_managment')}} </a>
                        <ul class="nav-dropdown-items">
                            @if(GeneralFunctions::check_add_permission('currency_form'))
                                <li class="nav-item">
                                    <a class="nav-link" href="{{route('currency.form')}}"><i class="icon-puzzle"></i>
                                        {{trans('labels.add_currency')}} </a>
                                </li>
                            @endif
                            @if(GeneralFunctions::check_view_permission('currency_list'))
                                <li class="nav-item">
                                    <a class="nav-link" href="{{route('currency.show')}}"><i class="icon-puzzle"></i>
                                        {{trans('labels.currecny_list')}} </a>
                                </li>
                            @endif
                        </ul>
                    </li>
                @endif
                @if(GeneralFunctions::check_view_permission('site_account_transaction_form') || GeneralFunctions::check_view_permission('site_account_transaction_list'))
                    <li class="nav-item nav-dropdown">
                        <a class="nav-link nav-dropdown-toggle" href="#"><i class="icon-puzzle"></i> {{trans('labels.transaction_managment')}}
                        </a>
                        <ul class="nav-dropdown-items">
                            @if(GeneralFunctions::check_view_permission('site_account_transaction_form'))
                                <li class="nav-item">
                                    <a class="nav-link"
                                       href="{{ url('admin/company/site_account_transaction_form')}}"><i
                                                class="icon-puzzle"></i> {{trans('labels.add_site_account_transaction')}} </a>
                                </li>
                            @endif
                            @if(GeneralFunctions::check_view_permission('site_account_transaction_list'))
                                <li class="nav-item">
                                    <a class="nav-link" href="{{url('admin/company/site/account/transaction/list')}}"><i
                                                class="icon-puzzle"></i> {{trans('labels.account_transaction_list')}} </a>
                                </li>
                            @endif
                        </ul>
                    </li>
                @endif
                @if(GeneralFunctions::check_view_permission('send_cash_balance') || GeneralFunctions::check_view_permission('tenant_account_details_transaction_list') || GeneralFunctions::check_view_permission('share_profit'))
                    <li class="nav-item nav-dropdown">
                        <a class="nav-link nav-dropdown-toggle" href="#"><i class="icon-puzzle"></i> {{trans('labels.transfer_funcds')}} </a>
                        <ul class="nav-dropdown-items">
                         @if(GeneralFunctions::check_view_permission('send_cash_balance'))
                            <li class="nav-item">
                                <a class="nav-link"
                                   href="{{ url('admin/company/transfer_fund_accounts')}}"><i class="icon-puzzle"></i>  {{trans('labels.transfer_cash_balance')}} </a>
                            </li>
                         @endif
                         @if(GeneralFunctions::check_view_permission('tenant_account_details_transaction_list'))
                            <li class="nav-item">
                                <a class="nav-link" href="{{url('admin/company/transfer_fund_accounts/list')}}"><i class="icon-puzzle"></i> {{trans('labels.check_transaction_list')}} </a>
                            </li>
                         @endif
                            @if(GeneralFunctions::check_view_permission('share_profit'))
                                <li class="nav-item">
                                    <a class="nav-link" href="{{ route('profit.share') }}"><i class="icon-puzzle"></i> {{trans('labels.share_profit')}}</a>
                                </li>
                            @endif
                        </ul>
                    </li>
                @endif
                @if(GeneralFunctions::check_view_permission('partner_profitloss') || GeneralFunctions::check_view_permission('partner_account_detail') || GeneralFunctions::check_view_permission('tenant_profitloss') || GeneralFunctions::check_view_permission('tenant_account_detail') || GeneralFunctions::check_view_permission('interest_detail'))
                    <li class="nav-item nav-dropdown">
                        <a class="nav-link nav-dropdown-toggle" href="#"><i class="icon-puzzle"></i> {{ trans('labels.reports') }}</a>
                        <ul class="nav-dropdown-items">
                            @if(GeneralFunctions::check_view_permission('partner_profitloss'))
                                <li class="nav-item">
                                    <a href="{{ route('report.partner.profitloss') }}" class="nav-link"><i class="icon-puzzle"></i> {{ trans('labels.partner_profit_loss') }}</a>
                                </li>
                            @endif
                            @if(GeneralFunctions::check_view_permission('partner_account_detail'))
                                <li class="nav-item">
                                    <a href="{{ route('report.partner.account.detail') }}" class="nav-link"><i class="icon-puzzle"></i> {{ trans('labels.partner_account_detail') }}</a>
                                </li>
                            @endif
                            @if(GeneralFunctions::check_view_permission('tenant_profitloss'))
                                <li class="nav-item">
                                    <a href="{{ route('report.tenant.profitloss') }}" class="nav-link"><i class="icon-puzzle"></i> {{ trans('labels.tenant_profit_loss') }}</a>
                                </li>
                            @endif
                            @if(GeneralFunctions::check_view_permission('tenant_account_detail'))
                                <li class="nav-item">
                                    <a href="{{ route('report.tenant.account.detail') }}" class="nav-link"><i class="icon-puzzle"></i> {{ trans('labels.tenant_account_detail') }}</a>
                                </li>
                            @endif
                            @if(GeneralFunctions::check_view_permission('interest_detail'))
                                <li class="nav-item">
                                    <a href="{{ route('partners.interest.report') }}" class="nav-link"><i class="icon-puzzle"></i> {{ trans('labels.interest_report') }}</a>
                                </li>
                            @endif
                        </ul>
                    </li>
                @endif
            </ul>
        </nav>
        <button class="sidebar-minimizer brand-minimizer" type="button"></button>
    </div>
    <!-- Main content -->
    <main class="main">
        @yield('content')
    </main>
</div>
<footer class="app-footer text-center">
    <span class="">Betting Form © {{ \Carbon\Carbon::now()->year }}</span>
</footer>

<!-- Bootstrap and necessary plugins -->
<script src="{{ url('vendors/js/jquery.min.js') }}"></script>
<script src="{{ url('vendors/js/popper.min.js') }}"></script>
<script src="{{ url('vendors/js/bootstrap.min.js') }}"></script>
<script src="{{ url('vendors/js/pace.min.js') }}"></script>

<!-- Plugins and scripts required by all views -->
<script src="{{ url('vendors/js/Chart.min.js') }}"></script>

<!-- Prime main scripts -->

<script src="{{ url('js/app.js') }}"></script>

<!-- Plugins and scripts required by this views -->
<script src="{{ url('vendors/js/toastr.min.js') }}"></script>
<script src="{{ url('vendors/js/gauge.min.js') }}"></script>
<script src="{{ url('vendors/js/moment.min.js') }}"></script>
<script src="{{ url('vendors/js/daterangepicker.min.js') }}"></script>

<!-- Custom scripts required by this view -->
<script src="{{ url('js/views/main.js') }}"></script>
<script src="{{ url('js/remodal.min.js') }}"></script>
<script type="text/javascript" src="{{ url('js/datatables.js') }}"></script>
<script type="text/javascript" src="{{ url('js/dataTables.select.min.js') }}"></script>
<script type="text/javascript" src="{{ url('js/dataTables.buttons.min.js') }}"></script>
<script type="text/javascript" src="{{ url('js/jszip.min.js') }}"></script>
<script type="text/javascript" src="{{ url('js/pdfmake.min.js') }}"></script>
<script type="text/javascript" src="{{ url('js/vfs_fonts.js') }}"></script>
<script type="text/javascript" src="{{ url('js/buttons.html5.min.js') }}"></script>
<script type="text/javascript" src="{{ url('js/buttons.print.min.js') }}"></script>
<script type="text/javascript" src="{{ url('js/jquery.repeater.js') }}"></script>
<script type="text/javascript">
    $(document).ready(function () {
        /*$('table').DataTable({
            dom: 'Bfrtip',
            buttons: [
                {
                    extend:'excelHtml5',
                    exportOptions: {
                        columns: ':visible:not(th:last-child)'
                    }
                },
                {
                    extend:'csvHtml5',
                    exportOptions: {
                        columns: ':visible:not(th:last-child)'
                    }
                },
                {
                    extend:'pdfHtml5',
                    orientation: 'landscape',
                    pageSize: 'LEGAL',
                    exportOptions:
                        {
                            columns: ':visible:not(th:last-child)'
                        }
                }
            ]
        });*/
        @if(\Session::has('welcom_msg'))
        setTimeout(function () {
            $('.md_profile_completion').modal('show');
        }, 400);
        @endif
        $('.datepicker').datepicker({
            orientation: 'bottom',
            format: 'yyyy/mm/dd',
            autoclose: true
        });
    });
</script>
@yield('js')
</body>
<!-- First Time Register. Welcome Message. -->
<div class="modal fade md_profile_completion" id="myModal" tabindex="-1" role="dialog" aria-labelledby="myModalLabel">
    <div class="modal-dialog" role="document">
        <div class="modal-content">
            <div class="modal-header alert alert-danger">
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
                <h4 class="modal-title" id="myModalLabel">Welcome</h4>
            </div>
            <div class="modal-body">
                <p class="success-message">Welcome to the Betting Form Portal. We hope that you will find each and
                    everthing which is required in this portal. If you find any difficulties, please share the issue
                    with administration. Please Complete the Profile First.</p>
            </div>
        </div>
    </div>
</div>
</html>
