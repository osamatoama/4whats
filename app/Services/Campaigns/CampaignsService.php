<?php

namespace App\Services\Campaigns;

use App\Enums\CampaignType;
use App\Enums\Jobs\BatchName;
use App\Enums\Jobs\QueueName;
use App\Enums\Whatsapp\MessageType;
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

    public function send(
        CampaignType $campaignType,
        MessageType $messageType,
        ?string $message,
        ?string $filePath,
        ?string $imagePath,
        ?string $videoPath,
        ?string $audioPath,
    ): void {
        $batchName = $this->getBatchName(
            campaignType: $campaignType,
        );

        $jobClassName = $this->getJobClassName(
            campaignType: $campaignType,
        );

        BatchService::createPendingBatch(
            jobs: new $jobClassName(
                store: $this->store,
                messageType: $messageType,
                message: $message,
                filePath: $filePath,
                imagePath: $imagePath,
                videoPath: $videoPath,
                audioPath: $audioPath,
            ),
            batchName: $batchName,
            storeId: $this->store->id,
            queue: QueueName::OTHERS->value
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
