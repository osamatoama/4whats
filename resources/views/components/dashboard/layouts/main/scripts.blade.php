<script src="{{ asset(path: 'assets/dashboard/libs/bootstrap/js/bootstrap.bundle.min.js') }}"></script>
<script src="{{ asset(path: 'assets/dashboard/libs/simplebar/simplebar.min.js') }}"></script>
<script src="{{ asset(path: 'assets/dashboard/libs/feather-icons/feather.min.js') }}"></script>
<script src="{{ asset('assets/dashboard/libs/sweetalert2/sweetalert2.min.js') }}"></script>

@if($pluginsScripts !== null)
    {{ $pluginsScripts }}
@endif

<script src="{{ asset(path: 'assets/dashboard/js/app.js') }}"></script>

<script>
    const toast = (title, icon = 'success') => {
        const Toast = Swal.mixin({
            toast: true,
            position: 'bottom-start',
            showConfirmButton: false,
            timer: 3000,
            timerProgressBar: true,
            didOpen: (toast) => {
                toast.onmouseenter = Swal.stopTimer;
                toast.onmouseleave = Swal.resumeTimer;
            }
        });
        Toast.fire({
            icon: icon,
            title: title
        });
    }

    @session('success')
    toast('{{ $value }}');
    @endsession

    @session('error')
    toast('{{ $value }}', 'error');
    @endsession

    document.addEventListener('livewire:init', () => {
        Livewire.on('alerts.success', (event) => {
            toast(event.message);
        });
    });
</script>

@if($scripts !== null)
    {{ $scripts }}
@endif
