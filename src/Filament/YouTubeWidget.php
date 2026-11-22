<?php

declare(strict_types=1);

namespace Capell\WidgetYouTube\Filament;

use Capell\Admin\Contracts\Widgets\FilamentWidget;
use Capell\LayoutBuilder\Filament\Components\Forms\WidgetFileUpload;
use Filament\Forms\Components\Builder\Block;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Toggle;
use Override;

final class YouTubeWidget implements FilamentWidget
{
    #[Override]
    public static function getWidgetName(): string
    {
        return 'capell-app.youtube';
    }

    #[Override]
    public static function make(): Block
    {
        return Block::make(self::getWidgetName())->label(__('capell-widget-youtube::widget.label'))->schema([
            YouTubeVideoIdField::make('videoId')->label(__('capell-widget-youtube::widget.url'))->helperText(__('capell-widget-youtube::widget.url_helper')),
            WidgetFileUpload::make('poster')->label(__('capell-widget-youtube::widget.poster'))->image()->required(),
            TextInput::make('title')->label(__('capell-widget-youtube::widget.title'))->helperText(__('capell-widget-youtube::widget.title_helper'))->required()->maxLength(180),
            Textarea::make('caption')->label(__('capell-widget-youtube::widget.caption'))->maxLength(500),
            Textarea::make('transcript')->label(__('capell-widget-youtube::widget.transcript'))->helperText(__('capell-widget-youtube::widget.transcript_helper'))->rows(6)->maxLength(20000),
            Toggle::make('captionsOn')->label(__('capell-widget-youtube::widget.captions_on'))->helperText(__('capell-widget-youtube::widget.captions_on_helper'))->default(false),
            TextInput::make('captionLanguage')->label(__('capell-widget-youtube::widget.caption_language'))->helperText(__('capell-widget-youtube::widget.caption_language_helper'))->maxLength(12)->regex('/^[a-z]{2,3}(?:-[A-Za-z0-9]{2,8})?$/')->placeholder('en'),
        ]);
    }
}
