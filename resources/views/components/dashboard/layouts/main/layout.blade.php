<!DOCTYPE html>
<html lang="ar" dir="rtl">
<head>
    <meta charset="utf-8"/>
    <title>{{ $title }} | @lang('dashboard.common.title')</title>
    <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no">
    <meta content="4Whats" name="description"/>
    <meta content="Valinteca" name="author"/>
    <meta http-equiv="X-UA-Compatible" content="IE=edge"/>

    <!-- App favicon -->
    <link rel="shortcut icon" href="assets/images/favicon.ico">

    <!-- App css -->
    @if(isset($pluginsStyles))
        {{ $pluginsStyles }}
    @endif
    <link href="{{ asset(path: 'assets/dashboard/libs/sweetalert2/sweetalert2.min.css') }}" rel="stylesheet" type="text/css"/>
    <link href="{{ asset(path: 'assets/dashboard/css/bootstrap-rtl.min.css') }}" rel="stylesheet" type="text/css"/>
    <link href="{{ asset(path: 'assets/dashboard/css/icons.min.css') }}" rel="stylesheet" type="text/css"/>
    <link href="{{ asset(path: 'assets/dashboard/css/app-rtl.min.css') }}" rel="stylesheet" type="text/css"/>
    @if(isset($styles))
        {{ $styles }}
    @endif
</head>
<body id="body">

<x-dashboard.layouts.main.sidebar/>

<x-dashboard.layouts.main.navbar/>

<div class="page-wrapper">
    <div class="page-content-tab">
        <div class="container-fluid">
            <div class="row">
                <div class="col-sm-12">
                    <div class="page-title-box">
                        @if(isset($breadcrumb))
                            <div class="float-end">
                                {{ $breadcrumb }}
                            </div>
                        @endif
                        @if(isset($pageTitle))
                            <h4 class="page-title">
                                {{ $pageTitle }}
                            </h4>
                        @endif
                    </div>
                </div>
            </div>
            {{ $slot }}
        </div>
        <footer class="footer text-center text-sm-start">
            @lang('dashboard.footer.copyrights', ['date' => date(format: 'Y')])
            <span class="text-muted d-none d-sm-inline-block float-end">
                @lang('dashboard.footer.made_with_love_by_valinteca', ['icon' => '<i class="mdi mdi-heart text-danger"></i>'])
            </span>
        </footer>
    </div>
</div>

<script src="{{ asset(path: 'assets/dashboard/libs/bootstrap/js/bootstrap.bundle.min.js') }}"></script>
<script src="{{ asset(path: 'assets/dashboard/libs/simplebar/simplebar.min.js') }}"></script>
<script src="{{ asset(path: 'assets/dashboard/libs/feather-icons/feather.min.js') }}"></script>
@if(isset($pluginsScripts))
    {{ $pluginsScripts }}
@endif
<script src="{{ asset(path: 'assets/dashboard/js/app.js') }}"></script>
<script src="{{ asset('assets/dashboard/libs/sweetalert2/sweetalert2.min.js') }}"></script>
<script>
    @session('success')
    swal.fire({text: '{{ $value }}', icon: 'success'});
    @endsession
</script>
@if(isset($scripts))
    {{ $scripts }}
@endif
</body>
</html>
