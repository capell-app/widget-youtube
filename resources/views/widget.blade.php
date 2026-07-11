@php /** @var \Capell\WidgetYouTube\Data\YouTubeRenderData $renderData */ @endphp
<figure class="capell-youtube" data-capell-youtube data-video-id="{{ $renderData->videoId }}">
    <div class="capell-youtube__frame">
        <img src="{{ asset('storage/' . ltrim($renderData->poster, '/')) }}" alt="" loading="lazy">
        <button type="button" data-youtube-activate aria-label="Play {{ $renderData->title }}">
            <span aria-hidden="true">▶</span><span>Play video</span>
        </button>
        <p class="capell-youtube__notice" data-youtube-notice role="status" aria-live="polite"></p>
    </div>
    @if ($renderData->caption)<figcaption>{{ $renderData->caption }}</figcaption>@endif
    <noscript><a href="https://www.youtube.com/watch?v={{ $renderData->videoId }}">Watch {{ $renderData->title }} on YouTube</a></noscript>
    <template data-youtube-frame><iframe title="{{ $renderData->title }}" allow="accelerometer; encrypted-media; gyroscope; picture-in-picture" referrerpolicy="strict-origin-when-cross-origin" allowfullscreen loading="lazy"></iframe></template>
</figure>
