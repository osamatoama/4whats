<?php

use App\Models\Store;
use App\Models\User;
use App\Support\Settings;
use Illuminate\Database\Eloquent\Collection;

if (! function_exists('resolveSingletonIf')) {
    function resolveSingletonIf(string $abstract, ?Closure $concrete = null): mixed
    {
        app()->singletonIf(abstract: $abstract, concrete: $concrete ?? $abstract);

        return resolve(name: $abstract);
    }
}

if (! function_exists('settings')) {
    function settings(?int $storeId = null, bool $eager = true): Settings
    {
        return resolveSingletonIf(
            abstract: Settings::class.':'.($storeId ?? 'system').':'.($eager ? 'eager' : 'lazy'),
            concrete: fn (): Settings => new Settings(storeId: $storeId, eager: $eager),
        );
    }
}

if (! function_exists('parentUser')) {
    function parentUser(?User $user = null): User
    {
        $user ??= request()->user();
        if ($user->is_parent) {
            return $user;
        }

        return parentUser(user: $user->parent);
    }
}

if (! function_exists('parentUserStores')) {
    function parentUserStores(): Collection
    {
        return once(callback: fn (): Collection => parentUser()->stores);
    }
}

if (! function_exists('currentStore')) {
    function currentStore(): Store
    {
        $key = 'current_store_id';
        if (! session()->has(key: $key)) {
            session()->put(key: $key, value: parentUser()->stores->first()->id);
        }

        return once(
            callback: function () use ($key): Store {
                $store = parentUserStores()->firstWhere(
                    key: 'id',
                    operator: '=',
                    value: session()->get(
                        key: $key,
                    ),
                );

                if ($store === null) {
                    $store = parentUser()->stores->first();
                    session()->put(
                        key: $key,
                        value: $store,
                    );
                }

                return $store;
            },
        );
    }
}

if (! function_exists('isInBlacklistedMobiles')) {
    function isInBlacklistedMobiles(string $mobile, ?Store $store = null): bool
    {
        $store ??= currentStore();

        return $store->relationLoaded(key: 'blacklistedMobiles')
            ? $store->blacklistedMobiles->contains(key: 'mobile', value: $mobile)
            : $store->blacklistedMobiles()->mobile(mobile: $mobile)->exists();
    }
}

if (! function_exists('isNotInBlacklistedMobiles')) {
    function isNotInBlacklistedMobiles(string $mobile, ?Store $store = null): bool
    {
        return ! isInBlacklistedMobiles(mobile: $mobile, store: $store);
    }
}

if (! function_exists('generateMessageUsingSeparatedLines')) {
    function generateMessageUsingSeparatedLines(array $lines): string
    {
        $message = '';
        foreach ($lines as $key => $line) {
            if ($key !== 0) {
                $message .= PHP_EOL;
            }

            $message .= $line;
        }

        return $message;
    }
}

if (! function_exists('ensureMobileStartingWithPlus')) {
    function ensureMobileStartingWithPlus(string $mobile): string
    {
        return str(string: $mobile)->start(prefix: '+')->toString();
    }
}
