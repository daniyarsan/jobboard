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
}(), $(function () {
    "use strict";

    $("#autocomplete-ajax").autocomplete({
        lookupFilter: function (a, e, n) {
            return new RegExp("\\b" + $.Autocomplete.utils.escapeRegExChars(n), "gi").test(a.value)
        }, onSelect: function (a) {
            $("#selction-ajax").html("You selected: " + a.value + ", " + a.data)
        }, onHint: function (a) {
            $("#autocomplete-ajax-x").val(a)
        }, onInvalidateSelection: function () {
            $("#selction-ajax").html("You selected: none")
        }
    });
    var a = $.map(["Anaheim Ducks", "Atlanta Thrashers", "Boston Bruins", "Buffalo Sabres", "Calgary Flames", "Carolina Hurricanes", "Chicago Blackhawks", "Colorado Avalanche", "Columbus Blue Jackets", "Dallas Stars", "Detroit Red Wings", "Edmonton OIlers", "Florida Panthers", "Los Angeles Kings", "Minnesota Wild", "Montreal Canadiens", "Nashville Predators", "New Jersey Devils", "New Rork Islanders", "New York Rangers", "Ottawa Senators", "Philadelphia Flyers", "Phoenix Coyotes", "Pittsburgh Penguins", "Saint Louis Blues", "San Jose Sharks", "Tampa Bay Lightning", "Toronto Maple Leafs", "Vancouver Canucks", "Washington Capitals"], function (a) {
            return {value: a, data: {category: "NHL"}}
        }),
        e = $.map(["Atlanta Hawks", "Boston Celtics", "Charlotte Bobcats", "Chicago Bulls", "Cleveland Cavaliers", "Dallas Mavericks", "Denver Nuggets", "Detroit Pistons", "Golden State Warriors", "Houston Rockets", "Indiana Pacers", "LA Clippers", "LA Lakers", "Memphis Grizzlies", "Miami Heat", "Milwaukee Bucks", "Minnesota Timberwolves", "New Jersey Nets", "New Orleans Hornets", "New York Knicks", "Oklahoma City Thunder", "Orlando Magic", "Philadelphia Sixers", "Phoenix Suns", "Portland Trail Blazers", "Sacramento Kings", "San Antonio Spurs", "Toronto Raptors", "Utah Jazz", "Washington Wizards"], function (a) {
            return {value: a, data: {category: "NBA"}}
        }), n = a.concat(e);

});
