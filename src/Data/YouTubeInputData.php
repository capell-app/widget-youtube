<?php

declare(strict_types=1);

namespace Capell\WidgetYouTube\Data;

use Spatie\LaravelData\Data;

final class YouTubeInputData extends Data
{
    public function __construct(public string $videoId, public string $poster, public string $title, public ?string $caption = null) {}

    /** @return array<string, list<string>> */
    public static function rules(): array
    {
        return [
            'videoId' => ['required', 'string', 'regex:/^[A-Za-z0-9_-]{11}$/'],
            'poster' => ['required', 'string', 'max:2048', 'regex:/^(?![a-z][a-z0-9+.-]*:)(?!\/\/)[A-Za-z0-9_\/.-]+$/i'],
            'title' => ['required', 'string', 'max:180'],
            'caption' => ['nullable', 'string', 'max:500'],
        ];
    }
}
