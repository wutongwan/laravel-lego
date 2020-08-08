class GridBatch {
    constructor(element) {
        this.element = element
        this.ids = []
    }

    listen() {
        const that = this

        // 全选
        this.element.getElementsByClassName('lego-select-all')[0]
            .addEventListener('click', () => that.setInputIds(that.getAllIds()))

        // 反选
        this.element.getElementsByClassName('lego-select-reverse')[0]
            .addEventListener('click', () => {
                let newIds = []
                const currentIds = that.getInputIds()
                that.getAllIds().forEach(id => {
                    if (currentIds.includes(id) === false) {
                        newIds.push(id)
                    }
                })
                that.setInputIds(newIds)
            })

        // 每行的 checkbox
        for (const cb of this.element.getElementsByClassName('lego-batch-checkbox')) {
            // 浏览器返回时 checkbox 状态不会丢失，所以在这里先刷一遍
            cb.checked ? that.addInputId(cb.value) : that.delInputId(cb.value)
            cb.addEventListener('change', function () {
                this.checked ? that.addInputId(this.value) : that.delInputId(this.value)
            })
        }

        // 提交
        for (const btn of this.element.getElementsByClassName('lego-batch-submit')) {
            btn.addEventListener('click', function (event) {
                event.preventDefault()
                that.submit(
                    decodeURIComponent(this.getAttribute('data-action')),
                    this.getAttribute('data-open-target'),
                    this.getAttribute('data-name'),
                )
            })
        }
    }

    submit(action, target, name) {
        if (this.getInputIds().length === 0) {
            alert("尚未选中任何记录！")
            return false;
        }

        const form = this.element.getElementsByClassName('lego-batch-form')[0]
        form.action = action
        form.target = target
        form.submit()
    }

    getAllIds() {
        if (this.ids.length > 0) {
            return this.ids
        }
        for (const el of this.element.getElementsByClassName('lego-batch-checkbox')) {
            this.ids.push(el.value)
        }
        return this.ids
    }

    setInputIds(ids) {
        this.setInputIdsValue(ids)
        for (const el of this.element.getElementsByClassName('lego-batch-checkbox')) {
            el.checked = ids.includes(el.value)
        }
    }

    setInputIdsValue(ids) {
        this.element.getElementsByClassName('lego-selected-count')[0].innerText = ids.length
        this.element.querySelector('input[name="ids"]').value = [].join.call(ids, ',')
    }

    getInputIds() {
        const value = this.element.querySelector('input[name="ids"]').value
        return value.trim(',').split(',').filter(val => val)
    }

    addInputId(id) {
        let currentIds = this.getInputIds()
        if (currentIds.includes(id)) {
            return true
        }
        currentIds.push(id)
        this.setInputIdsValue(currentIds)
    }

    delInputId(id) {
        let currentIds = this.getInputIds()
        if (!currentIds.includes(id)) {
            return true
        }
        const index = currentIds.indexOf(id);
        if (index > -1) {
            currentIds.splice(index, 1);
        }
        this.setInputIdsValue(currentIds)
    }
}

function initGridBatch(element) {
    (new GridBatch(element)).listen()
}

export default initGridBatch
