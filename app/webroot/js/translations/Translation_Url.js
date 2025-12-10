$(document).ready(function () {
    i18n_Url.load_Url();
});

var i18n_Url = (function () {

    var language_Url = function () {
        var myDictionary = null;
        $.i18n.load(myDictionary);
    };

    return {
        load_Url: function () {
            language_Url();
        }
    }
})();