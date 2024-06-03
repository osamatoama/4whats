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
    <link href="{{ asset('assets/dashboard/css/bootstrap-rtl.min.css') }}" rel="stylesheet" type="text/css"/>
    <link href="{{ asset('assets/dashboard/css/icons.min.css') }}" rel="stylesheet" type="text/css"/>
    <link href="{{ asset('assets/dashboard/css/app-rtl.min.css') }}" rel="stylesheet" type="text/css"/>
    @if(isset($styles))
        {{ $styles }}
    @endif
</head>

<body id="body" class="auth-page" style="background-image: url('{{ asset('assets/dashboard/images/p-1.png') }}'); background-size: cover; background-position: center center;">

<div class="container-md">
    <div class="row vh-100 d-flex justify-content-center">
        <div class="col-12 align-self-center">
            <div class="card-body">
                <div class="row">
                    <div class="col-lg-4 mx-auto">
                        <div class="card">
                            <div class="card-body p-0 auth-header-box">
                                <div class="text-center p-3">
                                    <a href="{{ route('dashboard.home') }}" class="logo logo-admin">
                                        <img src="{{ asset('assets/dashboard/images/logo-sm.png') }}" height="50" alt="logo" class="auth-logo">
                                    </a>
                                    @if(isset($header))
                                        {{ $header }}
                                    @endif
                                </div>
                            </div>
                            <div class="card-body pt-0">
                                @session('status')
                                <div class="alert alert-success mt-4">{{ $value }}</div>
                                @endsession

                                @session('success')
                                <div class="alert alert-success mt-4">{{ $value }}</div>
                                @endsession

                                @session('error')
                                <div class="alert alert-danger mt-4">{{ $value }}</div>
                                @endsession

                                {{ $slot }}
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<script src="{{ asset('assets/dashboard/libs/bootstrap/js/bootstrap.bundle.min.js') }}"></script>
<script src="{{ asset('assets/dashboard/libs/simplebar/simplebar.min.js') }}"></script>
<script src="{{ asset('assets/dashboard/libs/feather-icons/feather.min.js') }}"></script>
@if(isset($pluginsScripts))
    {{ $pluginsScripts }}
@endif
<script src="{{ asset('assets/dashboard/js/app.js') }}"></script>
@if(isset($scripts))
    {{ $scripts }}
@endif
</body>
</html>
