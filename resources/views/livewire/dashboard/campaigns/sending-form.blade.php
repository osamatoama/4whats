@use(\App\Models\User)

<div class="row">
    <div class="col-12">
        <div class="card">
            <div class="card-body">
                <div class="row">
                    <div class="form-group col-12 col-md-6 mb-3">
                        <label class="form-label">
                            @lang('dashboard.pages.campaigns.columns.campaign_type.label')
                        </label>
                        <select @class(['form-select', 'is-invalid' => $errors->has(key: 'currentCampaignType')]) wire:model.live="currentCampaignType">
                            @foreach($campaignTypes as $campaignType)
                                <option value="{{ $campaignType->value }}" wire:key="{{ $campaignType->value }}">
                                    {{ $campaignType->label() }}
                                </option>
                            @endforeach
                        </select>
                        @error('currentCampaignType')
                        <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>

                    <div class="form-group col-12 col-md-6 mb-3">
                        <label class="form-label">
                            @lang('dashboard.pages.campaigns.columns.message_type.label')
                        </label>
                        <select @class(['form-select', 'is-invalid' => $errors->has(key: 'currentMessageType')]) wire:model.live="currentMessageType">
                            @foreach($messageTypes as $messageType)
                                <option value="{{ $messageType->value }}" wire:key="{{ $messageType->value }}">
                                    {{ $messageType->label() }}
                                </option>
                            @endforeach
                        </select>
                        @error('currentMessageType')
                        <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>
                </div>

                <div class="row">
                    @if($shouldShowMessage)
                        <div class="form-group col-12 col-md-6">
                            <label class="form-label">
                                @lang('dashboard.pages.campaigns.columns.message.label')
                            </label>
                            <p class="text-muted mb-1">
                                @lang('dashboard.pages.campaigns.columns.message.description')
                                @foreach($currentCampaignType->placeholders() as $placeholder)
                                    <button class="btn btn-sm btn-outline-info" wire:click="appendPlaceholder('{{ $placeholder }}')">
                                        {{ $placeholder }}
                                    </button>
                                @endforeach
                            </p>
                            <textarea @class(['form-control', 'is-invalid' => $errors->has(key: 'message')]) wire:model="message"></textarea>
                            @error('message')
                            <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>
                    @endif

                    @if($shouldShowImage)
                        <div class="form-group col-12 col-md-6 mt-3">
                            <label class="form-label">
                                @lang('dashboard.pages.campaigns.columns.image.label')
                            </label>
                            <input type="file" @class(['form-control', 'is-invalid' => $errors->has(key: 'image')]) wire:model="image"/>
                            @error('image')
                            <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                            @if($image !== null && !$errors->has(key: 'image'))
                                <img src="{{ $image->temporaryUrl() }}" class="img-thumbnail mt-1">
                            @endif
                        </div>
                    @endif

                    @if($shouldShowVideo)
                        <div class="form-group col-12 col-md-6 mt-3">
                            <label class="form-label">
                                @lang('dashboard.pages.campaigns.columns.video.label')
                            </label>
                            <input type="file" @class(['form-control', 'is-invalid' => $errors->has(key: 'video')]) wire:model="video"/>
                            @error('video')
                            <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                            <small class="form-text text-muted">
                                @lang('dashboard.pages.campaigns.columns.video.description')
                            </small>
                            @if($video !== null && !$errors->has(key: 'video'))
                                <video class="d-block mw-100" controls>
                                    <source src="{{ $video->temporaryUrl() }}" type="video/mp4">
                                    Your browser does not support the video tag.
                                </video>
                            @endif
                        </div>
                    @endif

                    @if($shouldShowAudio)
                        <div class="form-group col-12 col-md-6 mt-3">
                            <label class="form-label">
                                @lang('dashboard.pages.campaigns.columns.audio.label')
                            </label>
                            <input type="file" @class(['form-control', 'is-invalid' => $errors->has(key: 'audio')]) wire:model="audio"/>
                            @error('audio')
                            <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                            <small class="form-text text-muted">
                                @lang('dashboard.pages.campaigns.columns.audio.description')
                            </small>
                            @if($audio !== null && !$errors->has(key: 'audio'))
                                <audio class="d-block mw-100" controls>
                                    <source src="{{ $audio->temporaryUrl() }}" type="audio/mpeg">
                                    Your browser does not support the audio element.
                                </audio>
                            @endif
                        </div>
                    @endif
                </div>
            </div>
            <div class="card-footer">
                @can('sendCampaigns', User::class)
                    <button class="btn btn-primary" wire:click="sendCampaign" wire:loading.attr="disabled">
                        @lang('dashboard.common.send')
                    </button>
                @endcan
            </div>
        </div>
    </div>
</div>
