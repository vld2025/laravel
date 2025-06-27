<div class="fi-page-wrapper">
    <div class="fi-page-content" style="background-color: #d0d0d0; min-height: calc(100vh - 4rem); margin: -2rem; padding: 2rem; position: relative;">
        <!-- Logo come sfondo -->
        <div style="position: absolute; top: 50%; left: 50%; transform: translate(-50%, -50%); opacity: 0.5; z-index: 0;">
            <img src="{{ asset('images/logo/2.svg') }}" alt="VLD Service GmbH" class="h-96">
        </div>

        <!-- Contenuto e widgets -->
        <div style="position: relative; z-index: 10;">
            <x-filament-widgets::widgets
                :widgets="$this->getWidgets()"
                :columns="$this->getColumns()"
            />
        </div>
    </div>
</div>
