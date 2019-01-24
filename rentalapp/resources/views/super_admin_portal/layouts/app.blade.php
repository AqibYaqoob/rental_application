<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="utf-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no">
    <meta name="description" content="Prime - Bootstrap 4 Admin Template">
    <meta name="author" content="Łukasz Holeczek">
    <meta name="keyword" content="Bootstrap,Admin,Template,Open,Source,AngularJS,Angular,Angular2,jQuery,CSS,HTML,RWD,Dashboard,Vue,Vue.js,React,React.js">
    <link rel="shortcut icon" href="img/favicon.png">
    <title>{!! isset($title) ? $title : '' !!}</title>

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
    <link rel="stylesheet" type="text/css" href="{{ url('css/datatables.css') }}">

    <link href="{{ url('css/remodal-default-theme.css') }}" rel="stylesheet">

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
            <a class="nav-link nav-link" data-toggle="dropdown" href="#" role="button" aria-haspopup="true" aria-expanded="false">
                <img src="{{ url('img/avatars/custome.png') }}" class="img-avatar" alt="admin@bootstrapmaster.com">
            </a>
            <div class="dropdown-menu dropdown-menu-right">
                <div class="dropdown-header text-center">
                    <strong>{{ Session::get('user_name') }}</strong>
                </div>
                <a class="dropdown-item" href="{{ url('/admin/profile') }}"><i class="fa fa-bell-o"></i> {{trans('labels.profile')}}</a>
                @if(Auth::user()->IsAdmin == 1)
                    <a class="dropdown-item" href="{{ url('/admin/settings') }}"><i class="fa fa-envelope-o"></i> {{trans('labels.settings')}}</a>
                @endif
                <a class="dropdown-item" href="{{ url('/admin/logout') }}"><i class="fa fa-lock"></i> {{trans('labels.logout')}}</a>
            </div>
        </li>
    </ul>
