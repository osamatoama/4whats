<?php

namespace App\Services\Queue;

use App\Enums\Jobs\BatchName;
use Closure;
use Illuminate\Bus\Batch;
use Illuminate\Bus\PendingBatch;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Database\Query\Builder;
use Illuminate\Support\Facades\Bus;
use Illuminate\Support\Facades\DB;

class BatchService
{
    public static function find(string $id): Batch
    {
        return Bus::findBatch(
            batchId: $id,
        );
    }

    /**
     * @param  ShouldQueue|ShouldQueue[]  $jobs
     */
    public static function createPendingBatch(
        ShouldQueue|array $jobs,
        BatchName $batchName,
        int $storeId,
        ?Closure $finallyCallback = null,
        bool $deleteWhenFinished = false,
    ): PendingBatch {
        $pendingBatch = Bus::batch(
            jobs: $jobs,
        )->name(
            name: $batchName->generate(
                storeId: $storeId,
            ),
        );

        if ($finallyCallback !== null || $deleteWhenFinished) {
            $pendingBatch->finally(
                callback: function (Batch $batch) use ($finallyCallback, $deleteWhenFinished): void {
                    if ($finallyCallback !== null) {
                        $finallyCallback(batch: $batch);
                    }

                    if ($deleteWhenFinished) {
                        $batch->delete();
                    }
                },
            );
        }

        return $pendingBatch;
    }

    /**
     * @param  BatchName|BatchName[]  $batchName
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
     * @param  BatchName|BatchName[]  $batchName
     */
    public static function doesntHaveRunningBatches(BatchName|array $batchName, int $storeId, bool $onlyProcessing = true): bool
    {
        return ! static::hasRunningBatches(
            batchName: $batchName,
            storeId: $storeId,
            onlyProcessing: $onlyProcessing,
        );
    }

    /**
     * @param  BatchName|BatchName[]  $batchName
     */
    public static function getRunningBatchesCount(BatchName|array $batchName, int $storeId, bool $onlyProcessing = true): int
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
     * @param  BatchName|BatchName[]|array<string>  $batchName
     */
    public static function getRunningBatchesQuery(BatchName|array $batchName, int $storeId, bool $onlyProcessing = true): Builder
    {
        $batchesTable = config(
            key: 'queue.batching.table',
        );

        return DB::table(table: $batchesTable)
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
