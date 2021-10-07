<!DOCTYPE html>
<html lang="en">
    <head>

        <!-- Required meta tags -->
        <meta charset="utf-8">
        <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no">

        <!-- Favicon -->
        <link rel="shortcut icon" type="image/x-icon" href="{{ asset('assets/img/favicon.png') }}">

        <title>Warehouse Management System</title>

        <!-- vendor css -->
        <link href="{{ asset('dashforge/lib/@fortawesome/fontawesome-free/css/all.min.css" rel="stylesheet') }}">
        <link href="{{ asset('dashforge/lib/ionicons/css/ionicons.min.css" rel="stylesheet') }}">
        <link href="{{ asset('dashforge/lib/jqvmap/jqvmap.min.css" rel="stylesheet') }}">

        <!-- DashForge CSS -->
        <link rel="stylesheet" href="{{ asset('dashforge/css/dashforge.css') }}">
        <link rel="stylesheet" href="{{ asset('dashforge/css/dashforge.dashboard.css') }}">

        <link href="{{ mix('/css/app.css') }}" rel="stylesheet" />
        <script src="{{ mix('/js/app.js') }}" defer></script>
    </head>
    <body>
        @routes
        @inertia

        <script src="{{ asset('dashforge/lib/jquery/jquery.min.js') }}"></script>
        <script src="{{ asset('dashforge/lib/bootstrap/js/bootstrap.bundle.min.js') }}"></script>
        <script src="{{ asset('dashforge/lib/feather-icons/feather.min.js') }}"></script>
        <script src="{{ asset('dashforge/lib/perfect-scrollbar/perfect-scrollbar.min.js') }}"></script>
        <script src="{{ asset('dashforge/lib/jquery.flot/jquery.flot.js') }}"></script>
        <script src="{{ asset('dashforge/lib/jquery.flot/jquery.flot.stack.js') }}"></script>
        <script src="{{ asset('dashforge/lib/jquery.flot/jquery.flot.resize.js') }}"></script>
        <script src="{{ asset('dashforge/lib/chart.js/Chart.bundle.min.js') }}"></script>
        <script src="{{ asset('dashforge/lib/jqvmap/jquery.vmap.min.js') }}"></script>
        <script src="{{ asset('dashforge/lib/jqvmap/maps/jquery.vmap.usa.js') }}"></script>

        <script src="{{ asset('dashforge/js/dashforge.js') }}"></script>
        <script src="{{ asset('dashforge/js/dashforge.aside.js') }}"></script>
        <script src="{{ asset('dashforge/js/dashforge.sampledata.js') }}"></script>
    </body>
</html>