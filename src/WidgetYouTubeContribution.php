<?php

declare(strict_types=1);

namespace Capell\WidgetYouTube;

use Capell\Core\Contracts\Extensions\RegistersExtensionAsset;
use Capell\Core\Contracts\Extensions\RegistersExtensionContentWidget;
use Override;

final class WidgetYouTubeContribution implements RegistersExtensionAsset, RegistersExtensionContentWidget
{
    #[Override]
    public static function compatibleCapellApiVersion(): string
    {
        return '^1.0';
    }
}
