!function (t) {
    "use strict";
    var a = function () {
    };
    a.prototype.initSelect2 = function () {
        t('[data-toggle="select2"]').select2()
    }, a.prototype.initSwitchery = function () {
        t('[data-plugin="switchery"]').each(function (a, e) {
            new Switchery(t(this)[0], t(this).data())
        })
    }, a.prototype.initMultiSelect = function () {
        0 < t('[data-plugin="multiselect"]').length && t('[data-plugin="multiselect"]').multiSelect(t(this).data())
    }, a.prototype.initTouchspin = function () {
        var i = {};
        t('[data-toggle="touchspin"]').each(function (a, e) {
            var n = t.extend({}, i, t(e).data());
            t(e).TouchSpin(n)
        })
    }, a.prototype.init = function () {
        this.initSelect2(), this.initSwitchery(), this.initMultiSelect(), this.initTouchspin()
    }, t.FormAdvanced = new a, t.FormAdvanced.Constructor = a
}(window.jQuery), function (a) {
    "use strict";
    window.jQuery.FormAdvanced.init()
}();
