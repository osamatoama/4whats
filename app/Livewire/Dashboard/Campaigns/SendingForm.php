<?php

namespace App\Livewire\Dashboard\Campaigns;

use App\Enums\CampaignType;
use App\Enums\Whatsapp\MessageType;
use App\Livewire\Concerns\ConvertEmptyStringsToNull;
use App\Livewire\Concerns\InteractsWithToasts;
use App\Models\User;
use App\Services\Campaigns\CampaignsService;
use Illuminate\Support\Arr;
use Illuminate\Support\Facades\Gate;
use Illuminate\Validation\Rule;
use Illuminate\View\View;
use Livewire\Component;
use Livewire\Features\SupportFileUploads\TemporaryUploadedFile;
use Livewire\WithFileUploads;

class SendingForm extends Component
{
    use ConvertEmptyStringsToNull, InteractsWithToasts, WithFileUploads;

    public array $campaignTypes;

    public CampaignType $currentCampaignType;

    public array $messageTypes;

    public MessageType $currentMessageType;

    public ?string $message = null;

    public ?TemporaryUploadedFile $image = null;

    public ?TemporaryUploadedFile $video = null;

    public ?TemporaryUploadedFile $audio = null;

    public function sendCampaign(): void
    {
        Gate::authorize(
            ability: 'sendCampaigns',
            arguments: User::class,
        );

        if (currentStore()->is_uninstalled || currentStore()->is_expired) {
            $this->customErrorToast(
                message: __(
                    key: 'dashboard.common.store_expired_message',
                ),
            );

            return;
        }

        $this->validate(
            rules: [
                'message' => [
                    'nullable',
                    Rule::requiredIf(
                        callback: $this->currentMessageType === MessageType::TEXT,
                    ),
                    'string',
                    'max:5000',
                ],
                'image' => [
                    'nullable',
                    Rule::requiredIf(
                        callback: $this->currentMessageType === MessageType::IMAGE,
                    ),
                    'image',
                    'max:4096',
                ],
                'video' => [
                    'nullable',
                    Rule::requiredIf(
                        callback: $this->currentMessageType === MessageType::VIDEO,
                    ),
                    'file',
                    'mimes:mp4',
                    'max:4096',
                ],
                'audio' => [
                    'nullable',
                    Rule::requiredIf(
                        callback: $this->currentMessageType === MessageType::AUDIO,
                    ),
                    'file',
                    'mimes:mp3',
                    'max:4096',
                ],
            ],
            attributes: [
                'message' => __(
                    key: 'dashboard.pages.campaigns.columns.message.label'
                ),
                'image' => __(
                    key: 'dashboard.pages.campaigns.columns.image.label'
                ),
                'video' => __(
                    key: 'dashboard.pages.campaigns.columns.video.label'
                ),
                'audio' => __(
                    key: 'dashboard.pages.campaigns.columns.audio.label'
                ),
            ],
        );

        $service = new CampaignsService(
            store: currentStore(),
        );

        $service->send(
            campaignType: $this->currentCampaignType,
            messageType: $this->currentMessageType,
            message: $this->convertEmptyStringToNull(
                string: $this->message,
            ),
            imagePath: $this->image?->store(
                path: 'campaigns/images',
            ),
            videoPath: $this->video?->store(
                path: 'campaigns/videos',
            ),
            audioPath: $this->audio?->store(
                path: 'campaigns/audios',
            ),
        );

        $this->dispatch(
            event: 'campaign-started',
        );

        $this->customSuccessToast(
            message: __(
                key: 'dashboard.pages.campaigns.send.messages.sending',
            ),
        );
    }

    public function appendPlaceholder(string $placeholder): void
    {
        $this->message .= $placeholder;
    }

    public function updated(): void
    {
        $this->validate(
            rules: [
                'currentCampaignType' => [
                    'required',
                    Rule::enum(
                        type: CampaignType::class,
                    ),
                ],
                'currentMessageType' => [
                    'required',
                    Rule::enum(
                        type: MessageType::class,
                    ),
                ],
                'image' => ['nullable', 'image', 'max:4096'],
                'video' => ['nullable', 'file', 'mimes:mp4', 'max:4096'],
                'audio' => ['nullable', 'file', 'mimes:mp3', 'max:4096'],
            ],
            attributes: [
                'currentCampaignType' => __(
                    key: 'dashboard.pages.campaigns.columns.campaign_type.label',
                ),
                'currentMessageType' => __(
                    key: 'dashboard.pages.campaigns.columns.message_type.label',
                ),
                'image' => __(
                    key: 'dashboard.pages.campaigns.columns.image.label'
                ),
                'video' => __(
                    key: 'dashboard.pages.campaigns.columns.video.label'
                ),
                'audio' => __(
                    key: 'dashboard.pages.campaigns.columns.audio.label'
                ),
            ],
        );
    }

    public function mount(): void
    {
        $this->campaignTypes = CampaignType::cases();
        $this->currentCampaignType = Arr::first(
            array: $this->campaignTypes,
        );

        $this->messageTypes = MessageType::cases();
        $this->currentMessageType = Arr::first(
            array: $this->messageTypes,
        );
    }

    public function render(): View
    {
        return view(
            view: 'livewire.dashboard.campaigns.sending-form',
            data: [
                'shouldShowMessage' => $this->currentMessageType !== MessageType::AUDIO,
                'shouldShowImage' => $this->currentMessageType === MessageType::IMAGE,
                'shouldShowVideo' => $this->currentMessageType === MessageType::VIDEO,
                'shouldShowAudio' => $this->currentMessageType === MessageType::AUDIO,
            ],
        );
    }
}
