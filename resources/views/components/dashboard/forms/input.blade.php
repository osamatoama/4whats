@props(['isInvalid' => false])

<input {{ $attributes->class(['form-control', 'is-invalid' => $isInvalid]) }}>
