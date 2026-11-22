<?php

declare(strict_types=1);

use Capell\Core\Facades\CapellCore;
use Capell\WidgetYouTube\Filament\YouTubeWidget;
use Capell\WidgetYouTube\Providers\WidgetYouTubeServiceProvider;
use Filament\Forms\Components\TextInput;

/*
 * Bug caught: the editor labelled the title field "Iframe title", developer
 * jargon, and never said the video code on its own is accepted.
 */
beforeEach(function (): void {
    CapellCore::forcePackageInstalled('capell-app/widget-youtube');
    $this->app->register(WidgetYouTubeServiceProvider::class);
});

it('labels the video fields in plain words', function (): void {
    $labels = [];

    $children = YouTubeWidget::make()->getDefaultChildComponents();
    assert(is_array($children));

    foreach ($children as $component) {
        if ($component instanceof TextInput) {
            $labels[$component->getName()] = $component->getLabel();
        }
    }

    expect($labels['title'] ?? null)->toBe('Video title')
        ->and($labels['videoId'] ?? null)->toBe('YouTube URL')
        ->and(__('capell-widget-youtube::widget.title_helper'))->toContain('Screen readers')
        ->and(__('capell-widget-youtube::widget.url_helper'))->toContain('video code on its own');
});
