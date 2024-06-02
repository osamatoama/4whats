<?php

namespace App\Models;

use App\Enums\Jobs\BatchName;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;

class QueuedJobBatch extends Model
{
    public function getTable(): string
    {
        return config(
            key: 'queue.batching.table',
        );
    }

    /**
     * @param  BatchName|array<int, BatchName>  $batchName
     */
    public static function hasRunningBatches(BatchName|array $batchName, int $storeId, bool $onlyProcessing = true): bool
    {
        return once(
            callback: function () use ($batchName, $storeId, $onlyProcessing): bool {
                return static::getRunningBatchesQuery(
                    batchName: $batchName,
                    storeId: $storeId,
                    onlyProcessing: $onlyProcessing,
                )->exists();
            },
        );
    }

    /**
     * @param  BatchName|array<int, BatchName>  $batchName
     */
    public static function getRunningBatchesCount(BatchName|array $batchName, int $storeId, bool $onlyProcessing = true): bool
    {
        return once(
            callback: function () use ($batchName, $storeId, $onlyProcessing): int {
                return static::getRunningBatchesQuery(
                    batchName: $batchName,
                    storeId: $storeId,
                    onlyProcessing: $onlyProcessing,
                )->count();
            },
        );
    }

    /**
     * @param  BatchName|BatchName[]|array<int, string>  $batchName
     */
    public static function getRunningBatchesQuery(BatchName|array $batchName, int $storeId, bool $onlyProcessing = true): Builder
    {
        return static::query()
            ->when(
                value: $batchName instanceof BatchName,
                callback: fn (Builder $query): Builder => $query->where(
                    column: 'name',
                    operator: '=',
                    value: $batchName->generate(
                        storeId: $storeId,
                    ),
                ),
                default: function (Builder $query) use ($batchName, $storeId): Builder {
                    $names = [];
                    foreach ($batchName as $name) {
                        if ($name instanceof BatchName) {
                            $name = $name->generate(
                                storeId: $storeId,
                            );
                        }

                        $names[] = $name;
                    }

                    return $query->whereIn(
                        column: 'name',
                        values: $names,
                    );
                },
            )
            ->when(
                value: $onlyProcessing,
                callback: fn (Builder $query): Builder => $query->whereNull(
                    columns: ['cancelled_at', 'finished_at'],
                ),
            );
    }
}
