Mtg.namespace('Client.Weekplan');

Mtg.Client.Weekplan = (function() {
    "use strict";

    var create = function(element) {
        $("#loading-image").show();
        var tag = $("<div></div>"),
            title = $(element).data('title');

        $.ajax({
                url: $(element).data('url'),
                type: 'GET',
                error: function() { alert('Could not load form') },
                success: function(data, textStatus, jqXHR) {
                    if(typeof data == "object" && data.html) { //response is assumed to be JSON
                        tag.html(data.html).dialog({modal:true, title: title}).dialog('open');
                    } else { //response is assumed to be HTML
                        tag.html(data).dialog({modal: true, title: title, height: 350, width:800, hide: "scale", show : "scale",
                            open: function (event, ui) {
                                var startDate = $(".DyndatepickerImage1");
                                $('#weekplan_time').timepicker(Mtg.Config.Timepicker);
                                startDate.unbind();
                                startDate.datepicker(Mtg.Config.Datepicker);
                                $.validate({
                                    form : '#frmClientEditMaal',
                                    validateOnBlur : false,
                                    borderColorOnError : '#C90312',
                                    addValidClassOnAll : true,
                                    showHelpOnFocus : false,
                                    addSuggestions : false,
                                    errorMessagePosition : 'top',
                                    scrollToTopOnError : true
                                });
                                $("#loading-image").hide();
                            },
                            close: function (event, ui) {
                                $('input.DyndatepickerImage1').datepicker("destroy");
                                $(this).dialog("destroy");
                                $(this).remove();
                            }
                        }).dialog('open');
                    }
                }
            });
            return false;
    };

    var changeWeek = function(element) {
        var parent = $(element).closest('#weekplan-change-week'),
            week = parent.find('#week-selector :selected').val(),
            year = parent.find('#year-selector :selected').val(),
            url = parent.attr('data-url') + 'year/' + year + '/week/' + week;

        location.href = url;
        return false;
    };

    return {
        changeWeek : changeWeek,
        create : create
    };
}());