<link href="{{ asset(path: 'assets/dashboard/libs/sweetalert2/sweetalert2.min.css') }}" rel="stylesheet" type="text/css"/>

@if($pluginsStyles !== null)
    {{ $pluginsStyles }}
@endif

<link href="{{ asset(path: 'assets/dashboard/css/bootstrap-rtl.min.css') }}" rel="stylesheet" type="text/css"/>
<link href="{{ asset(path: 'assets/dashboard/css/icons.min.css') }}" rel="stylesheet" type="text/css"/>
<link href="{{ asset(path: 'assets/dashboard/css/app-rtl.min.css') }}" rel="stylesheet" type="text/css"/>

<style>
    .swal2-popup.swal2-toast .swal2-title {
        line-height: 20px;
    }
</style>

@if($styles !== null)
    {{ $styles }}
@endif
