<li class="dropdown">
    <a class="nav-link dropdown-toggle" data-bs-toggle="dropdown" href="#" role="button" aria-haspopup="false" aria-expanded="false">
        {{ $currentStore->name }}
        @if($hasMoreStores)
            <i class="mdi mdi-chevron-down"></i>
        @endif
    </a>
    @if($hasMoreStores)
        <div class="dropdown-menu dropdown-menu-end">
            @foreach($stores as $store)
                <button class="dropdown-item" wire:key="{{ $store->id }}" wire:click="switch({{ $store->id }})">{{ $store->name }}</button>
            @endforeach
        </div>
    @endif
</li>
