import register from './ui-bootstrap-jquery/register'

class LegoAPI {
    constructor() {
        this.data = new Map()
    }

    setData(type, id, value) {
        if (!this.data.has(type)) {
            this.data.set(type, new Map());
        }

        this.data.get(type).set(id, value)
    }

    getData(type, id, defaultValue = null) {
        if (this.data.has(type) && this.data.get(type).has(id)) {
            return this.data.get(type).get(id)
        }
        return defaultValue;
    }

    register() {
        register()
    }
}

window.lego = new LegoAPI();
document.addEventListener("DOMContentLoaded", () => window.lego.register());
export default window.lego
