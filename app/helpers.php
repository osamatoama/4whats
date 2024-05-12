<?php

use App\Support\Settings;

if (! function_exists('resolveSingletonIf')) {
    function resolveSingletonIf(string $abstract, ?Closure $concrete = null): mixed
    {
        app()->singletonIf(abstract: $abstract, concrete: $concrete ?? $abstract);

        return resolve(name: $abstract);
    }
}

if (! function_exists('settings')) {
    function settings(?int $storeId = null): Settings
    {
        return resolveSingletonIf(
            abstract: Settings::class.':'.$storeId ?? 'system',
            concrete: fn (): Settings => new Settings(storeId: $storeId),
        );
    }
}
