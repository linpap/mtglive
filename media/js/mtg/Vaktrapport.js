Mtg.namespace('Vaktrapport');

Mtg.Vaktrapport = (function() {
    "use strict";

    var genogram = function(obj) {
        var isReportPageVisible = $('.main-report-section').is(':visible');
        if(!isReportPageVisible) {
            $('a[name="tab1"]').click(function() {
                location.href = $('.main-report-section').attr('data-url');
            });
        }
    };

    return {
        genogram : genogram
    }
}());