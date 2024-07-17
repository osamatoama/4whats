<li class="dropdown w-100">
    <a class="nav-link dropdown-toggle" data-bs-toggle="dropdown" href="#" role="button" aria-haspopup="false" aria-expanded="false">
        <div class="d-flex flex-column justify-content-between align-items-start flex-md-row-reverse align-items-md-center gap-md-3">
            <div>
                {{ $currentStore->name }} ({{ $currentStore->provider_type->label() }})
                @if($hasMoreStores)
                    <i class="mdi mdi-chevron-down"></i>
                @endif
            </div>
            <div class="d-flex flex-column">
                <small>
                    @lang('dashboard.common.subscription_expired_at') {{ $subscriptionExpiredAt }}
                </small>
                <small>
                    @lang('dashboard.common.subscription_type')
                    <span class="badge badge-outline-{{ $subscriptionTypeCssClass }}">
                        {{ $subscriptionType }}
                    </span>
                </small>
            </div>
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
