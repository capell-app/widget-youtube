<?php

declare(strict_types=1);

namespace Capell\WidgetYouTube\Filament;

use Capell\Admin\Contracts\Widgets\FilamentWidget;
use Filament\Forms\Components\Builder\Block;
use Filament\Forms\Components\FileUpload;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\TextInput;

final class YouTubeWidget implements FilamentWidget
{
    public static function getWidgetName(): string
    {
        return 'capell-app.youtube';
    }

    public static function make(): Block
    {
        return Block::make(self::getWidgetName())->label(__('capell-widget-youtube::widget.label'))->schema([
            YouTubeVideoIdField::make('videoId')->label(__('capell-widget-youtube::widget.url')),
            FileUpload::make('poster')->label(__('capell-widget-youtube::widget.poster'))->image()->required(),
            TextInput::make('title')->label(__('capell-widget-youtube::widget.title'))->required()->maxLength(180),
            Textarea::make('caption')->label(__('capell-widget-youtube::widget.caption'))->maxLength(500),
        ]);
    }
}
