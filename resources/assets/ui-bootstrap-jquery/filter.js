export function initFilterInlineStyle(filter) {
    filter.classList.remove('form-inline')
    filter.classList.add('form')
    filter.classList.add('row')
    filter.querySelectorAll('.form-group').forEach(fg => {
        const times = Math.max(fg.getElementsByClassName('form-control').length, 1)
        fg.classList.add('col-sm-' + (4 * times))
        fg.classList.add('col-md-' + (4 * times))
        fg.classList.add('col-lg-' + (3 * times))
    })
}
