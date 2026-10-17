<?php

declare(strict_types=1);

use Capell\Core\Facades\CapellCore;
use Capell\Frontend\Actions\AssertPublicHtmlContainsNoAuthoringSurfaceAction;
use Capell\Frontend\Support\Security\PublicHtmlSafetyInspector;
use Capell\LayoutBuilder\Support\WidgetExtensions\WidgetExtensionRegistry;
use Capell\WidgetYouTube\Data\YouTubeInputData;
use Capell\WidgetYouTube\Data\YouTubeRenderData;
use Capell\WidgetYouTube\Filament\YouTubeVideoIdField;
use Capell\WidgetYouTube\Health\WidgetYouTubeHealthCheck;
use Capell\WidgetYouTube\Providers\WidgetYouTubeServiceProvider;
use Illuminate\Validation\ValidationException;
use Symfony\Component\HttpFoundation\Response;

beforeEach(function (): void {
    CapellCore::forcePackageInstalled('capell-app/widget-youtube');
    $this->app->register(WidgetYouTubeServiceProvider::class);
});

it('normalizes supported urls through the core parser', function (): void {
    expect(YouTubeVideoIdField::normalise('https://youtu.be/dQw4w9WgXcQ'))->toBe('dQw4w9WgXcQ')
        ->and(YouTubeVideoIdField::normalise('javascript:alert(1)'))->toBeNull();
});

it('registers a healthy typed widget definition', function (): void {
    $definition = resolve(WidgetExtensionRegistry::class)->definition('capell-app.youtube');
    expect($definition?->inputData)->toBe(YouTubeInputData::class)
        ->and($definition?->renderData)->toBe(YouTubeRenderData::class)
        ->and(WidgetYouTubeHealthCheck::passed())->toBeTrue();
});

it('ships no provider request in initial html', function (): void {
    $renderData = YouTubeRenderData::from(['videoId' => 'dQw4w9WgXcQ', 'poster' => 'posters/video.jpg', 'title' => 'Product tour', 'caption' => '<b>Tour</b>']);
    $html = view('capell-widget-youtube::widget', ['renderData' => $renderData])->render();

    expect($html)->toContain('data-capell-widget-youtube')
        ->not->toContain('data-capell-youtube');

    AssertPublicHtmlContainsNoAuthoringSurfaceAction::run(new Response(
        '<!doctype html><html><body>' . $html . '</body></html>',
        Response::HTTP_OK,
        ['Content-Type' => 'text/html'],
    ));

    expect(resolve(PublicHtmlSafetyInspector::class)->containsAuthoringSurface(
        '<section data-capell-youtube>Widget</section>',
    ))->toBeTrue();
    expect($html)->not->toContain('youtube-nocookie.com')
        ->not->toContain('img.youtube.com')
        ->toContain('&lt;b&gt;Tour&lt;/b&gt;')
        ->toContain('referrerpolicy="strict-origin-when-cross-origin"')
        ->toContain('sandbox="allow-scripts allow-same-origin allow-presentation"')
        ->toContain('data-privacy-denied="Video playback is disabled by your privacy choices."');
});

it('validates the normalized id and local poster', function (): void {
    expect(fn (): YouTubeInputData => YouTubeInputData::validateAndCreate(['videoId' => 'invalid', 'poster' => 'https://remote.test/poster.jpg', 'title' => 'Bad']))->toThrow(ValidationException::class);
});
