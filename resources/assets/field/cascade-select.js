/**
 * Cascade Select Input
 */

(function () {
    window.cascadeSelectManager = new CascadeSelectManger();

    function CascadeSelectManger() {
        let registered = {};
        let dependency = {};

        this.add = function (vue) {
            if (dependency[vue.depend]) {
                dependency[vue.depend].push(vue);
            } else {
                dependency[vue.depend] = [vue];
            }
            registered[vue.id] = vue;
        };

        this.get = function (id) {
            return registered[id];
        };

        this.run = function (depend, value) {
            for (i = 0; i < dependency[depend].length; i++) {
                let vue = dependency[depend][i];
                vue.syncOptions(value);
            }
        }
    }
})();
