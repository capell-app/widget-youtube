<?php

declare(strict_types=1);

use Capell\Core\Facades\CapellCore;
use Capell\Frontend\Actions\AssertPublicHtmlContainsNoAuthoringSurfaceAction;
use Capell\Frontend\Support\Security\PublicHtmlSafetyInspector;
use Capell\LayoutBuilder\Data\WidgetExtensions\WidgetExtensionPayloadBatchData;
use Capell\LayoutBuilder\Data\WidgetExtensions\WidgetExtensionPayloadInputData;
use Capell\LayoutBuilder\Data\WidgetExtensions\WidgetExtensionRenderContextData;
use Capell\LayoutBuilder\Support\WidgetExtensions\WidgetExtensionRegistry;
use Capell\WidgetYouTube\Actions\ResolveYouTubePayloadsAction;
use Capell\WidgetYouTube\Data\YouTubeInputData;
use Capell\WidgetYouTube\Data\YouTubeRenderData;
use Capell\WidgetYouTube\Filament\YouTubeVideoIdField;
use Capell\WidgetYouTube\Health\WidgetYouTubeHealthCheck;
use Capell\WidgetYouTube\Providers\WidgetYouTubeServiceProvider;
use Illuminate\Support\Facades\Storage;
use Illuminate\Validation\ValidationException;
use Symfony\Component\HttpFoundation\Response;

beforeEach(function (): void {
    CapellCore::forcePackageInstalled('capell-app/widget-youtube');
    $this->app->register(WidgetYouTubeServiceProvider::class);
});

it('normalizes supported urls through the core parser', function (): void {
    expect(YouTubeVideoIdField::normalise('https://youtu.be/dQw4w9WgXcQ'))->toBe('dQw4w9WgXcQ')
        ->and(YouTubeVideoIdField::normalise('dQw4w9WgXcQ'))->toBe('dQw4w9WgXcQ')
        ->and(YouTubeVideoIdField::normalise('javascript:alert(1)'))->toBeNull()
        ->and(YouTubeVideoIdField::normalise('broken'))->toBeNull();
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

it('renders the transcript as escaped paragraphs and the caption settings as data attributes', function (): void {
    $renderData = YouTubeRenderData::from([
        'videoId' => 'dQw4w9WgXcQ', 'poster' => 'posters/video.jpg', 'title' => 'Product tour',
        'transcript' => "First <b>line</b>\n\nSecond paragraph", 'captionsOn' => true, 'captionLanguage' => 'fr-CA',
        'posterWidth' => 1280, 'posterHeight' => 720,
    ]);
    $html = view('capell-widget-youtube::widget', ['renderData' => $renderData])->render();

    expect($html)->toContain('<details class="capell-youtube__transcript">')
        ->toContain('<p>First &lt;b&gt;line&lt;/b&gt;</p>')
        ->toContain('<p>Second paragraph</p>')
        ->toContain('data-captions="true"')
        ->toContain('data-caption-language="fr-CA"')
        ->toContain('width="1280"')
        ->toContain('height="720"');

    $plain = view('capell-widget-youtube::widget', ['renderData' => YouTubeRenderData::from(['videoId' => 'dQw4w9WgXcQ', 'poster' => 'p.jpg', 'title' => 'T'])])->render();

    expect($plain)->not->toContain('capell-youtube__transcript')->not->toContain('data-captions')->not->toContain('width=');
});

it('validates the transcript and caption language and resolves the poster size into render data', function (): void {
    expect(fn (): YouTubeInputData => YouTubeInputData::validateAndCreate(['videoId' => 'dQw4w9WgXcQ', 'poster' => 'p.jpg', 'title' => 'T', 'captionLanguage' => 'en&evil=1']))->toThrow(ValidationException::class);

    Storage::fake('public');
    ob_start();
    imagepng(imagecreatetruecolor(640, 360));
    Storage::disk('public')->put('posters/video.png', (string) ob_get_clean());
    $input = YouTubeInputData::validateAndCreate(['videoId' => 'dQw4w9WgXcQ', 'poster' => 'posters/video.png', 'title' => 'T', 'transcript' => 'Words', 'captionsOn' => true]);
    $payload = resolve(ResolveYouTubePayloadsAction::class)->resolve(new WidgetExtensionPayloadBatchData([new WidgetExtensionPayloadInputData('instance', $input)], new WidgetExtensionRenderContextData(null)))['instance'];

    expect($payload->posterWidth)->toBe(640)
        ->and($payload->posterHeight)->toBe(360)
        ->and($payload->transcript)->toBe('Words')
        ->and($payload->captionsOn)->toBeTrue();
});

it('serves a srcset for a wide poster and keeps a missing poster plain, with no authoring state', function (): void {
    Storage::fake('public');
    ob_start();
    imagejpeg(imagecreatetruecolor(1800, 1012));
    Storage::disk('public')->put('posters/wide.jpg', (string) ob_get_clean());
    $resolve = fn (string $poster): YouTubeRenderData => resolve(ResolveYouTubePayloadsAction::class)->resolve(new WidgetExtensionPayloadBatchData([new WidgetExtensionPayloadInputData('instance', YouTubeInputData::validateAndCreate(['videoId' => 'dQw4w9WgXcQ', 'poster' => $poster, 'title' => 'T']))], new WidgetExtensionRenderContextData(null)))['instance'];

    $wide = view('capell-widget-youtube::widget', ['renderData' => $resolve('posters/wide.jpg')])->render();
    $missing = view('capell-widget-youtube::widget', ['renderData' => $resolve('posters/missing.jpg')])->render();

    expect($wide)->toContain('srcset="')->toContain(' 480w')->toContain('posters/wide.jpg 1800w')->toContain('sizes="100vw"')
        ->and($wide)->not->toContain('instance')->not->toContain(Storage::disk('public')->path(''))
        ->and($missing)->not->toContain('srcset=');
});
