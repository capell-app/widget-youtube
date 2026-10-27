<?php

declare(strict_types=1);

use Capell\Admin\Filament\Components\Forms\Editor\ContentBuilder;
use Capell\Admin\Support\Widgets\WidgetDiscovery;
use Capell\Core\Facades\CapellCore;
use Capell\LayoutBuilder\Models\Widget;
use Capell\WidgetYouTube\Data\YouTubeRenderData;
use Capell\WidgetYouTube\Filament\YouTubeWidget;
use Capell\WidgetYouTube\Providers\WidgetYouTubeServiceProvider;
use Dom\HTMLDocument;
use Filament\Forms\Components\Field;
use Filament\Forms\Components\FileUpload;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\TextInput;
use Filament\Schemas\Concerns\InteractsWithSchemas;
use Filament\Schemas\Contracts\HasSchemas;
use Filament\Schemas\Schema;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;
use Illuminate\Validation\ValidationException;
use Livewire\Component;

/** @param array<string, mixed>|null $state */
function youtubeBuilderForm(Widget $widget, ?array $state = null): Schema
{
    $host = new class extends Component implements HasSchemas
    {
        use InteractsWithSchemas;

        /** @var array<string, mixed> */
        public array $data = [];
    };

    // Match the persisted attribute so Core can authorise reopened upload paths.
    return Schema::make($host)->model($widget)->statePath('data')->components([
        ContentBuilder::make('meta.content')->blocks([YouTubeWidget::make()]),
    ])->fill(['meta' => $state ?? $widget->meta ?? []]);
}

beforeEach(function (): void {
    CapellCore::forcePackageInstalled('capell-app/widget-youtube');
    $this->app->register(WidgetYouTubeServiceProvider::class);
    resolve(WidgetDiscovery::class)->registerAuthoritative(YouTubeWidget::class);
    config()->set('filament.default_filesystem_disk', 'public');
    Storage::fake('public');
    UploadedFile::fake()->image('video.jpg')->storeAs('posters', 'video.jpg', 'public');
});

it('saves reopens and repeatedly saves a youtube block without losing its video', function (string $url): void {
    $widget = Widget::factory()->create(['meta' => ['content' => [['type' => YouTubeWidget::getWidgetName(), 'data' => [
        'videoId' => $url, 'poster' => 'posters/video.jpg', 'title' => 'Tour', 'caption' => 'First caption',
    ]]]]]);
    $widget->update(youtubeBuilderForm($widget)->getState());

    for ($save = 0; $save < 2; $save++) {
        $form = youtubeBuilderForm($widget->refresh());
        $builder = $form->getComponent('meta.content');
        $this->assertInstanceOf(ContentBuilder::class, $builder);
        $raw = $builder->getRawState();
        $this->assertIsArray($raw);
        $key = array_key_first($raw);
        $this->assertNotNull($key);
        $block = $builder->getChildSchema($key);
        $this->assertInstanceOf(Schema::class, $block);
        $poster = $block->getComponent('poster');
        $this->assertInstanceOf(FileUpload::class, $poster);
        expect($poster->getState())->toBe('posters/video.jpg')
            ->and($poster->getUploadedFiles())->toHaveCount(1);
        $video = $block->getComponent('videoId');
        $this->assertInstanceOf(TextInput::class, $video);
        expect($video->getState())->toBe('https://www.youtube.com/watch?v=dQw4w9WgXcQ');
        $caption = $block->getComponent('caption');
        $this->assertInstanceOf(Textarea::class, $caption);
        $caption->state('Updated caption');
        $widget->update($form->getState());
    }

    $payload = data_get($widget->refresh()->meta, 'content.0.data');
    $this->assertIsArray($payload);
    expect($payload['videoId'])->toBe('dQw4w9WgXcQ');
    $html = view('capell-widget-youtube::widget', ['renderData' => YouTubeRenderData::from($payload)])->render();
    $document = HTMLDocument::createFromString($html, LIBXML_NOERROR);
    expect($document->querySelector('figure')?->getAttribute('data-video-id'))->toBe('dQw4w9WgXcQ')
        ->and($document->querySelector('figure img')?->getAttribute('src'))->toEndWith('/storage/posters/video.jpg')
        ->and($document->querySelector('button[data-youtube-activate]'))->not->toBeNull()
        ->and($document->querySelector('figcaption')?->textContent)->toBe('Updated caption');
})->with([
    'watch' => 'https://www.youtube.com/watch?v=dQw4w9WgXcQ',
    'short' => 'https://youtu.be/dQw4w9WgXcQ',
    'shorts' => 'https://www.youtube.com/shorts/dQw4w9WgXcQ',
    'embed' => 'https://www.youtube.com/embed/dQw4w9WgXcQ',
]);

it('rejects invalid replacements at the video field and preserves saved content', function (string $url): void {
    $saved = ['content' => [['type' => YouTubeWidget::getWidgetName(), 'data' => [
        'videoId' => 'dQw4w9WgXcQ', 'poster' => 'posters/video.jpg', 'title' => 'Tour',
    ]]]];
    $widget = Widget::factory()->create(['meta' => $saved]);
    $saved['content'][0]['data']['videoId'] = $url;
    $form = youtubeBuilderForm($widget, $saved);

    try {
        $widget->update($form->getState());
        $this->fail('Expected video URL validation to prevent the write.');
    } catch (ValidationException $exception) {
        expect(collect(array_keys($exception->errors()))->contains(static fn (string $field): bool => str_ends_with($field, '.videoId')))->toBeTrue();
    }

    expect(data_get($widget->refresh()->meta, 'content.0.data.videoId'))->toBe('dQw4w9WgXcQ');
})->with(['https://example.com/watch?v=dQw4w9WgXcQ', 'https://youtube.com/watch?v=broken', 'javascript:alert(1)']);

it('rejects a poster path not owned by the reopened widget', function (): void {
    $saved = ['content' => [['type' => YouTubeWidget::getWidgetName(), 'data' => [
        'videoId' => 'dQw4w9WgXcQ', 'poster' => 'posters/video.jpg', 'title' => 'Tour',
    ]]]];
    $widget = Widget::factory()->create(['meta' => $saved]);
    $form = youtubeBuilderForm($widget);
    $poster = collect($form->getFlatFields())->first(static fn (Field $field): bool => $field instanceof FileUpload);
    $this->assertInstanceOf(FileUpload::class, $poster);
    expect($poster->getUploadedFiles())->toHaveCount(1);
    UploadedFile::fake()->image('unrelated.jpg')->storeAs('posters', 'unrelated.jpg', 'public');
    $poster->state('posters/unrelated.jpg');

    try {
        $widget->update($form->getState());
        $this->fail('Expected upload validation to reject an unrelated poster.');
    } catch (ValidationException $exception) {
        expect($exception->errors())->toHaveKey((string) $poster->getStatePath());
    }

    expect($widget->refresh()->meta)->toBe($saved);
});
