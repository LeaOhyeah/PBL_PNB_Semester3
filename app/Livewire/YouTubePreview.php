<?php

namespace App\Livewire;

use Livewire\Component;

class YouTubePreview extends Component
{
    public $contentUrl;
    
    public function render()
    {
        return view('livewire.you-tube-preview');
    }

    public function updatedContentUrl($value)
    {
        $this->contentUrl = $value;
    }

    public function extractVideoId($url)
    {
        parse_str(parse_url($url, PHP_URL_QUERY), $youtubeParams);
        return $youtubeParams['v'] ?? null;
    }
}
