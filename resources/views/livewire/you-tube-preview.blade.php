<div>
    {{-- The Master doesn't talk, he acts. --}}
    <div>
        <input type="text" wire:model="contentUrl" placeholder="Enter YouTube URL">
        @if($contentUrl)
            <div class="mt-3">
                <iframe width="560" height="315" src="https://www.youtube.com/embed/{{ $this->extractVideoId($contentUrl) }}" frameborder="0" allowfullscreen></iframe>
            </div>
        @endif
    </div>
    
</div>

