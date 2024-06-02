<div
    x-data="{
        show: false,
        @if( $attributes->wire('model')->value() )
            show: @entangle($attributes->wire('model')->value())
        @endif
    }"
        x-on:open-modal.window="show = ($event.detail.id === $id)"
        x-on:close-modal.window="show !($event.detail.id === $id)"
        x-show="show"
        class="fixed inset-0 z-10 p-6 overflow-auto"
        style="display: none;"
    >
        <div
            x-on:@click="show = false"
            class="absolute inset-0 transition-opacity bg-gray-500 text-opacity-75"
        >

        </div>
</div>


