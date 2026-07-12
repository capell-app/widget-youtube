<?php

declare(strict_types=1);

namespace Capell\WidgetYouTube;

use Capell\Core\Contracts\Extensions\RegistersExtensionContentWidget;

final class WidgetYouTubeContribution implements RegistersExtensionContentWidget
{
    public static function compatibleCapellApiVersion(): string
    {
        return '^0.0';
    }
}
