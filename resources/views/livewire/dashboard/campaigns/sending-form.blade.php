@use(\App\Models\User)

<div class="row">
    <div class="col-12">
        <div class="card">
            <div class="card-body">
                <div class="form-group mb-3">
                    <label class="form-label">
                        @lang('dashboard.pages.campaigns.columns.type.label')
                    </label>
                    <select @class(['d-none form-select hidden-select', 'is-invalid' => $errors->has(key: 'currentType')]) wire:model.live="currentType">
                        @foreach($types as $type)
                            <option value="{{ $type->value }}" wire:key="{{ $type->value }}">
                                {{ $type->label() }}
                            </option>
                        @endforeach
                    </select>
                    <div class="dropdown">
                        <button style="color:#000444" class="btn dropdown-toggle custom-select w-100 border d-flex justify-content-between align-items-center" type="button" data-bs-toggle="dropdown" aria-expanded="false">
                            <span>
                                أختر النوع
                            </span>
                            <svg width="20" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 16 16" fill="currentColor">
                                <path fill-rule="evenodd" d="M4.22 6.22a.75.75 0 0 1 1.06 0L8 8.94l2.72-2.72a.75.75 0 1 1 1.06 1.06l-3.25 3.25a.75.75 0 0 1-1.06 0L4.22 7.28a.75.75 0 0 1 0-1.06Z" clip-rule="evenodd" />
                            </svg>
                        </button>
                        <ul class="dropdown-menu w-100">
                            @foreach($types as $type)
                                <li>
                                    <button class="dropdown-item custom-option" value="{{ $type->value }}" wire:key="{{ $type->value }}">
                                        {{ $type->label() }}
                                    </button>
                                </li>
                            @endforeach
                        </ul>
                    </div>
                    @error('currentType')
                    <div class="invalid-feedback">{{ $message }}</div>
                    @enderror
                </div>
                <div class="form-group">
                    <label class="form-label">
                        @lang('dashboard.pages.campaigns.columns.message.label')
                    </label>
                    <textarea @class(['form-control', 'is-invalid' => $errors->has(key: 'message')]) wire:model="message"></textarea>
                    @error('message')
                    <div class="invalid-feedback">{{ $message }}</div>
                    @enderror
                    <small class="form-text text-muted">
                        @lang('dashboard.pages.campaigns.columns.message.description', ['placeholders' => $currentType->placeholdersAsString()])
                    </small>
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
<script>
    const hiddenSelect = document.querySelector(".hidden-select")
    const customOptions = document.querySelectorAll(".custom-option")
    const customSelect = document.querySelector(".custom-select")
    customOptions.forEach(option => {
        option.addEventListener("click", () => {
            const changeEvent = new Event('change')
            hiddenSelect.querySelector(`option[value=${option.value}]`).selected = "selected"
            hiddenSelect.dispatchEvent(changeEvent)
            customSelect.querySelector("span").textContent = option.textContent
        })
    })
</script>