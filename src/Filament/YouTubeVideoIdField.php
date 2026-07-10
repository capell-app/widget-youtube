<?php

declare(strict_types=1);

namespace Capell\WidgetYouTube\Filament;

use Capell\Core\Support\Media\YouTubeVideoUrl;
use Filament\Forms\Components\TextInput;

final class YouTubeVideoIdField extends TextInput
{
    protected function setUp(): void
    {
        parent::setUp();

        $this->url()
            ->required()
            ->maxLength(2048)
            ->dehydrateStateUsing(static fn (mixed $state): ?string => self::normalise(is_string($state) ? $state : ''));
    }

    public static function normalise(string $url): ?string
    {
        return YouTubeVideoUrl::parse($url)?->videoId;
    }
}
