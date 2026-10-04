@php /** @var \Capell\WidgetYouTube\Data\YouTubeRenderData $renderData */ @endphp
<figure
    class="capell-youtube"
    data-capell-widget-youtube
    data-video-id="{{ $renderData->videoId }}"
    data-privacy-denied="{{ __('capell-widget-youtube::widget.privacy_denied') }}"
>
    <div class="capell-youtube__frame">
        <img
            src="{{ asset('storage/' . ltrim($renderData->poster, '/')) }}"
            alt=""
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
