<div x-data="{ url: @entangle($attributes->wire('url')) }">
    <iframe x-if="url" width="560" height="315" src="https://www.youtube.com/embed/{{ $url }}" frameborder="0" allowfullscreen></iframe>
</div>