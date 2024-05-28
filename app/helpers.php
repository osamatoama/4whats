<?php

use App\Enums\Jobs\JobBatchName;
use App\Models\Store;
use App\Models\User;
use App\Support\Settings;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Support\Facades\DB;

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

        $storeId = session()->get(key: $key);

        return once(callback: fn (): Store => parentUserStores()->firstWhere(key: 'id', operator: '=', value: $storeId));
    }
}

if (! function_exists('hasRunningBatches')) {
    function hasRunningBatches(JobBatchName $jobBatchName, int $storeId): bool
    {
        return once(callback: function () use ($jobBatchName, $storeId): bool {
            $batchesTableName = config(key: 'queue.batching.table');
            $name = $jobBatchName->generate(storeId: $storeId);

            return DB::table(table: $batchesTableName)
                ->where(column: 'name', operator: '=', value: $name)
                ->whereNull(columns: ['cancelled_at', 'finished_at'])
                ->exists();
        });
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
