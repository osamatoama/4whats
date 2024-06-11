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
                        <livewire:dashboard.settings.text-table-row :setting="$voucher"/>
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>
</div>
