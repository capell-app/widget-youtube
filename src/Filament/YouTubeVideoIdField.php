<?php

declare(strict_types=1);

namespace Capell\WidgetYouTube\Filament;

use Capell\Core\Support\Media\YouTubeVideoUrl;
use Closure;
use Filament\Forms\Components\TextInput;
use Override;

final class YouTubeVideoIdField extends TextInput
{
    #[Override]
    protected function setUp(): void
    {
        parent::setUp();

        // No `url()` rule: the stored form is the bare video ID, which must stay valid
        // if the builder hands it back without running the hydration formatter.
        $this->type('url')
            ->required()
            ->maxLength(2048)
            ->formatStateUsing(static fn (mixed $state): mixed => is_string($state) && self::isVideoId($state)
                ? 'https://www.youtube.com/watch?v=' . $state
                : $state)
            ->rules([static fn (): Closure => static function (string $attribute, mixed $value, Closure $fail): void {
                if (! is_string($value) || self::normalise($value) === null) {
                    $fail(__('capell-widget-youtube::widget.invalid_url'));
                }
            }])
            ->dehydrateStateUsing(static fn (mixed $state): ?string => self::normalise(is_string($state) ? $state : ''));
    }

    /** Accepts a supported watch, short or embed URL, or an already-stored video ID. */
    public static function normalise(string $value): ?string
    {
        $value = trim($value);

        return self::isVideoId($value) ? $value : YouTubeVideoUrl::parse($value)?->videoId;
    }

    private static function isVideoId(string $value): bool
    {
        return preg_match('/^[A-Za-z0-9_-]{11}$/', $value) === 1;
    }
}
