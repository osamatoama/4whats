<?php

namespace App\Services\Campaigns;

use App\Enums\CampaignType;
use App\Enums\Jobs\BatchName;
use App\Jobs\Campaigns\AbandonedCarts\SendAbandonedCartsCampaignJob;
use App\Jobs\Campaigns\Contacts\SendContactsCampaignJob;
use App\Models\QueuedJobBatch;
use App\Models\Store;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Support\Facades\Bus;

readonly class CampaignsService
{
    public function __construct(
        public Store $store,
    ) {
    }

    public function getRunningCampaigns(): Collection
    {
        return QueuedJobBatch::getRunningBatchesQuery(
            batchName: $this->getBatchNames(),
            storeId: $this->store->id,
        )->get();
    }

    public function getRunningCampaignsCount(): int
    {
        return QueuedJobBatch::getRunningBatchesCount(
            batchName: $this->getBatchNames(),
            storeId: $this->store->id,
        );
    }

    public function send(CampaignType $campaignType, string $message): void
    {
        $batchName = $this->generateBatchNameFromCampaignType(
            campaignType: $campaignType,
        );

        $jobClassName = $this->getJobClassName(
            campaignType: $campaignType,
        );

        Bus::batch(
            jobs: new $jobClassName(
                store: $this->store,
                message: $message,
            ),
        )->name(
            name: $batchName,
        )->allowFailures()->dispatch();
    }

    protected function getBatchNames(): array
    {
        return [
            BatchName::CAMPAIGNS_CONTACTS,
            BatchName::CAMPAIGNS_ABANDONED_CARTS,
        ];
    }

    protected function generateBatchNameFromCampaignType(CampaignType $campaignType): string
    {
        $batchName = match ($campaignType) {
            CampaignType::CONTACTS => BatchName::CAMPAIGNS_CONTACTS,
            CampaignType::ABANDONED_CARTS => BatchName::CAMPAIGNS_ABANDONED_CARTS,
        };

        return $batchName->generate(
            storeId: $this->store->id,
        );
    }

    protected function getJobClassName(CampaignType $campaignType): string
    {
        return match ($campaignType) {
            CampaignType::CONTACTS => SendContactsCampaignJob::class,
            CampaignType::ABANDONED_CARTS => SendAbandonedCartsCampaignJob::class,
        };
    }
}
