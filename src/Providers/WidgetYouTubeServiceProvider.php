<?php

declare(strict_types=1);

namespace Capell\WidgetYouTube\Providers;

use Capell\Core\Enums\PackageTypeEnum;
use Capell\Core\Enums\PresentationLoadingStrategy;
use Capell\Core\Facades\CapellCore;
use Capell\Core\Support\Packages\AbstractPackageServiceProvider;
use Capell\Frontend\Data\Assets\FrontendResourceData;
use Capell\Frontend\Data\Assets\FrontendResourceGroupData;
use Capell\Frontend\Data\Assets\PublicResourceSourceData;
use Capell\Frontend\Support\Assets\FrontendResourceRegistry;
use Capell\LayoutBuilder\Data\WidgetExtensions\WidgetExtensionCapabilitiesData;
use Capell\LayoutBuilder\Data\WidgetExtensions\WidgetExtensionDefinitionData;
use Capell\LayoutBuilder\Enums\WidgetPresentationCapability;
use Capell\LayoutBuilder\Support\WidgetExtensions\WidgetExtensionRegistrar;
use Capell\WidgetYouTube\Data\YouTubeInputData;
use Capell\WidgetYouTube\Data\YouTubeRenderData;
use Capell\WidgetYouTube\Filament\YouTubeWidget;
use Spatie\LaravelPackageTools\Package;

final class WidgetYouTubeServiceProvider extends AbstractPackageServiceProvider
{
    public static string $name = 'capell-widget-youtube';

    public static string $packageName = 'capell-app/widget-youtube';

    public static PackageTypeEnum $type = PackageTypeEnum::Package;

    public function configurePackage(Package $package): void
    {
        $package->name(self::$name)->hasViews()->hasTranslations()->hasAssets();
    }

    public function packageBooted(): void
    {
        resolve(WidgetExtensionRegistrar::class)->register(new WidgetExtensionDefinitionData(
            key: 'capell-app.youtube',
            packageName: self::$packageName,
            stateVersion: 1,
            filamentWidget: YouTubeWidget::class,
            inputData: YouTubeInputData::class,
            renderData: YouTubeRenderData::class,
            fallbackView: 'capell-widget-youtube::widget',
            components: ['blade' => 'capell::widgets.capell-app.youtube'],
            resourceGroups: ['capell-app.widget-youtube'],
            defaultResourceLoadingStrategy: PresentationLoadingStrategy::Interaction,
            capabilities: new WidgetExtensionCapabilitiesData(requiresInstanceIdentity: true, presentationCapabilities: [WidgetPresentationCapability::Width, WidgetPresentationCapability::Alignment, WidgetPresentationCapability::LoadingStrategy]),
        ));
        resolve(FrontendResourceRegistry::class)->register(new FrontendResourceGroupData(
            key: 'capell-app.widget-youtube',
            label: 'YouTube widget',
            package: self::$packageName,
            resources: [
                FrontendResourceData::style(
                    handle: 'capell-app/widget-youtube:widget-style',
                    package: self::$packageName,
                    source: new PublicResourceSourceData('/vendor/capell-widget-youtube/widget.css'),
                    loadingStrategy: PresentationLoadingStrategy::Visible,
                ),
                FrontendResourceData::classicScript(
                    handle: 'capell-app/widget-youtube:widget-script',
                    package: self::$packageName,
                    source: new PublicResourceSourceData('/vendor/capell-widget-youtube/widget.js'),
                    loadingStrategy: PresentationLoadingStrategy::Interaction,
                ),
            ],
        ));
    }

    protected function isPackageInstalled(): bool
    {
        return CapellCore::isPackageInstalled(self::$packageName);
    }
}
