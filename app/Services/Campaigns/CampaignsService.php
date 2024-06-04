<?php

namespace App\Services\Campaigns;

use App\Enums\CampaignType;
use App\Enums\Jobs\BatchName;
use App\Jobs\Campaigns\AbandonedCarts\SendAbandonedCartsCampaignJob;
use App\Jobs\Campaigns\Contacts\SendContactsCampaignJob;
use App\Models\Store;
use App\Services\Queue\BatchService;
use Illuminate\Support\Collection;

readonly class CampaignsService
{
    public function __construct(
        public Store $store,
    ) {
    }

    public function getRunningCampaigns(): Collection
    {
        return BatchService::getRunningBatchesQuery(
            batchName: $this->getBatchNames(),
            storeId: $this->store->id,
        )->get();
    }

    public function getRunningCampaignsCount(): int
    {
        return BatchService::getRunningBatchesCount(
            batchName: $this->getBatchNames(),
            storeId: $this->store->id,
        );
    }

    public function send(CampaignType $campaignType, string $message): void
    {
        $batchName = $this->getBatchName(
            campaignType: $campaignType,
        );

        $jobClassName = $this->getJobClassName(
            campaignType: $campaignType,
        );

        BatchService::createPendingBatch(
            jobs: new $jobClassName(
                store: $this->store,
                message: $message,
            ),
            batchName: $batchName,
            storeId: $this->store->id,
        )->allowFailures()->dispatch();
    }

    protected function getBatchNames(): array
    {
        return [
            BatchName::CAMPAIGNS_CONTACTS,
            BatchName::CAMPAIGNS_ABANDONED_CARTS,
        ];
    }

    protected function getBatchName(CampaignType $campaignType): BatchName
    {
        return match ($campaignType) {
            CampaignType::CONTACTS => BatchName::CAMPAIGNS_CONTACTS,
            CampaignType::ABANDONED_CARTS => BatchName::CAMPAIGNS_ABANDONED_CARTS,
        };
    }

    protected function getJobClassName(CampaignType $campaignType): string
    {
        return match ($campaignType) {
            CampaignType::CONTACTS => SendContactsCampaignJob::class,
            CampaignType::ABANDONED_CARTS => SendAbandonedCartsCampaignJob::class,
        };
    }
}
