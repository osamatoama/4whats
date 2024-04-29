<script src="{{ asset(path: 'assets/dashboard/libs/bootstrap/js/bootstrap.bundle.min.js') }}"></script>
<script src="{{ asset(path: 'assets/dashboard/libs/simplebar/simplebar.min.js') }}"></script>
<script src="{{ asset(path: 'assets/dashboard/libs/feather-icons/feather.min.js') }}"></script>
<script src="{{ asset('assets/dashboard/libs/sweetalert2/sweetalert2.min.js') }}"></script>

@if($pluginsScripts !== null)
    {{ $pluginsScripts }}
@endif

<script src="{{ asset(path: 'assets/dashboard/js/app.js') }}"></script>

<script>
    @session('success')
    swal.fire({text: '{{ $value }}', icon: 'success'});
    @endsession

    @session('error')
    swal.fire({text: '{{ $value }}', icon: 'error'});
    @endsession
</script>

@if($scripts !== null)
    {{ $scripts }}
@endif
