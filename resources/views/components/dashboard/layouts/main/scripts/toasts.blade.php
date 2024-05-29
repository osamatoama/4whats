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
        Livewire.on('toasts.success', (event) => {
            toast(event.message);
        });

        Livewire.on('toasts.warning', (event) => {
            toast(event.message, 'warning');
        });

        Livewire.on('toasts.error', (event) => {
            toast(event.message, 'error');
        });
    });
</script>
