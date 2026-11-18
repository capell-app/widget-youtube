# YouTube Widget

<!-- prettier-ignore-start -->

A picture you chose, with a Play button on it, stands in for the video, and the YouTube video behind it loads only when a visitor presses play. Until then the page doesn't contact YouTube at all.

Setting one up means pasting a normal YouTube link and uploading a still image of your own, because it won't borrow YouTube's thumbnail. Give the video a title as well, which screen readers read out on the Play button.

Your site may run Privacy Centre, which asks visitors for their cookie choices. If so, anyone who turned down marketing cookies, or whose browser asks sites not to track them, sees a short message and nothing loads. A browser that blocks scripts gets a plain link to watch on YouTube.

Once someone presses play, the video starts right there on the page. It plays in YouTube's privacy-enhanced mode, which reduces YouTube's tracking but doesn't remove it, and YouTube may still store things in the browser. When the video ends, the suggestions come from the same channel as the one just played.

- **Price and availability:** Check current availability and price on the [Capell Marketplace listing](https://capell.app/extensions/widget-youtube).
- **Licence:** Check the licence terms on the [Capell Marketplace listing](https://capell.app/extensions/widget-youtube).
- **Requires:** `PHP ^8.4`, `capell-app/admin ^1.0`, `capell-app/core ^1.0.66`, `capell-app/frontend ^1.0.2`, `capell-app/layout-builder ^1.0.44`, `spatie/laravel-package-tools ^1.0`.
- **Install:** `composer require capell-app/widget-youtube`.

## Quick Start

1. Install the package: `composer require capell-app/widget-youtube`.
2. Open a page, click **Layout builder**, **Add widgets**, then choose **YouTube video**.

## For Developers

The extension registers its interaction assets while tests verify that initial HTML contains no YouTube request target and that denied privacy state remains blocked.

## Configure

This extension works without configuration. There is nothing to set after install.

## Limits And Pitfalls

- Install the required Capell packages at the versions in `composer.json`: `capell-app/admin ^1.0`, `capell-app/core ^1.0.66`, `capell-app/frontend ^1.0.2`, `capell-app/layout-builder ^1.0.44`.
- Keep public Blade and cached HTML free of authoring markers, model IDs, permissions, signed editor URLs, and lazy database queries.
- Custom write integrations must preserve invalidation for `widget-youtube` cache tags.

## Screens And Workflow

![Youtube public output](docs/screenshots/youtube-public-output.png)

![YouTube fields in Layout Builder](docs/screenshots/youtube-layout-builder-editor.png)

- YouTube Widget extension card (marketplace).
- YouTube fields in Layout Builder (admin).
- Youtube public output (frontend).
- YouTube fields in Layout Builder with admin sidebar menu open (admin).

## Install Impact

- Required packages: `capell-app/admin`, `capell-app/core`, `capell-app/frontend`, `capell-app/layout-builder`.
- Admin navigation: no admin page or resource contribution is declared.
- Admin/editor extensions: `content-widget: WidgetYouTubeContribution`.
- Permissions: no package permission declarations or Shield gates detected; host access rules still apply.
- Public routes: none declared.
- Database changes: no package migrations declared.
- Config: no package config files.
- Settings: no package settings declared.
- Queues or schedules: none declared.
- Cache tags: `widget-youtube`.
- Commands: none declared.

## Troubleshooting

| Symptom | Likely cause | Check | Fix |
| --- | --- | --- | --- |
| Package surface is missing after install | Provider or manifest is not loaded | Confirm `capell.json`, package `composer.json`, and provider registration | Reinstall the package, refresh Composer autoload, and clear host caches |
| Public output leaks unexpected state | Render data, cache variation, or authoring boundary has regressed | Check public Blade, cache tags, and public-output safety tests | Move data loading out of Blade and rerun the package public-output tests |

## Upgrading

No manual steps between minor versions.

## Next Steps

- [Package docs](docs/README.md)
- [Overview](docs/overview.md)
- [Troubleshooting](#troubleshooting)
- [Developer reference](docs/reference.md)
- [Marketplace assets](docs/assets/marketplace/)
- [Capell content language plan](../../docs/CONTENT_LANGUAGE_PLAN.md)
- [Capell documentation design system](../../docs/DESIGN_SYSTEM.md)
- [Capell and package ERD notes](../../docs/erd/capell-and-package-erds.md)
- Related packages: [Layout Builder](../layout-builder/README.md), [Privacy Centre](../privacy-center/README.md).

<!-- prettier-ignore-end -->
