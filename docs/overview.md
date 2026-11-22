# YouTube Widget

<!-- prettier-ignore-start -->

A picture you chose, with a Play button on it, stands in for the video, and the YouTube video behind it loads only when a visitor presses play. Until then the page doesn't contact YouTube at all.

Setting one up means pasting a normal YouTube link and uploading a still image of your own, because it won't borrow YouTube's thumbnail. Give the video a title as well, which screen readers read out on the Play button.

Your site may run Privacy Centre, which asks visitors for their cookie choices. If so, anyone who turned down marketing cookies, or whose browser asks sites not to track them, sees a short message and nothing loads. A browser that blocks scripts gets a plain link to watch on YouTube.

Once someone presses play, the video starts right there on the page. It plays in YouTube's privacy-enhanced mode, which reduces YouTube's tracking but doesn't remove it, and YouTube may still store things in the browser. When the video ends, the suggestions come from the same channel as the one just played.

## What it does

YouTube lets editors add a video while preserving a privacy-first first load. Capell stores the normalised video identifier and renders a local poster until the visitor chooses to play.

## Do I need to do anything?

Add **YouTube video** in Layout Builder, paste a supported YouTube URL, upload a local poster image, and enter the required iframe title. You can add an optional caption. Capell normalises the URL to the video identifier when the widget is saved; preview the public page before publishing.

## Where it shows up

Editors configure it as a Layout Builder widget for the URL, local poster, title, and optional caption. Visitors first receive the local poster and play control; the privacy-enhanced iframe is created only after activation. Without JavaScript, they receive a direct **Watch on YouTube** link instead.

## Good to know

- This free extension belongs to Capell Essentials and requires Layout Builder.
- The saved video identifier must be exactly 11 URL-safe characters, and the poster must be local media rather than a remote URL.
- No request reaches YouTube before explicit activation. When Privacy Centre denies `youtube`, playback remains disabled and the visitor sees the privacy notice.
- The activated iframe uses `youtube-nocookie.com`, a lazy load, a restrictive sandbox, and a strict-origin referrer policy.

---

For developers: see the [README](../README.md).

<!-- prettier-ignore-end -->
