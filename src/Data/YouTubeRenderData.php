<?php

declare(strict_types=1);

namespace Capell\WidgetYouTube\Data;

use Spatie\LaravelData\Data;

final class YouTubeRenderData extends Data
{
    public function __construct(
        public string $videoId,
        public string $poster,
        public string $title,
        public ?string $caption = null,
        public ?string $transcript = null,
        public bool $captionsOn = false,
        public ?string $captionLanguage = null,
        public ?int $posterWidth = null,
        public ?int $posterHeight = null,
        public ?string $posterSrcset = null,
    ) {}
}
