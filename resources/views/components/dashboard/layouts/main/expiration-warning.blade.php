@if(currentStore()->is_expired)
    <div class="mt-3 alert alert-danger">
        @lang('dashboard.common.store_expired_message')
    </div>
@endif
