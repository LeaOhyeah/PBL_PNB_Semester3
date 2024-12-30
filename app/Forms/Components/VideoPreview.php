<?php
namespace App\Forms\Components;

use Filament\Forms\Components\Component;

class VideoPreview extends Component
{
    protected string $view = 'forms.youtube-preview';

    public static function make(): static
    {
        return app(static::class);
    }
}
