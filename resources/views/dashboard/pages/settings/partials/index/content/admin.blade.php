@use(\App\Enums\SettingKey)

<div class="row">
    <div class="col-12">
        <div class="card">
            <div class="card-body">
                <div class="table-responsive">
                    <table class="table table-hover mb-0">
                        <thead>
                        <tr>
                            <th>@lang('dashboard.pages.settings.columns.key')</th>
                            <th>@lang('dashboard.pages.settings.columns.value')</th>
                            <th>@lang('dashboard.common.actions')</th>
                        </tr>
                        </thead>
                        <tbody>
                        <livewire:dashboard.settings.text-table-row :setting="settings()->find(key: SettingKey::SYSTEM_FOUR_WHATS_VOUCHER)"/>
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>
</div>
