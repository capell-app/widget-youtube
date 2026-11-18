import assert from 'node:assert/strict'
import { readFileSync } from 'node:fs'
import test from 'node:test'
import vm from 'node:vm'

const source = readFileSync(
    new URL('../../resources/dist/widget.js', import.meta.url),
    'utf8',
)

class FakeIFrame {
    focused = false
    focus() {
        this.focused = true
    }
}

function activate(dataset, privacy) {
    let click
    let mounted
    const notice = { textContent: '' }
    const frame = new FakeIFrame()
    const root = {
        dataset: { videoId: 'dQw4w9WgXcQ', ...dataset },
        querySelector: (selector) =>
            ({
                '[data-youtube-activate]': {
                    addEventListener: (event, listener) => {
                        click = listener
                    },
                },
                '[data-youtube-notice]': notice,
                '[data-youtube-frame]': {
                    content: { firstElementChild: { cloneNode: () => frame } },
                },
                '.capell-youtube__frame': {
                    replaceChildren: (child) => {
                        mounted = child
                    },
                },
            })[selector],
    }
    vm.runInNewContext(source, {
        HTMLIFrameElement: FakeIFrame,
        URLSearchParams,
        window: { CapellPrivacy: privacy },
        document: { querySelectorAll: () => [root], addEventListener() {} },
    })
    click()
    return { frame, mounted, notice }
}

test('focus moves to the iframe that replaces the play button', () => {
    const { frame, mounted } = activate({})
    assert.equal(mounted, frame)
    assert.equal(frame.focused, true)
})

test('captions and language are passed to the player only when configured', () => {
    const plain = activate({}).frame.src
    assert.ok(!plain.includes('cc_load_policy'))
    assert.ok(!plain.includes('hl='))
    const captioned = activate({ captions: 'true', captionLanguage: 'fr-CA' })
        .frame.src
    assert.ok(captioned.includes('cc_load_policy=1'))
    assert.ok(captioned.includes('hl=fr-CA'))
    assert.ok(captioned.includes('cc_lang_pref=fr'))
    assert.ok(
        !activate({ captionLanguage: 'en&evil=1' }).frame.src.includes('evil'),
    )
})

test('a refusal by privacy choices loads nothing and keeps focus on the button', () => {
    const { frame, mounted, notice } = activate(
        { privacyDenied: 'Denied' },
        { allows: () => false },
    )
    assert.equal(mounted, undefined)
    assert.equal(frame.focused, false)
    assert.equal(notice.textContent, 'Denied')
})
