<?php

declare(strict_types=1);

namespace Capell\WidgetYouTube\Data;

use Spatie\LaravelData\Data;

final class YouTubeRenderData extends Data
{
    public function __construct(public string $videoId, public string $poster, public string $title, public ?string $caption = null) {}
}
