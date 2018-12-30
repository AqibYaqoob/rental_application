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


<body class="app header-fixed sidebar-fixed aside-menu-fixed aside-menu-hidden pace-done sidebar-hidden">
<header class="app-header navbar">
    <button class="navbar-toggler mobile-sidebar-toggler d-lg-none" type="button">
        <span class="navbar-toggler-icon"></span>
    </button>
    <a href="#">Rental Application</a>
</header>
<div class="app-body">
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
