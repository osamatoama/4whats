<div class="row">
    <div class="col-12 col-md-4 col-lg-2">
        <div class="card overflow-hidden">
            <div class="card-body">
                <div class="row align-items-center">
                    <div class="col">
                        <span class="h4 fw-bold">{{ $contactsCount }}</span>
                        <h6 class="text-uppercase text-muted mt-2 m-0 font-11">
                            @lang('dashboard.pages.home.contacts')
                        </h6>
                    </div>
                    <div class="col-auto">
                        <i class="ti ti-users display-3 text-secondary position-absolute o-1 translate-middle"></i>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <div class="col-12 col-md-4 col-lg-2">
        <div class="card overflow-hidden">
            <div class="card-body">
                <div class="row align-items-center">
                    <div class="col">
                        <span class="h4 fw-bold">{{ $messagesCount }}</span>
                        <h6 class="text-uppercase text-muted mt-2 m-0 font-11">
                            @lang('dashboard.pages.home.messages')
                        </h6>
                    </div>
                    <div class="col-auto">
                        <i class="ti ti-messages display-3 text-secondary position-absolute o-1 translate-middle"></i>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

@if(!auth()->user()->is_admin)
    @can('connect', currentStore()->whatsappAccount)
        <livewire:dashboard.whatsapp-connection.qr-code/>
    @endcan
@endif
