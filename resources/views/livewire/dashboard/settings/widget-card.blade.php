<div class="row">
    <div class="col-12">
        <div class="card">
            <div class="card-header">
                <h3 class="card-title">
                    @lang('dashboard.pages.settings.index.widget.title')
                </h3>
            </div>
            <div class="card-body">
                <table class="table table-hover mb-0">
                    <tbody>
                    <tr>
                        <th>
                            @lang('dashboard.pages.settings.index.widget.message')
                        </th>
                        <td>
                            <div class="form-group">
                                <textarea @class(['form-control', 'is-invalid' => $errors->has(key: 'message')]) wire:model="message"></textarea>
                                @error('message')
                                <div class="invalid-feedback">
                                    {{ $message }}
                                </div>
                                @enderror
                            </div>
                        </td>
                    </tr>
                    <tr>
                        <th>
                            @lang('dashboard.pages.settings.index.widget.color')
                        </th>
                        <td>
                            <div class="form-group">
                                <input type="color" @class(['form-control', 'is-invalid' => $errors->has(key: 'color')]) wire:model="color">
                                @error('color')
                                <div class="invalid-feedback">
                                    {{ $message }}
                                </div>
                                @enderror
                            </div>
                        </td>
                    </tr>
                    <tr>
                        <th>
                            @lang('dashboard.common.is_enabled')
                        </th>
                        <td>
                            <div class="form-check form-switch">
                                <input class="form-check-input" type="checkbox" wire:model.live="isEnabled">
                            </div>
                        </td>
                    </tr>
                    </tbody>
                </table>
            </div>
            @can('update', $widget)
                <div class="card-footer">
                    <button class="btn btn-warning" wire:click="updateWidget" wire:loading.attr="disabled">
                        @lang('dashboard.common.edit')
                    </button>
                </div>
            @endcan
        </div>
    </div>
</div>
