<div class="dt--top-section">
    <div class="row">

        <div class="col-12 col-sm-6 d-flex justify-content-sm-start justify-content-center">

            @include(powerGridThemeRoot().'.export')

            @includeIf(powerGridThemeRoot().'.toggle-columns')

            @include(powerGridThemeRoot().'.loading')

        </div>
        <div class="col-12 col-sm-6 d-flex justify-content-sm-end justify-content-center mt-sm-0 mt-3">

            @include(powerGridThemeRoot().'.filter')

        </div>

    </div>
</div>

@include(powerGridThemeRoot().'.batch-exporting')

@include(powerGridThemeRoot().'.enabled-filters')




