@props([
    'name' => '',
    'class' => ''
])

@if ($name === 'cloud-upload')
<svg {{ $attributes->merge(['class' => $class]) }} fill="none" viewBox="0 0 24 24" stroke="currentColor">
    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 16v-4a4 4 0 00-8 0v4M12 12v8m0 0l-4-4m4 4l4-4" />
</svg>
@else
<span {{ $attributes->merge(['class' => $class]) }}>[icon: {{ $name }}]</span>
@endif
