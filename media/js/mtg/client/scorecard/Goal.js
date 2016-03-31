Mtg.namespace('Client.Scorecard.Goal');

Mtg.Client.Scorecard.Goal = (function() {
    "use strict";

    var createGoal = function(element) {
        var url = $(element).attr('data-url'),
            title = $(element).attr('data-title');

       Mtg.Dialog.ajax(title, url,  250, 800, function() {
           var startDate = $(".DyndatepickerImage");
           startDate.unbind();
           startDate.datepicker(Mtg.Config.Datepicker);
        });

        return false;
    };

    var createShortTermGoal = function(element) {

        var url = $(element).attr('data-url'),
            title = $(element).attr('data-title');

        Mtg.Dialog.ajax(title, url,  300, 800, function() {
        });
        return false;
    };

    return {
        createGoal : createGoal,
        createShortTermGoal : createShortTermGoal
    };
}());