<?php

declare(strict_types=1);

namespace Capell\WidgetYouTube\Health;

use Capell\LayoutBuilder\Health\AbstractWidgetExtensionHealthCheck;
use Capell\WidgetYouTube\Filament\YouTubeWidget;

final class WidgetYouTubeHealthCheck extends AbstractWidgetExtensionHealthCheck
{
    public static function compatibleCapellApiVersion(): string
    {
        return '^0.0';
    }

    protected static function definitionKey(): string
    {
        return 'capell-app.youtube';
    }

    protected static function filamentWidget(): string
    {
        return YouTubeWidget::class;
    }

    protected static function fallbackView(): string
    {
        return 'capell-widget-youtube::widget';
    }

    protected static function resourceGroups(): array
    {
        return ['capell-app.widget-youtube'];
    }
}
