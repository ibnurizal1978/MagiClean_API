<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Magiclean</title>
    
    <link rel="preconnect" href="https://fonts.gstatic.com">
    <link href="https://fonts.googleapis.com/css2?family=Nunito:wght@300;400;600;700;800&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="{{ asset('/css/bootstrap.css') }}">
    
    <link rel="stylesheet" href="{{ asset('/vendors/perfect-scrollbar/perfect-scrollbar.css') }}">
    <link rel="stylesheet" href="{{ asset('/vendors/bootstrap-icons/bootstrap-icons.css') }}">
    <link rel="stylesheet" href="{{ asset('/css/app.css') }}">
    <link rel="shortcut icon" href="{{ asset('/images/favicon.svg') }}" type="image/x-icon">
</head>

<body>
    <div id="app">
        <div id="sidebar" class="active">
            <div class="sidebar-wrapper active">
    <div class="sidebar-header">
        <div class="d-flex justify-content-between">
            <div class="logo">
                <a href="index.html">Backend</a>
            </div>
            <div class="toggler">
                <a href="#" class="sidebar-hide d-xl-none d-block"><i class="bi bi-x bi-middle"></i></a>
            </div>
        </div>
    </div>
    <div class="sidebar-menu">
        <ul class="menu">
            <li class="sidebar-title">Menu</li>
            
            <li class="sidebar-item">
                <a href="{{ route('users/view') }}" class='sidebar-link'>
                    <i class="bi bi-person-fill"></i>
                    <span>Users</span>
                </a>
            </li>
            
            <li class="sidebar-item">
                <a href="{{ route('leaderboard/view') }}" class='sidebar-link'>
                    <i class="bi bi-calendar2"></i>
                    <span>Leaderboard</span>
                </a>
            </li>

            <li
                class="sidebar-item  has-sub">
                <a href="#" class='sidebar-link'>
                    <i class="bi bi-bar-chart-line-fill"></i>
                    <span>Report</span>
                </a>
                <ul class="submenu ">
                    <li class="submenu-item ">
                        <a href="{{ route('report/report1') }}">Accumulative report</a>
                    </li>
                    <li class="submenu-item ">
                        <a href="{{ route('report/report2') }}">Daily Report</a>
                    </li>
                </ul>
            </li>


            <li class="sidebar-item has-sub">
                <a href="#" class='sidebar-link'>
                    <i class="bi bi-filter-circle-fill"></i>
                    <span>Log</span>
                </a>
                <ul class="submenu ">
                    <li class="submenu-item ">
                        <a href="{{ route('log/otp') }}">OTP Log</a>
                    </li>
                    <li class="submenu-item ">
                        <a href="{{ route('log/emailVoucher') }}">Email Voucher</a>
                    </li>
                </ul>
            </li>

            <li class="sidebar-item">
                <a href="{{ route('logout') }}" class='sidebar-link'>
                    <i class="bi bi-power"></i>
                    <span>Logout</span>
                </a>
            </li>
            
        </ul>
    </div>
    <button class="sidebar-toggler btn x"><i data-feather="x"></i></button>
</div>
        </div>
        <div id="main">
            <header class="mb-3">
                <a href="#" class="burger-btn d-block d-xl-none">
                    <i class="bi bi-justify fs-3"></i>
                </a>
            </header>
            @yield('content')

            <footer>
                <div class="footer clearfix mb-0 text-muted">
                    <div class="float-start">
                        <p>&copy;<?php echo date('Y') ?> Mazer Themes</p>
                    </div>
                    <div class="float-end">
                        <p>Crafted for <span class="text-danger"><i class="bi bi-heart"></i></span> Magiclean Backend</a></p>
                    </div>
                </div>
            </footer>
        </div>
    </div>
    <script src="{{ asset('/vendors/perfect-scrollbar/perfect-scrollbar.min.js') }}"></script>
    <script src="{{ asset('/js/bootstrap.bundle.min.js') }}"></script>
    
    <script src="{{ asset('/js/mazer.js') }}"></script>
</body>

</html>
