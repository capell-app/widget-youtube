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

        $this->url()
            ->required()
            ->maxLength(2048)
            ->formatStateUsing(static function (mixed $state): mixed {
                if (is_string($state) && preg_match('/^[A-Za-z0-9_-]{11}$/', $state) === 1) {
                    return 'https://www.youtube.com/watch?v=' . $state;
                }

                return $state;
            })
            ->rules([static fn (): Closure => static function (string $attribute, mixed $value, Closure $fail): void {
                if (! is_string($value) || self::normalise($value) === null) {
                    $fail(__('capell-widget-youtube::widget.invalid_url'));
                }
            }])
            ->dehydrateStateUsing(static fn (mixed $state): ?string => self::normalise(is_string($state) ? $state : ''));
    }

    public static function normalise(string $url): ?string
    {
        return YouTubeVideoUrl::parse($url)?->videoId;
    }
}
