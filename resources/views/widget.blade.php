@php /** @var \Capell\WidgetYouTube\Data\YouTubeRenderData $renderData */ @endphp
<figure
    class="capell-youtube"
    data-capell-widget-youtube
    data-video-id="{{ $renderData->videoId }}"
    @if ($renderData->captionsOn) data-captions="true" @endif
    @if ($renderData->captionLanguage) data-caption-language="{{ $renderData->captionLanguage }}" @endif
    data-privacy-denied="{{ __('capell-widget-youtube::widget.privacy_denied') }}"
>
    <div class="capell-youtube__frame">
        <img
            src="{{ asset('storage/' . ltrim($renderData->poster, '/')) }}"
            alt=""
            @if ($renderData->posterSrcset) srcset="{{ $renderData->posterSrcset }}" sizes="100vw" @endif
            @if ($renderData->posterWidth && $renderData->posterHeight) width="{{ $renderData->posterWidth }}" height="{{ $renderData->posterHeight }}" @endif
            decoding="async"
            loading="lazy"
        />
        <button
            type="button"
            data-youtube-activate
            aria-label="{{ __('capell-widget-youtube::widget.play_named', ['title' => $renderData->title]) }}"
        >
            <span aria-hidden="true">▶</span
            ><span>{{ __('capell-widget-youtube::widget.play') }}</span>
        </button>
        <p class="capell-youtube__notice" data-youtube-notice role="status" aria-live="polite"></p>
    </div>
    @if ($renderData->caption)
        <figcaption>{{ $renderData->caption }}</figcaption>
    @endif
    @if ($renderData->transcript)
        <details class="capell-youtube__transcript">
            <summary>
                {{ __('capell-widget-youtube::widget.transcript_heading') }}
            </summary>
            @foreach (preg_split('/\R{2,}/', trim($renderData->transcript)) ?: [] as $paragraph)
                <p>{{ $paragraph }}</p>
            @endforeach
        </details>
    @endif
    <noscript
        ><a
            href="https://www.youtube.com/watch?v={{ $renderData->videoId }}"
            >{{ __('capell-widget-youtube::widget.watch_on_youtube', ['title' => $renderData->title]) }}</a
        ></noscript
    >
    <template data-youtube-frame
        ><iframe
            title="{{ $renderData->title }}"
            sandbox="allow-scripts allow-same-origin allow-presentation"
            allow="accelerometer; encrypted-media; gyroscope; picture-in-picture"
            referrerpolicy="strict-origin-when-cross-origin"
            allowfullscreen
            loading="lazy"
        ></iframe
    ></template>
</figure>
