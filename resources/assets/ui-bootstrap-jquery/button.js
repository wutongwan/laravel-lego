export function initButtonCountdown(btn) {
    const countdown = function (second, first = true) {
        if (second <= 0) {
            btn.innerText = '确认'
            btn.classList.remove('disabled')
        } else {
            if (first) {
                btn.classList.add('disabled')
            }
            btn.innerText = (second--) + ' 秒后可确认'
            setTimeout(function () {
                countdown(second, false);
            }, 1000)
        }
    }

    const seconds = btn.getAttribute('data-lego-button-delay')
    if (seconds > 0) {
        countdown(seconds)
    }
}


export function initButtonPreventRepeat(btn) {
    btn.addEventListener('click', function () {
        setTimeout(() => {
            btn.setAttribute('disabled', true)
            btn.setAttribute('href', 'javascript:;')
        }, 0)
    })
}
