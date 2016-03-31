Mtg.namespace('EventDialog');

Mtg.EventDialog = (function() {
    "use strict";

    var init = function() {
        $('body').on('click', '.week-day', function() {
            var title = 'Dagens aktiviteter',
                date = $(this).attr('data-date'),
                url = Mtg.Config.System.baseUrl + '/system/index/events/date/' + date;
            Mtg.Dialog.ajax(title, url, 500, 800, function() {});
        });
    };

   init();
}());