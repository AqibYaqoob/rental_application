<!--
 * GenesisUI - Bootstrap 4 Admin Template
 * @version v1.8.11
 * @link https://genesisui.com
 * Copyright (c) 2017 creativeLabs Łukasz Holeczek
 * @license https://genesisui.com/license.html
 -->
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
  <title>Prime - Bootstrap 4 Admin Template</title>

  <!-- Icons -->
  <link href="{{ url('vendors/css/font-awesome.min.css') }}" rel="stylesheet">
  <link href="{{ url('vendors/css/simple-line-icons.min.css') }}" rel="stylesheet">

  <!-- Main styles for this application -->
  <link href="{{ url('css/style.css') }}" rel="stylesheet">

  <!-- Styles required by this views -->
  <link href="{{ url('vendors/css/daterangepicker.min.css') }}" rel="stylesheet">
  <link href="{{ url('vendors/css/gauge.min.css') }}" rel="stylesheet">
  <link href="{{ url('vendors/css/toastr.min.css') }}" rel="stylesheet">

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
    <ul class="nav navbar-nav d-md-down-none mr-auto">

      <li class="nav-item px-3">
        <a class="nav-link" href="#">Dashboard</a>
      </li>
      <li class="nav-item px-3">
        <a class="nav-link" href="#">Users</a>
      </li>
      <li class="nav-item px-3">
        <a class="nav-link" href="#">Settings</a>
      </li>
    </ul>
    <ul class="nav navbar-nav ml-auto">
      <li class="nav-item dropdown d-md-down-none">
        <a class="nav-link" data-toggle="dropdown" href="#" role="button" aria-haspopup="true" aria-expanded="false">
          <i class="icon-bell"></i><span class="badge badge-pill badge-danger">5</span>
        </a>
        <div class="dropdown-menu dropdown-menu-right dropdown-menu-lg">
          <div class="dropdown-header text-center">
            <strong>You have 5 notifications</strong>
          </div>
          <a href="#" class="dropdown-item">
            <i class="icon-user-follow text-success"></i> New user registered
          </a>
          <a href="#" class="dropdown-item">
            <i class="icon-user-unfollow text-danger"></i> User deleted
          </a>
          <a href="#" class="dropdown-item">
            <i class="icon-chart text-info"></i> Sales report is ready
          </a>
          <a href="#" class="dropdown-item">
            <i class="icon-basket-loaded text-primary"></i> New client
          </a>
          <a href="#" class="dropdown-item">
            <i class="icon-speedometer text-warning"></i> Server overloaded
          </a>
          <div class="dropdown-header text-center">
            <strong>Server</strong>
          </div>
          <a href="#" class="dropdown-item">
            <div class="text-uppercase mb-1">
              <small><b>CPU Usage</b></small>
            </div>
            <span class="progress progress-xs">
              <div class="progress-bar bg-info" role="progressbar" style="width: 25%" aria-valuenow="25" aria-valuemin="0" aria-valuemax="100"></div>
            </span>
            <small class="text-muted">348 Processes. 1/4 Cores.</small>
          </a>
          <a href="#" class="dropdown-item">
            <div class="text-uppercase mb-1">
              <small><b>Memory Usage</b></small>
            </div>
            <span class="progress progress-xs">
              <div class="progress-bar bg-warning" role="progressbar" style="width: 70%" aria-valuenow="70" aria-valuemin="0" aria-valuemax="100"></div>
            </span>
            <small class="text-muted">11444GB/16384MB</small>
          </a>
          <a href="#" class="dropdown-item">
            <div class="text-uppercase mb-1">
              <small><b>SSD 1 Usage</b></small>
            </div>
            <span class="progress progress-xs">
              <div class="progress-bar bg-danger" role="progressbar" style="width: 95%" aria-valuenow="95" aria-valuemin="0" aria-valuemax="100"></div>
            </span>
            <small class="text-muted">243GB/256GB</small>
          </a>
        </div>
      </li>
      <li class="nav-item dropdown d-md-down-none">
        <a class="nav-link" data-toggle="dropdown" href="#" role="button" aria-haspopup="true" aria-expanded="false">
          <i class="icon-list"></i><span class="badge badge-pill badge-warning">15</span>
        </a>
        <div class="dropdown-menu dropdown-menu-right dropdown-menu-lg">
          <div class="dropdown-header text-center">
            <strong>You have 5 pending tasks</strong>
          </div>
          <a href="#" class="dropdown-item">
            <div class="small mb-1">Upgrade NPM &amp; Bower
              <span class="float-right">
                <strong>0%</strong>
              </span>
            </div>
            <span class="progress progress-xs">
              <div class="progress-bar bg-info" role="progressbar" style="width: 0%" aria-valuenow="0" aria-valuemin="0" aria-valuemax="100"></div>
            </span>
          </a>
          <a href="#" class="dropdown-item">
            <div class="small mb-1">ReactJS Version
              <span class="float-right">
                <strong>25%</strong>
              </span>
            </div>
            <span class="progress progress-xs">
              <div class="progress-bar bg-danger" role="progressbar" style="width: 25%" aria-valuenow="25" aria-valuemin="0" aria-valuemax="100"></div>
            </span>
          </a>
          <a href="#" class="dropdown-item">
            <div class="small mb-1">VueJS Version
              <span class="float-right">
                <strong>50%</strong>
              </span>
            </div>
            <span class="progress progress-xs">
              <div class="progress-bar bg-warning" role="progressbar" style="width: 50%" aria-valuenow="50" aria-valuemin="0" aria-valuemax="100"></div>
            </span>
          </a>
          <a href="#" class="dropdown-item">
            <div class="small mb-1">Add new layouts
              <span class="float-right">
                <strong>75%</strong>
              </span>
            </div>
            <span class="progress progress-xs">
              <div class="progress-bar bg-info" role="progressbar" style="width: 75%" aria-valuenow="75" aria-valuemin="0" aria-valuemax="100"></div>
            </span>
          </a>
          <a href="#" class="dropdown-item">
            <div class="small mb-1">Angular 2 Cli Version
              <span class="float-right">
                <strong>100%</strong>
              </span>
            </div>
            <span class="progress progress-xs">
              <div class="progress-bar bg-success" role="progressbar" style="width: 100%" aria-valuenow="100" aria-valuemin="0" aria-valuemax="100"></div>
            </span>
          </a>
          <a href="#" class="dropdown-item text-center">
            <strong>View all tasks</strong>
          </a>
        </div>
      </li>
      <li class="nav-item dropdown d-md-down-none">
        <a class="nav-link" data-toggle="dropdown" href="#" role="button" aria-haspopup="true" aria-expanded="false">
          <i class="icon-envelope-letter"></i><span class="badge badge-pill badge-info">7</span>
        </a>
        <div class="dropdown-menu dropdown-menu-right dropdown-menu-lg">
          <div class="dropdown-header text-center">
            <strong>You have 4 messages</strong>
          </div>
          <a href="#" class="dropdown-item">
            <div class="message">
              <div class="py-3 mr-3 float-left">
                <div class="avatar">
                  <img src="img/avatars/7.jpg" class="img-avatar" alt="admin@bootstrapmaster.com">
                  <span class="avatar-status badge-success"></span>
                </div>
              </div>
              <div>
                <small class="text-muted">John Doe</small>
                <small class="text-muted float-right mt-1">Just now</small>
              </div>
              <div class="text-truncate font-weight-bold">
                <span class="fa fa-exclamation text-danger"></span> Important message</div>
              <div class="small text-muted text-truncate">Lorem ipsum dolor sit amet, consectetur adipisicing elit, sed do eiusmod tempor incididunt...</div>
            </div>
          </a>
          <a href="#" class="dropdown-item">
            <div class="message">
              <div class="py-3 mr-3 float-left">
                <div class="avatar">
                  <img src="img/avatars/6.jpg" class="img-avatar" alt="admin@bootstrapmaster.com">
                  <span class="avatar-status badge-warning"></span>
                </div>
              </div>
              <div>
                <small class="text-muted">John Doe</small>
                <small class="text-muted float-right mt-1">5 minutes ago</small>
              </div>
              <div class="text-truncate font-weight-bold">Lorem ipsum dolor sit amet</div>
              <div class="small text-muted text-truncate">Lorem ipsum dolor sit amet, consectetur adipisicing elit, sed do eiusmod tempor incididunt...</div>
            </div>
          </a>
          <a href="#" class="dropdown-item">
            <div class="message">
              <div class="py-3 mr-3 float-left">
                <div class="avatar">
                  <img src="img/avatars/5.jpg" class="img-avatar" alt="admin@bootstrapmaster.com">
                  <span class="avatar-status badge-danger"></span>
                </div>
              </div>
              <div>
                <small class="text-muted">John Doe</small>
                <small class="text-muted float-right mt-1">1:52 PM</small>
              </div>
              <div class="text-truncate font-weight-bold">Lorem ipsum dolor sit amet</div>
              <div class="small text-muted text-truncate">Lorem ipsum dolor sit amet, consectetur adipisicing elit, sed do eiusmod tempor incididunt...</div>
            </div>
          </a>
          <a href="#" class="dropdown-item">
            <div class="message">
              <div class="py-3 mr-3 float-left">
                <div class="avatar">
                  <img src="img/avatars/4.jpg" class="img-avatar" alt="admin@bootstrapmaster.com">
                  <span class="avatar-status badge-info"></span>
                </div>
              </div>
              <div>
                <small class="text-muted">John Doe</small>
                <small class="text-muted float-right mt-1">4:03 PM</small>
              </div>
              <div class="text-truncate font-weight-bold">Lorem ipsum dolor sit amet</div>
              <div class="small text-muted text-truncate">Lorem ipsum dolor sit amet, consectetur adipisicing elit, sed do eiusmod tempor incididunt...</div>
            </div>
          </a>
          <a href="#" class="dropdown-item text-center">
            <strong>View all messages</strong>
          </a>
        </div>
      </li>
      <li class="nav-item dropdown">
        <a class="nav-link nav-link" data-toggle="dropdown" href="#" role="button" aria-haspopup="true" aria-expanded="false">
          <img src="img/avatars/6.jpg" class="img-avatar" alt="admin@bootstrapmaster.com">
        </a>
        <div class="dropdown-menu dropdown-menu-right">
          <div class="dropdown-header text-center">
            <strong>Account</strong>
          </div>
          <a class="dropdown-item" href="#"><i class="fa fa-bell-o"></i> Updates<span class="badge badge-info">42</span></a>
          <a class="dropdown-item" href="#"><i class="fa fa-envelope-o"></i> Messages<span class="badge badge-success">42</span></a>
          <a class="dropdown-item" href="#"><i class="fa fa-tasks"></i> Tasks<span class="badge badge-danger">42</span></a>
          <a class="dropdown-item" href="#"><i class="fa fa-comments"></i> Comments<span class="badge badge-warning">42</span></a>
          <div class="dropdown-header text-center">
            <strong>Settings</strong>
          </div>
          <a class="dropdown-item" href="#"><i class="fa fa-user"></i> Profile</a>
          <a class="dropdown-item" href="#"><i class="fa fa-wrench"></i> Settings</a>
          <a class="dropdown-item" href="#"><i class="fa fa-usd"></i> Payments<span class="badge badge-dark">42</span></a>
          <a class="dropdown-item" href="#"><i class="fa fa-file"></i> Projects<span class="badge badge-primary">42</span></a>
          <div class="divider"></div>
          <a class="dropdown-item" href="#"><i class="fa fa-shield"></i> Lock Account</a>
          <a class="dropdown-item" href="#"><i class="fa fa-lock"></i> Logout</a>
        </div>
      </li>
      <button class="navbar-toggler aside-menu-toggler" type="button">
        <span class="navbar-toggler-icon"></span>
      </button>

    </ul>
  </header>
  <div class="app-body">
    <div class="sidebar">
      <nav class="sidebar-nav">
        <ul class="nav">
          <li class="nav-item">
            <a class="nav-link" href="index.html"><i class="icon-speedometer"></i> Dashboard <span class="badge badge-info">NEW</span></a>
          </li>

          <li class="divider"></li>
          <li class="nav-title">
            UI Elements
          </li>
          <li class="nav-item nav-dropdown">
            <a class="nav-link nav-dropdown-toggle" href="#"><i class="icon-puzzle"></i> Components</a>
            <ul class="nav-dropdown-items">
              <li class="nav-item">
                <a class="nav-link" href="components-buttons.html"><i class="icon-puzzle"></i> Buttons</a>
              </li>
              <li class="nav-item">
                <a class="nav-link" href="components-social-buttons.html"><i class="icon-puzzle"></i> Social Buttons</a>
              </li>
              <li class="nav-item">
                <a class="nav-link" href="components-cards.html"><i class="icon-puzzle"></i> Cards</a>
              </li>
              <li class="nav-item">
                <a class="nav-link" href="components-modals.html"><i class="icon-puzzle"></i> Modals</a>
              </li>
              <li class="nav-item">
                <a class="nav-link" href="components-switches.html"><i class="icon-puzzle"></i> Switches</a>
              </li>
              <li class="nav-item">
                <a class="nav-link" href="components-tables.html"><i class="icon-puzzle"></i> Tables</a>
              </li>
              <li class="nav-item">
                <a class="nav-link" href="components-tabs.html"><i class="icon-puzzle"></i> Tabs</a>
              </li>
            </ul>
          </li>
          <li class="nav-item nav-dropdown">
            <a class="nav-link nav-dropdown-toggle" href="#"><i class="icon-note"></i> Forms</a>
            <ul class="nav-dropdown-items">
              <li class="nav-item">
                <a class="nav-link" href="forms-basic-forms.html"><i class="icon-note"></i> Basic Forms</a>
              </li>
              <li class="nav-item">
                <a class="nav-link" href="forms-advanced-forms.html"><i class="icon-note"></i> Advanced Forms</a>
              </li>
              <li class="nav-item">
                <a class="nav-link" href="forms-validation.html"><i class="icon-note"></i> Validation</a>
              </li>
            </ul>
          </li>
          <li class="nav-item nav-dropdown">
            <a class="nav-link nav-dropdown-toggle" href="#"><i class="fa fa-code"></i> Editors</a>
            <ul class="nav-dropdown-items">
              <li class="nav-item">
                <a class="nav-link" href="editors-code-editor.html"><i class="icon-note"></i> Code Editor</a>
              </li>
              <li class="nav-item">
                <a class="nav-link" href="editors-markdown-editor.html"><i class="fa fa-code"></i> Markdown Editor</a>
              </li>
              <li class="nav-item">
                <a class="nav-link" href="editors-text-editors.html"><i class="icon-note"></i> Rich Text Editor</a>
              </li>
            </ul>
          </li>
          <li class="nav-item nav-dropdown">
            <a class="nav-link nav-dropdown-toggle" href="#"><i class="icon-star"></i> Icons</a>
            <ul class="nav-dropdown-items">
              <li class="nav-item">
                <a class="nav-link" href="icons-font-awesome.html"><i class="icon-star"></i> Font Awesome <span class="badge badge-secondary">4.7</span></a>
              </li>
              <li class="nav-item">
                <a class="nav-link" href="icons-simple-line-icons.html"><i class="icon-star"></i> Simple Line Icons</a>
              </li>
            </ul>
          </li>
          <li class="nav-item nav-dropdown">
            <a class="nav-link nav-dropdown-toggle" href="#"><i class="icon-energy"></i> Plugins</a>
            <ul class="nav-dropdown-items">
              <li class="nav-item">
                <a class="nav-link" href="plugins-calendar.html"><i class="icon-calendar"></i> Calendar</a>
              </li>
              <li class="nav-item">
                <a class="nav-link" href="plugins-draggable-cards.html"><i class="icon-cursor-move"></i> Draggable Cards</a>
              </li>
              <li class="nav-item">
                <a class="nav-link" href="plugins-loading-buttons.html"><i class="icon-cursor"></i> Loading Buttons</a>
              </li>
              <li class="nav-item">
                <a class="nav-link" href="plugins-notifications.html"><i class="icon-info"></i> Notifications</a>
              </li>
              <li class="nav-item">
                <a class="nav-link" href="plugins-sliders.html"><i class="icon-equalizer"></i> Sliders</a>
              </li>
              <li class="nav-item">
                <a class="nav-link" href="plugins-spinners.html"><i class="fa fa-spinner"></i> Spinners</a>
              </li>
              <li class="nav-item">
                <a class="nav-link" href="plugins-tables.html"><i class="icon-list"></i> Tables</a>
              </li>
            </ul>
          </li>
          <li class="nav-item">
            <a class="nav-link" href="widgets.html"><i class="icon-calculator"></i> Widgets <span class="badge badge-info">NEW</span></a>
          </li>
          <li class="nav-item">
            <a class="nav-link" href="charts.html"><i class="icon-pie-chart"></i> Charts</a>
          </li>
          <li class="nav-item">
            <a class="nav-link" href="google-maps.html"><i class="icon-map"></i> Google Maps <span class="badge badge-info">NEW</span></a>
          </li>
          <li class="divider"></li>
          <li class="nav-title">
            Extras
          </li>
          <li class="nav-item nav-dropdown">
            <a class="nav-link nav-dropdown-toggle" href="#"><i class="icon-star"></i> Pages</a>
            <ul class="nav-dropdown-items">
              <li class="nav-item">
                <a class="nav-link" href="pages-login.html" target="_top"><i class="icon-star"></i> Login</a>
              </li>
              <li class="nav-item">
                <a class="nav-link" href="pages-register.html" target="_top"><i class="icon-star"></i> Register</a>
              </li>
              <li class="nav-item">
                <a class="nav-link" href="pages-404.html" target="_top"><i class="icon-star"></i> Error 404</a>
              </li>
              <li class="nav-item">
                <a class="nav-link" href="pages-500.html" target="_top"><i class="icon-star"></i> Error 500</a>
              </li>
            </ul>
          </li>
          <li class="nav-item nav-dropdown">
            <a class="nav-link nav-dropdown-toggle" href="#"><i class="icon-layers"></i> UI Kits</a>
            <ul class="nav-dropdown-items">
              <li class="nav-item nav-dropdown">
                <a class="nav-link nav-dropdown-toggle" href="#"><i class="icon-speech"></i> Invoicing</a>
                <ul class="nav-dropdown-items">
                  <li class="nav-item">
                    <a class="nav-link" href="UIkits-invoicing-invoice.html"><i class="icon-speech"></i> Invoice</a>
                  </li>
                </ul>
              </li>
              <li class="nav-item nav-dropdown">
                <a class="nav-link nav-dropdown-toggle" href="#"><i class="icon-speech"></i> Email</a>
                <ul class="nav-dropdown-items">
                  <li class="nav-item">
                    <a class="nav-link" href="UIkits-email-inbox.html"><i class="icon-speech"></i> Inbox</a>
                  </li>
                  <li class="nav-item">
                    <a class="nav-link" href="UIkits-email-message.html"><i class="icon-speech"></i> Message</a>
                  </li>
                  <li class="nav-item">
                    <a class="nav-link" href="UIkits-email-compose.html"><i class="icon-speech"></i> Compose</a>
                  </li>
                </ul>
              </li>
            </ul>
          </li>
          <li class="divider m-2"></li>
          <li class="nav-title">
            Labels
          </li>
          <li class="nav-item hidden-cn">
            <a class="nav-label" href="#"><i class="fa fa-circle text-danger"></i> Label danger</a>
          </li>
          <li class="nav-item hidden-cn">
            <a class="nav-label" href="#"><i class="fa fa-circle text-info"></i> Label info</a>
          </li>
          <li class="nav-item hidden-cn">
            <a class="nav-label" href="#"><i class="fa fa-circle text-warning"></i> Label warning</a>
          </li>
          <li class="divider"></li>
          <li class="nav-title">
            System Utilization
          </li>
          <li class="nav-item px-3 hidden-cn">
            <div class="text-uppercase mb-1">
              <small><b>CPU Usage</b></small>
            </div>
            <div class="progress progress-xs">
              <div class="progress-bar bg-info" role="progressbar" style="width: 25%" aria-valuenow="25" aria-valuemin="0" aria-valuemax="100"></div>
            </div>
            <small class="text-muted">348 Processes. 1/4 Cores.</small>
          </li>
          <li class="nav-item px-3 hidden-cn">
            <div class="text-uppercase mb-1">
              <small><b>Memory Usage</b></small>
            </div>
            <div class="progress progress-xs">
              <div class="progress-bar bg-warning" role="progressbar" style="width: 70%" aria-valuenow="70" aria-valuemin="0" aria-valuemax="100"></div>
            </div>
            <small class="text-muted">11444GB/16384MB</small>
          </li>
          <li class="nav-item px-3 hidden-cn">
            <div class="text-uppercase mb-1">
              <small><b>SSD 1 Usage</b></small>
            </div>
            <div class="progress progress-xs">
              <div class="progress-bar bg-danger" role="progressbar" style="width: 95%" aria-valuenow="95" aria-valuemin="0" aria-valuemax="100"></div>
            </div>
            <small class="text-muted">243GB/256GB</small>
          </li>

        </ul>
      </nav>
      <button class="sidebar-minimizer brand-minimizer" type="button"></button>
    </div>
    <!-- Main content -->
    <main class="main">
        @yeild('content')
    </main>
    

    <aside class="aside-menu">
      <ul class="nav nav-tabs" role="tablist">
        <li class="nav-item">
          <a class="nav-link active" data-toggle="tab" href="#timeline" role="tab"><i class="icon-list"></i></a>
        </li>
        <li class="nav-item">
          <a class="nav-link" data-toggle="tab" href="#messages" role="tab"><i class="icon-speech"></i></a>
        </li>
        <li class="nav-item">
          <a class="nav-link" data-toggle="tab" href="#settings" role="tab"><i class="icon-settings"></i></a>
        </li>
      </ul>
      <!-- Tab panes -->
      <div class="tab-content">
        <div class="tab-pane active" id="timeline" role="tabpanel">
          <div class="callout m-0 py-2 text-muted text-center bg-light text-uppercase">
            <small><b>Today</b></small>
          </div>
          <hr class="transparent mx-3 my-0">
          <div class="callout callout-warning m-0 py-3">
            <div class="avatar float-right">
              <img src="img/avatars/7.jpg" class="img-avatar" alt="admin@bootstrapmaster.com">
            </div>
            <div>Meeting with
              <strong>Lucas</strong>
            </div>
            <small class="text-muted mr-3"><i class="icon-calendar"></i>&nbsp; 1 - 3pm</small>
            <small class="text-muted"><i class="icon-location-pin"></i>&nbsp; Palo Alto, CA </small>
          </div>
          <hr class="mx-3 my-0">
          <div class="callout callout-info m-0 py-3">
            <div class="avatar float-right">
              <img src="img/avatars/4.jpg" class="img-avatar" alt="admin@bootstrapmaster.com">
            </div>
            <div>Skype with
              <strong>Megan</strong>
            </div>
            <small class="text-muted mr-3"><i class="icon-calendar"></i>&nbsp; 4 - 5pm</small>
            <small class="text-muted"><i class="icon-social-skype"></i>&nbsp; On-line </small>
          </div>
          <hr class="transparent mx-3 my-0">
          <div class="callout m-0 py-2 text-muted text-center bg-light text-uppercase">
            <small><b>Tomorrow</b></small>
          </div>
          <hr class="transparent mx-3 my-0">
          <div class="callout callout-danger m-0 py-3">
            <div>New UI Project -
              <strong>deadline</strong>
            </div>
            <small class="text-muted mr-3"><i class="icon-calendar"></i>&nbsp; 10 - 11pm</small>
            <small class="text-muted"><i class="icon-home"></i>&nbsp; creativeLabs HQ </small>
            <div class="avatars-stack mt-2">
              <div class="avatar avatar-xs">
                <img src="img/avatars/2.jpg" class="img-avatar" alt="admin@bootstrapmaster.com">
              </div>
              <div class="avatar avatar-xs">
                <img src="img/avatars/3.jpg" class="img-avatar" alt="admin@bootstrapmaster.com">
              </div>
              <div class="avatar avatar-xs">
                <img src="img/avatars/4.jpg" class="img-avatar" alt="admin@bootstrapmaster.com">
              </div>
              <div class="avatar avatar-xs">
                <img src="img/avatars/5.jpg" class="img-avatar" alt="admin@bootstrapmaster.com">
              </div>
              <div class="avatar avatar-xs">
                <img src="img/avatars/6.jpg" class="img-avatar" alt="admin@bootstrapmaster.com">
              </div>
            </div>
          </div>
          <hr class="mx-3 my-0">
          <div class="callout callout-success m-0 py-3">
            <div>
              <strong>#10 Startups.Garden</strong> Meetup</div>
            <small class="text-muted mr-3"><i class="icon-calendar"></i>&nbsp; 1 - 3pm</small>
            <small class="text-muted"><i class="icon-location-pin"></i>&nbsp; Palo Alto, CA </small>
          </div>
          <hr class="mx-3 my-0">
          <div class="callout callout-primary m-0 py-3">
            <div>
              <strong>Team meeting</strong>
            </div>
            <small class="text-muted mr-3"><i class="icon-calendar"></i>&nbsp; 4 - 6pm</small>
            <small class="text-muted"><i class="icon-home"></i>&nbsp; creativeLabs HQ </small>
            <div class="avatars-stack mt-2">
              <div class="avatar avatar-xs">
                <img src="img/avatars/2.jpg" class="img-avatar" alt="admin@bootstrapmaster.com">
              </div>
              <div class="avatar avatar-xs">
                <img src="img/avatars/3.jpg" class="img-avatar" alt="admin@bootstrapmaster.com">
              </div>
              <div class="avatar avatar-xs">
                <img src="img/avatars/4.jpg" class="img-avatar" alt="admin@bootstrapmaster.com">
              </div>
              <div class="avatar avatar-xs">
                <img src="img/avatars/5.jpg" class="img-avatar" alt="admin@bootstrapmaster.com">
              </div>
              <div class="avatar avatar-xs">
                <img src="img/avatars/6.jpg" class="img-avatar" alt="admin@bootstrapmaster.com">
              </div>
              <div class="avatar avatar-xs">
                <img src="img/avatars/7.jpg" class="img-avatar" alt="admin@bootstrapmaster.com">
              </div>
              <div class="avatar avatar-xs">
                <img src="img/avatars/8.jpg" class="img-avatar" alt="admin@bootstrapmaster.com">
              </div>
            </div>
          </div>
          <hr class="mx-3 my-0">
        </div>
        <div class="tab-pane p-3" id="messages" role="tabpanel">
          <div class="message">
            <div class="py-3 pb-5 mr-3 float-left">
              <div class="avatar">
                <img src="img/avatars/7.jpg" class="img-avatar" alt="admin@bootstrapmaster.com">
                <span class="avatar-status badge-success"></span>
              </div>
            </div>
            <div>
              <small class="text-muted">Lukasz Holeczek</small>
              <small class="text-muted float-right mt-1">1:52 PM</small>
            </div>
            <div class="text-truncate font-weight-bold">Lorem ipsum dolor sit amet</div>
            <small class="text-muted">Lorem ipsum dolor sit amet, consectetur adipisicing elit, sed do eiusmod tempor incididunt...</small>
          </div>
          <hr>
          <div class="message">
            <div class="py-3 pb-5 mr-3 float-left">
              <div class="avatar">
                <img src="img/avatars/7.jpg" class="img-avatar" alt="admin@bootstrapmaster.com">
                <span class="avatar-status badge-success"></span>
              </div>
            </div>
            <div>
              <small class="text-muted">Lukasz Holeczek</small>
              <small class="text-muted float-right mt-1">1:52 PM</small>
            </div>
            <div class="text-truncate font-weight-bold">Lorem ipsum dolor sit amet</div>
            <small class="text-muted">Lorem ipsum dolor sit amet, consectetur adipisicing elit, sed do eiusmod tempor incididunt...</small>
          </div>
          <hr>
          <div class="message">
            <div class="py-3 pb-5 mr-3 float-left">
              <div class="avatar">
                <img src="img/avatars/7.jpg" class="img-avatar" alt="admin@bootstrapmaster.com">
                <span class="avatar-status badge-success"></span>
              </div>
            </div>
            <div>
              <small class="text-muted">Lukasz Holeczek</small>
              <small class="text-muted float-right mt-1">1:52 PM</small>
            </div>
            <div class="text-truncate font-weight-bold">Lorem ipsum dolor sit amet</div>
            <small class="text-muted">Lorem ipsum dolor sit amet, consectetur adipisicing elit, sed do eiusmod tempor incididunt...</small>
          </div>
          <hr>
          <div class="message">
            <div class="py-3 pb-5 mr-3 float-left">
              <div class="avatar">
                <img src="img/avatars/7.jpg" class="img-avatar" alt="admin@bootstrapmaster.com">
                <span class="avatar-status badge-success"></span>
              </div>
            </div>
            <div>
              <small class="text-muted">Lukasz Holeczek</small>
              <small class="text-muted float-right mt-1">1:52 PM</small>
            </div>
            <div class="text-truncate font-weight-bold">Lorem ipsum dolor sit amet</div>
            <small class="text-muted">Lorem ipsum dolor sit amet, consectetur adipisicing elit, sed do eiusmod tempor incididunt...</small>
          </div>
          <hr>
          <div class="message">
            <div class="py-3 pb-5 mr-3 float-left">
              <div class="avatar">
                <img src="img/avatars/7.jpg" class="img-avatar" alt="admin@bootstrapmaster.com">
                <span class="avatar-status badge-success"></span>
              </div>
            </div>
            <div>
              <small class="text-muted">Lukasz Holeczek</small>
              <small class="text-muted float-right mt-1">1:52 PM</small>
            </div>
            <div class="text-truncate font-weight-bold">Lorem ipsum dolor sit amet</div>
            <small class="text-muted">Lorem ipsum dolor sit amet, consectetur adipisicing elit, sed do eiusmod tempor incididunt...</small>
          </div>
        </div>
        <div class="tab-pane p-3" id="settings" role="tabpanel">
          <h6>Settings</h6>
          <div class="aside-options">
            <div class="clearfix mt-4">
              <small><b>Option 1</b></small>
              <label class="switch switch-text switch-pill switch-success switch-sm float-right">
                <input type="checkbox" class="switch-input" checked="">
                <span class="switch-label" data-on="On" data-off="Off"></span>
                <span class="switch-handle"></span>
              </label>
            </div>
            <div>
              <small class="text-muted">Lorem ipsum dolor sit amet, consectetur adipisicing elit, sed do eiusmod tempor incididunt ut labore et dolore magna aliqua.</small>
            </div>
          </div>
          <div class="aside-options">
            <div class="clearfix mt-3">
              <small><b>Option 2</b></small>
              <label class="switch switch-text switch-pill switch-success switch-sm float-right">
                <input type="checkbox" class="switch-input">
                <span class="switch-label" data-on="On" data-off="Off"></span>
                <span class="switch-handle"></span>
              </label>
            </div>
            <div>
              <small class="text-muted">Lorem ipsum dolor sit amet, consectetur adipisicing elit, sed do eiusmod tempor incididunt ut labore et dolore magna aliqua.</small>
            </div>
          </div>
          <div class="aside-options">
            <div class="clearfix mt-3">
              <small><b>Option 3</b></small>
              <label class="switch switch-text switch-pill switch-success switch-sm float-right">
                <input type="checkbox" class="switch-input">
                <span class="switch-label" data-on="On" data-off="Off"></span>
                <span class="switch-handle"></span>
              </label>
            </div>
          </div>
          <div class="aside-options">
            <div class="clearfix mt-3">
              <small><b>Option 4</b></small>
              <label class="switch switch-text switch-pill switch-success switch-sm float-right">
                <input type="checkbox" class="switch-input" checked="">
                <span class="switch-label" data-on="On" data-off="Off"></span>
                <span class="switch-handle"></span>
              </label>
            </div>
          </div>
          <hr>
          <h6>System Utilization</h6>
          <div class="text-uppercase mb-1 mt-4">
            <small><b>CPU Usage</b></small>
          </div>
          <div class="progress progress-xs">
            <div class="progress-bar bg-info" role="progressbar" style="width: 25%" aria-valuenow="25" aria-valuemin="0" aria-valuemax="100"></div>
          </div>
          <small class="text-muted">348 Processes. 1/4 Cores.</small>
          <div class="text-uppercase mb-1 mt-2">
            <small><b>Memory Usage</b></small>
          </div>
          <div class="progress progress-xs">
            <div class="progress-bar bg-warning" role="progressbar" style="width: 70%" aria-valuenow="70" aria-valuemin="0" aria-valuemax="100"></div>
          </div>
          <small class="text-muted">11444GB/16384MB</small>
          <div class="text-uppercase mb-1 mt-2">
            <small><b>SSD 1 Usage</b></small>
          </div>
          <div class="progress progress-xs">
            <div class="progress-bar bg-danger" role="progressbar" style="width: 95%" aria-valuenow="95" aria-valuemin="0" aria-valuemax="100"></div>
          </div>
          <small class="text-muted">243GB/256GB</small>
          <div class="text-uppercase mb-1 mt-2">
            <small><b>SSD 2 Usage</b></small>
          </div>
          <div class="progress progress-xs">
            <div class="progress-bar bg-success" role="progressbar" style="width: 10%" aria-valuenow="10" aria-valuemin="0" aria-valuemax="100"></div>
          </div>
          <small class="text-muted">25GB/256GB</small>
        </div>
      </div>
    </aside>

  </div>
  <footer class="app-footer">
    <span><a href="https://genesisui.com">Prime</a> © 2017 creativeLabs.</span>
    <span class="ml-auto">Powered by <a href="https://genesisui.com">GenesisUI</a></span>
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

</body>
</html>