class GridBatch {
    constructor(element) {
        this.element = element
        this.ids = []
        this.$modal = jQuery(document.getElementById('lego-grid-modal'))

        this.idsInput = this.element.querySelector('input[name="__lego_ids"]')
        this.idsCountInput = this.element.querySelector('input[name="__lego_ids_count"]')
        this.respIdInput = this.element.querySelector('input[name="__lego_resp_id"]')
    }

    listen() {
        const that = this

        // 切换批处理模式显示与否的按钮
        this._toggleBatchMode('auto') // 根据上一次状态自动开启
        this.element.getElementsByClassName('lego-enable-batch')[0]
            .addEventListener('click', () => this._toggleBatchMode(true))
        this.element.getElementsByClassName('lego-disable-batch')[0]
            .addEventListener('click', () => this._toggleBatchMode(false))

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
            setTimeout(() => cb.checked ? that.addInputId(cb.value) : that.delInputId(cb.value))
            cb.addEventListener('change', function () {
                this.checked ? that.addInputId(this.value) : that.delInputId(this.value)
            })
        }

        // 提交
        for (const btn of this.element.getElementsByClassName('lego-batch-submit')) {
            btn.addEventListener('click', function (event) {
                event.preventDefault()
                that.submit(decodeURIComponent(this.getAttribute('data-resp-id')))
            })
        }

        this.$modal.on('hidden.bs.modal', function () {
            that.$modal.find('iframe').attr('src', '')
        })
    }

    _toggleBatchMode(yes) {
        const storageKey = 'Lego:Grid:BatchSwitcher:' + window.location.pathname

        // 根据 localStorage 值自动开启
        if (yes === 'auto') {
            return this._toggleBatchMode(window.localStorage.getItem(storageKey) !== null)
        }

        if (yes) {
            this.element.getElementsByClassName('lego-enable-batch')[0].classList.add('hide')
            this.element.getElementsByClassName('lego-disable-batch')[0].classList.remove('hide')
            this.element.getElementsByClassName('lego-grid-batch-tools')[0].classList.remove('hide')
            this.element.querySelectorAll('.lego-batch-item').forEach(el => el.classList.remove('hide'))
            window.localStorage.setItem(storageKey, Date.now())
        } else {
            this.element.getElementsByClassName('lego-enable-batch')[0].classList.remove('hide')
            this.element.getElementsByClassName('lego-disable-batch')[0].classList.add('hide')
            this.element.getElementsByClassName('lego-grid-batch-tools')[0].classList.add('hide')
            this.element.querySelectorAll('.lego-batch-item').forEach(el => el.classList.add('hide'))
            window.localStorage.removeItem(storageKey)
        }
    }

    submit(respId) {
        if (this.getInputIds().length === 0) {
            alert("尚未选中任何记录！")
            return false;
        }

        this.respIdInput.value = respId
        const form = this.element.getElementsByClassName('lego-batch-form')[0]
        form.submit()

        this.$modal.modal()
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
        this.idsInput.value = [].join.call(ids, ',')
        this.idsCountInput.value = ids.length
    }

    getInputIds() {
        const value = this.idsInput.value
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

export default function initGridBatch(element) {
    (new GridBatch(element)).listen()
}
