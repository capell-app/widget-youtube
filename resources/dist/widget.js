const initialiseYouTube = (root) => {
    if (root.dataset.ready === 'true') return
    root.dataset.ready = 'true'
    root.querySelector('[data-youtube-activate]')?.addEventListener(
        'click',
        () => {
            const notice = root.querySelector('[data-youtube-notice]')
            if (window.CapellPrivacy?.allows?.('youtube') === false) {
                notice.textContent = root.dataset.privacyDenied || ''
                return
            }
            const template = root.querySelector('[data-youtube-frame]')
            const frame = template?.content.firstElementChild?.cloneNode(true)
            if (!(frame instanceof HTMLIFrameElement)) return
            const parameters = new URLSearchParams({
                autoplay: '1',
                rel: '0',
                playsinline: '1',
            })
            if (root.dataset.captions === 'true')
                parameters.set('cc_load_policy', '1')
            const language = root.dataset.captionLanguage
            if (
                language &&
                /^[a-z]{2,3}(?:-[A-Za-z0-9]{2,8})?$/.test(language)
            ) {
                parameters.set('hl', language)
                parameters.set('cc_lang_pref', language.split('-')[0])
            }
            frame.src = `https://www.youtube-nocookie.com/embed/${encodeURIComponent(root.dataset.videoId)}?${parameters}`
            root.querySelector('.capell-youtube__frame')?.replaceChildren(frame)
            // The activate button is replaced, so focus would otherwise fall back to the page.
            frame.focus()
        },
    )
}
document
    .querySelectorAll('[data-capell-widget-youtube]')
    .forEach(initialiseYouTube)
document.addEventListener('capell:content-ready', (event) =>
    event.target
        ?.querySelectorAll?.('[data-capell-widget-youtube]')
        .forEach(initialiseYouTube),
)
