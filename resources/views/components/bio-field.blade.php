@props([
    'label'    => '',
    'id'       => '',
    'type'     => 'text',
    'readonly' => false,
])

<div>
    <label for="{{ $id }}"
        style="font-size:12px;font-weight:500;color:#374151;display:block;margin-bottom:3px;">
        {{ $label }}
    </label>
    <input
        type="{{ $type }}"
        id="{{ $id }}"
        class="bio-input"
        @if($readonly) readonly @else disabled @endif
        {{ $attributes }}
    >
</div>