</header>
<div class="app-body">
    <div class="sidebar">
        <nav class="sidebar-nav">
            <ul class="nav">
                <li class="nav-item">
                    <a class="nav-link" href="{{ url('admin/dashboard') }}"><i class="icon-speedometer"></i> {{trans('labels.dashboard')}} </a>
                </li>
                @if(Auth::user()->IsAdmin == 1)
                    <li class="nav-item nav-dropdown">
                        <a class="nav-link nav-dropdown-toggle" href="#"><i class="icon-puzzle"></i> {{trans('labels.user_managment')}}</a>
                        <ul class="nav-dropdown-items">
                                <li class="nav-item">
                                    <a class="nav-link" href="{{ url('admin/roles_form') }}"><i class="icon-puzzle"></i> {{trans('labels.add_roles')}}</a>
                                </li>
                                <li class="nav-item">
                                    <a class="nav-link" href="{{ url('admin/roles_list') }}"><i class="icon-puzzle"></i> {{trans('labels.roles_list')}}</a>
                                </li>
                                <li class="nav-item">
                                    <a class="nav-link" href="{{ url('admin/staff/form') }}"><i class="icon-puzzle"></i> {{trans('labels.add_staff')}}</a>
                                </li>
                                <li class="nav-item">
                                    <a class="nav-link" href="{{ url('admin/staff/member/list') }}"><i class="icon-puzzle"></i> {{trans('labels.staff_list')}}</a>
                                </li>
                        </ul>
                    </li>
                @endif
                @if(GeneralFunctions::check_view_permission('super_admin_audit_trail_reports'))
                <li class="nav-item">
                    <a class="nav-link" href="{{ url('/admin/audit_trail')}}"><i class="icon-puzzle"></i>{{trans('labels.audit_trail')}}</a>
                </li>
                @endif
                @if(GeneralFunctions::check_view_permission('packages_form') || GeneralFunctions::check_view_permission('packages_list'))
                    <li class="nav-item nav-dropdown">
                        <a class="nav-link nav-dropdown-toggle" href="#"><i class="icon-puzzle"></i> {{trans('labels.packages')}} </a>
                        <ul class="nav-dropdown-items">
                            @if(GeneralFunctions::check_view_permission('packages_form'))
                                <li class="nav-item">
                                    <a class="nav-link" href="{{ url('/admin/packages_form') }}"><i class="icon-puzzle"></i>{{trans('labels.add_packages')}} </a>
                                </li>
                            @endif
                            @if(GeneralFunctions::check_view_permission('packages_list'))
                                <li class="nav-item">
                                    <a class="nav-link" href="{{ url('/admin/packages/list') }}"><i class="icon-puzzle"></i>{{trans('labels.packages')}} </a>
                                </li>
                            @endif
                        </ul>
                    </li>
                @endif
                @if(GeneralFunctions::check_view_permission('payments_form') || GeneralFunctions::check_view_permission('payments_list'))
                    <li class="nav-item nav-dropdown">
                        <a class="nav-link nav-dropdown-toggle" href="#"><i class="icon-puzzle"></i> {{trans('labels.payments')}} </a>
                        <ul class="nav-dropdown-items">
                            @if(GeneralFunctions::check_view_permission('payments_form'))
                                <li class="nav-item">
                                    <a class="nav-link" href="{{ url('/admin/payments_form') }}"><i class="icon-puzzle"></i>{{trans('labels.add_payments')}} </a>
                                </li>
                            @endif
                            @if(GeneralFunctions::check_view_permission('payments_list'))
                                <li class="nav-item">
                                    <a class="nav-link" href="{{ url('/admin/payments/list') }}"><i class="icon-puzzle"></i>{{trans('labels.payments')}} </a>
                                </li>
                            @endif
                        </ul>
                    </li>
                @endif
                @if(GeneralFunctions::check_view_permission('skills_form') || GeneralFunctions::check_view_permission('skills_list'))
                    <li class="nav-item nav-dropdown">
                        <a class="nav-link nav-dropdown-toggle" href="#"><i class="icon-puzzle"></i> {{trans('labels.skills')}} </a>
                        <ul class="nav-dropdown-items">
                            @if(GeneralFunctions::check_view_permission('skills_form'))
                                <li class="nav-item">
                                    <a class="nav-link" href="{{ url('/admin/skills_form') }}"><i class="icon-puzzle"></i>{{trans('labels.add_skills')}} </a>
                                </li>
                            @endif
                            @if(GeneralFunctions::check_view_permission('skills_list'))
                                <li class="nav-item">
                                    <a class="nav-link" href="{{ url('/admin/skills/list') }}"><i class="icon-puzzle"></i>{{trans('labels.skills')}} </a>
                                </li>
                            @endif
                        </ul>
                    </li>
                @endif
                <!-- @if(GeneralFunctions::check_view_permission('super_admin_company_reports'))
                 <li class="nav-item">
                    <a class="nav-link" href="{{ url('/admin/companies_report')}}"><i class="icon-puzzle"></i>{{trans('labels.companies_reporting')}}</a>
                </li>
                @endif -->

            </ul>
        </nav>
        <button class="sidebar-minimizer brand-minimizer" type="button"></button>
    </div>
    <!-- Main content -->
    <main class="main">
        @yield('content')
    </main>
</div>
<footer class="app-footer">
    <!-- <span><a href="https://genesisui.com">Prime</a> © 2017 creativeLabs.</span>
    <span class="ml-auto">Powered by <a href="https://genesisui.com">GenesisUI</a></span> -->
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
<script type="text/javascript">
    $(document).ready(function(){
        $('table').DataTable({
            // dom: 'Bfrtip',
            // buttons: [
            //     {
            //         extend: 'collection',
            //         text: 'Export',
            //         buttons: [
            //             'copy',
            //             'excel',
            //             'csv',
            //             'pdf',
            //             'print'
            //         ],
            //         orientation: 'landscape',//landscape give you more space
            //         pageSize: 'A4',//A0 is the largest A5 smallest(A0,A1,A2,A3,legal,A4,A5,letter))
            //     }
            // ],
        });
    });
</script>
@yield('js')
</body>
</html>
