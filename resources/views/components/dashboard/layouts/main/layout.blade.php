<!DOCTYPE html>
<html lang="ar" dir="rtl">
<head>
    <meta charset="utf-8"/>
    <title>{{ $title }} | @lang('dashboard.common.title')</title>
    <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no">
    <meta content="4Whats" name="description"/>
    <meta content="Valinteca" name="author"/>
    <meta http-equiv="X-UA-Compatible" content="IE=edge"/>

    <link rel="shortcut icon" href="{{ asset(path: 'assets/dashboard/images/favicon.ico') }}">

    <x-dashboard.layouts.main.styles :plugins-styles="$pluginsStyles ?? null" :styles="$styles ?? null"/>
</head>
<body id="body">

<x-dashboard.layouts.main.sidebar/>

<x-dashboard.layouts.main.navbar/>

<div class="page-wrapper">
    <div class="page-content-tab">
        <div class="container-fluid">
            <div class="row">
                <div class="col-sm-12">
                    <div class="page-title-box">
                        @if(isset($breadcrumb))
                            <div class="float-end">
                                {{ $breadcrumb }}
                            </div>
                        @endif
                        @if(isset($pageTitle))
                            <h4 class="page-title">
                                {{ $pageTitle }}
                            </h4>
                        @endif
                    </div>
                </div>
            </div>
            {{ $slot }}
        </div>
        <footer class="footer text-center text-sm-start">
            @lang('dashboard.footer.copyrights', ['date' => date(format: 'Y')])
            <span class="text-muted d-none d-sm-inline-block float-end">
                @lang('dashboard.footer.made_with_love_by_valinteca', ['icon' => '<i class="mdi mdi-heart text-danger"></i>'])
            </span>
        </footer>
    </div>
</div>

<x-dashboard.layouts.main.scripts :plugins-scripts="$pluginsScripts ?? null" :scripts="$scripts ?? null"/>
</body>
</html>
