# YouTube Widget

<!-- prettier-ignore-start -->

## What it does

YouTube lets editors add a video while preserving a privacy-first first load. Capell stores the normalized video identifier and renders a local poster until the visitor chooses to play.

## Do I need to do anything?

Add **YouTube video** in Layout Builder, paste a supported YouTube URL, upload a local poster image, and enter the required iframe title. You can add an optional caption. Capell normalizes the URL to the video identifier when the widget is saved; preview the public page before publishing.

## Where it shows up

Editors configure it as a Layout Builder widget for the URL, local poster, title, and optional caption. Visitors first receive the local poster and play control; the privacy-enhanced iframe is created only after activation. Without JavaScript, they receive a direct **Watch on YouTube** link instead.

## Good to know

- This free beta extension belongs to Capell Foundation and requires Layout Builder.
- The saved video identifier must be exactly 11 URL-safe characters, and the poster must be local media rather than a remote URL.
- No request reaches YouTube before explicit activation. When Privacy Center denies `youtube`, playback remains disabled and the visitor sees the privacy notice.
- The activated iframe uses `youtube-nocookie.com`, a lazy load, a restrictive sandbox, and a strict-origin referrer policy.

## Screenshot status

Capture the video fields and the public pre-activation poster with its play control and privacy explanation visible. The committed Marketplace card is interim beta artwork until these two fixture captures are recorded.

---

For developers: see the [README](../README.md).

<!-- prettier-ignore-end -->
