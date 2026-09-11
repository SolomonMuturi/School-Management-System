<div id="page-header" class="page-header page-header-light">
    <div class="page-header-content header-elements-md-inline">
        <div class="page-title d-flex">
            <button type="button" onclick="history.back()" class="btn btn-light btn-sm mr-2 align-self-center" title="Go back"><i class="icon-backward mr-1"></i> Back</button>
            <h4><i class="icon-plus-circle2 mr-2 text-primary"></i> <span class="font-weight-semibold">@yield('page_title')</span></h4>
            <a href="#" class="header-elements-toggle text-default d-md-none"><i class="icon-more"></i></a>
        </div>

        <div class="header-elements d-none">
            <div class="d-flex justify-content-center">
                <span class="btn btn-link btn-float text-default" style="cursor: default; pointer-events: none;"><i class="icon-arrow-down7 text-primary"></i> <span class="font-weight-semibold" style="color: var(--gray-600);">Current Session: {{ Qs::getSetting('current_session') }}</span></span>
            </div>
        </div>
    </div>
</div>
