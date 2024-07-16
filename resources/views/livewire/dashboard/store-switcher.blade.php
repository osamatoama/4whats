<li class="dropdown">
    <a class="nav-link dropdown-toggle" data-bs-toggle="dropdown" href="#" role="button" aria-haspopup="false" aria-expanded="false">
        {{ $currentStore->name }} ({{ $currentStore->provider_type->label() }})
        @if($hasMoreStores)
            <i class="mdi mdi-chevron-down"></i>
        @endif
        <div>
            @lang('dashboard.common.subscription_expired_at') {{ $subscriptionExpiredAt }}
        </div>
    </a>
    @if($hasMoreStores)
        <div class="dropdown-menu dropdown-menu-end">
            @foreach($stores as $store)
                <button class="dropdown-item" wire:key="{{ $store->id }}" wire:click="switch({{ $store->id }})">
                    {{ $store->name }} ({{ $store->provider_type->label() }})
                </button>
            @endforeach
        </div>
    @endif
</li>
