class CascadeSelect {
    constructor(select) {
        this.select = select
        this.depend = select.getAttribute('data-depend')
        this.selected = select.getAttribute('data-selected')
        this.remote = decodeURIComponent(select.getAttribute('data-remote'))
        this.required = select.getAttribute('data-required') === 'true'
        this.placeholder = select.getAttribute('data-placeholder')
        this.init = true
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
                    if (this.init && this.selected && options.hasOwnProperty(this.selected)) {
                        this.setSelectedValue(this.selected) // 第一次更新时选中旧值
                    } else {
                        this.setSelectedValue(Object.keys(options)[0]) // 之后默认选中第一项
                    }
                    this.init = false
                })
                .catch(() => alert(`更新级联选项失败`))
        }
    }

    setSelectedValue(value) {
        this.select.value = value
        this.select.dispatchEvent(new Event('change'))
    }

    setOptions(options) {
        const strings = []
        if (this.required === false) {
            strings.push(`<option>* ${this.placeholder} *</option>>`)
        }
        Object.entries(options)
            .forEach(option => strings.push(`<option value="${option[0]}">${option[1]}</option>`))
        this.select.innerHTML = strings.join('\n')
    }

    register() {
        const depend = document.getElementById(this.depend)
        if (depend.value !== '') {
            this.updateOptions(depend.value)
        }

        const that = this
        depend.addEventListener('change', function () {
            that.updateOptions(this.value)
        })
    }
}

function initCascadeSelect(select) {
    (new CascadeSelect(select)).register()
}

export default initCascadeSelect;
