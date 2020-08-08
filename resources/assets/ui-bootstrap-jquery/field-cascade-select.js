class CascadeSelect {
    constructor(select) {
        this.select = select
        this.depend = select.getAttribute('data-depend')
        this.selected = select.getAttribute('data-selected')
        this.remote = decodeURIComponent(select.getAttribute('data-remote'))
    }

    updateOptions(newDependValue) {
        this.setSelectedValue('')
        this.setOptions({})
        if (newDependValue) {
            fetch(this.remote + newDependValue)
                .then(function (response) {
                    if (!response.ok) {
                        throw response
                    }
                    return response.json();
                })
                .then(options => {
                    this.setOptions(options)
                    this.setSelectedValue(Object.keys(options)[0])
                })
                .catch(error => alert(`更新级联选项失败 [${error}]`))
        }
    }

    setSelectedValue(value) {
        this.select.value = value
        this.select.dispatchEvent(new Event('change'))
    }

    setOptions(options) {
        const strings = []
        Object.entries(options)
            .forEach(option => strings.push(`<option value="${option[0]}">${option[1]}</option>`))
        this.select.innerHTML = strings.join('\n')
    }

    register() {
        const that = this
        document.getElementById(this.depend).addEventListener('change', function () {
            that.updateOptions(this.value)
        })
    }
}

function initCascadeSelect(select) {
    (new CascadeSelect(select)).register()
}

export default initCascadeSelect;
