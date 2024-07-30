<tr>
    <td>
        {{ $store->provider_id }}
    </td>
    <td>
        {{ $store->provider_type->label() }}
    </td>
    <td>
        {{ $store->name }}
    </td>
    <td>
        {{ $store->email }}
    </td>
    <td>
        <a href="https://wa.me/{{ $store->mobile }}" target="_blank">
            {{ $store->mobile }}
        </a>
    </td>
    <td>
        {{ $store->whatsappAccount->expired_at->format(format: 'Y-m-d H:i:s') }}
    </td>
    <td>
        <div class="d-flex gap-1 flex-column flex-lg-row">
            @canany(['update', 'updatePassword'], $store)
                <div>
                    <button type="button" class="btn btn-warning" data-bs-toggle="modal" data-bs-target="#editModal{{ $store->id }}">
                        @lang('dashboard.common.edit')
                    </button>

                    <div class="modal fade" id="editModal{{ $store->id }}" tabindex="-1" role="dialog" aria-labelledby="editModalLabel" aria-hidden="true" wire:ignore.self>
                        <div class="modal-dialog" role="document">
                            <div class="modal-content">
                                <div class="modal-header">
                                    <h6 class="modal-title m-0" id="editModalLabel">
                                        @lang('dashboard.common.edit') #{{ $store->id }}
                                    </h6>
                                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                                </div>
                                <div class="modal-body">
                                    @can('update', $store)
                                        <div class="card">
                                            <div class="card-header">
                                                <h3 class="card-title">
                                                    @lang('dashboard.pages.stores.index.general_information')
                                                </h3>
                                            </div>
                                            <div class="card-body">
                                                <div class="form-group mb-3">
                                                    <label class="form-label" for="fourWhatsProviderId">
                                                        @lang('dashboard.pages.stores.columns.four_whats_provider_id')
                                                    </label>
                                                    <input type="text" id="fourWhatsProviderId" @class(['form-control', 'is-invalid'=> $errors->has(key: 'fourWhatsProviderId')]) wire:model="fourWhatsProviderId">
                                                    @error('fourWhatsProviderId')
                                                    <div class="invalid-feedback">
                                                        {{ $message }}
                                                    </div>
                                                    @enderror
                                                </div>

                                                <div class="form-group mb-3">
                                                    <label class="form-label" for="fourWhatsApiKey">
                                                        @lang('dashboard.pages.stores.columns.four_whats_api_key')
                                                    </label>
                                                    <input type="text" id="fourWhatsApiKey" @class(['form-control', 'is-invalid'=> $errors->has(key: 'fourWhatsApiKey')]) wire:model="fourWhatsApiKey">
                                                    @error('fourWhatsApiKey')
                                                    <div class="invalid-feedback">
                                                        {{ $message }}
                                                    </div>
                                                    @enderror
                                                </div>

                                                <div class="form-group mb-3">
                                                    <label class="form-label" for="instanceId">
                                                        @lang('dashboard.pages.stores.columns.whatsapp_instance_id')
                                                    </label>
                                                    <input type="text" id="instanceId" @class(['form-control', 'is-invalid'=> $errors->has(key: 'instanceId')]) wire:model="instanceId">
                                                    @error('instanceId')
                                                    <div class="invalid-feedback">
                                                        {{ $message }}
                                                    </div>
                                                    @enderror
                                                </div>

                                                <div class="form-group">
                                                    <label class="form-label" for="instanceToken">
                                                        @lang('dashboard.pages.stores.columns.whatsapp_instance_token')
                                                    </label>
                                                    <input type="text" id="instanceToken" @class(['form-control', 'is-invalid'=> $errors->has(key: 'instanceToken')]) wire:model="instanceToken">
                                                    @error('instanceToken')
                                                    <div class="invalid-feedback">
                                                        {{ $message }}
                                                    </div>
                                                    @enderror
                                                </div>

                                            </div>
                                            <div class="card-footer">
                                                <button type="button" class="btn btn-de-primary btn-sm" wire:click="updateStore" wire:loading.attr="disabled">
                                                    @lang('dashboard.common.edit')
                                                </button>
                                            </div>
                                        </div>
                                    @endcan

                                    @can('updatePassword', $store)
                                        <div class="card">
                                            <div class="card-header">
                                                <h3 class="card-title">
                                                    @lang('dashboard.pages.stores.index.password')
                                                </h3>
                                            </div>
                                            <div class="card-body">
                                                <div class="form-group mb-3">
                                                    <label class="form-label" for="name">
                                                        @lang('dashboard.pages.stores.columns.new_password')
                                                    </label>
                                                    <input type="password" @class(['form-control', 'is-invalid' => $errors->has(key: 'newPassword')]) wire:model="newPassword">
                                                    @error('newPassword')
                                                    <div class="invalid-feedback">
                                                        {{ $message }}
                                                    </div>
                                                    @enderror
                                                </div>
                                                <div class="form-group">
                                                    <label class="form-label" for="name">
                                                        @lang('dashboard.pages.stores.columns.new_password_confirmation')
                                                    </label>
                                                    <input type="password" class="form-control" wire:model="newPasswordConfirmation">
                                                </div>
                                            </div>
                                            <div class="card-footer">
                                                <button type="button" class="btn btn-de-primary btn-sm" wire:click="updatePassword" wire:loading.attr="disabled">
                                                    @lang('dashboard.common.edit')
                                                </button>
                                            </div>
                                        </div>
                                    @endcan
                                </div>
                                <div class="modal-footer">
                                    <button type="button" class="btn btn-de-secondary btn-sm" data-bs-dismiss="modal">
                                        @lang('dashboard.common.close')
                                    </button>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            @endcan

            @can('extendTrial', $store)
                <div>
                    <button class="btn btn-danger" wire:confirm wire:click="extendTrial" wire:loading.attr="disabled">
                        @lang('dashboard.pages.stores.index.extend_trial')
                    </button>
                </div>
            @endcan

            @can('toggleActivation', $store)
                <div>
                    <button class="btn btn-danger" wire:confirm wire:click="toggle" wire:loading.attr="disabled">
                        @if($store->is_uninstalled)
                            @lang('dashboard.pages.stores.index.activate')
                        @else
                            @lang('dashboard.pages.stores.index.deactivate')
                        @endif
                    </button>
                </div>
            @endcan
        </div>
    </td>
</tr>
