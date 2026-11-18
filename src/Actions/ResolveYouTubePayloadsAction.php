<?php

declare(strict_types=1);

namespace Capell\WidgetYouTube\Actions;

use Capell\Core\Actions\Media\ResolveResponsiveImageAction;
use Capell\LayoutBuilder\Contracts\WidgetExtensions\WidgetExtensionBatchPayloadResolver;
use Capell\LayoutBuilder\Data\WidgetExtensions\WidgetExtensionPayloadBatchData;
use Capell\WidgetYouTube\Data\YouTubeInputData;
use Capell\WidgetYouTube\Data\YouTubeRenderData;
use Override;

final class ResolveYouTubePayloadsAction implements WidgetExtensionBatchPayloadResolver
{
    /** @return array<string, YouTubeRenderData> */
    #[Override]
    public function resolve(WidgetExtensionPayloadBatchData $batch): array
    {
        $payloads = [];

        foreach ($batch->items as $item) {
            if (! $item->input instanceof YouTubeInputData) {
                continue;
            }

            $size = ResolveResponsiveImageAction::run($item->input->poster);
            $payloads[$item->instanceId] = new YouTubeRenderData(
                videoId: $item->input->videoId,
                poster: $item->input->poster,
                title: $item->input->title,
                caption: $item->input->caption,
                transcript: $item->input->transcript,
                captionsOn: $item->input->captionsOn,
                captionLanguage: $item->input->captionLanguage,
                posterWidth: $size?->width,
                posterHeight: $size?->height,
                posterSrcset: $size?->srcset,
            );
        }

        return $payloads;
    }
}
