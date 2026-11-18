# YouTube Widget reference

<!-- prettier-ignore-start -->

Developer reference for `capell-app/widget-youtube`. Start with the [README](../README.md) for what the extension does and how to install it.

## Package Details

YouTube Widget is an **Available** Capell package in the **Capell Essentials** product group. It ships as `capell-app/widget-youtube` and extends these surfaces: admin, frontend.

- Status: Available
- Tier: free
- Bundle: foundation
- Composer package: `capell-app/widget-youtube`
- Namespace: `Capell\WidgetYouTube`
- Theme key: not applicable

## Technical Shape

### Service providers

- `Capell\WidgetYouTube\Providers\WidgetYouTubeServiceProvider`

### Filament classes

- `YouTubeVideoIdField`
- `YouTubeWidget`

### Actions

- `ResolveYouTubePayloadsAction`

### Data objects

- `YouTubeInputData`
- `YouTubeRenderData`

### Manifest contributions

- `asset: Capell\WidgetYouTube\WidgetYouTubeContribution`
- `content-widget: Capell\WidgetYouTube\WidgetYouTubeContribution`

### Health checks

- `Capell\WidgetYouTube\Health\WidgetYouTubeHealthCheck`

### Blade views

- `packages/widget-youtube/resources/views/widget.blade.php`

### Cache tags

- `widget-youtube`


## Data Model

This package has no schema impact. It extends Capell through `asset` contributions and `content-widget` contributions instead of declaring package-owned tables.

<!-- prettier-ignore-end -->
