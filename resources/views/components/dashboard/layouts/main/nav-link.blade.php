@props(['url', 'text', 'isActive' => false])

<li @class(['nav-item', 'active' => $isActive])>
    <a @class(['nav-link', 'active' => $isActive]) href="{{ $url }}">{{ $text }}</a>
</li>
