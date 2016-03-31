Mtg.namespace('Dialog');

Mtg.Dialog = (function() {
    "use strict";

    var ajax = function(title, url, height, width, fn) {
        $("#loading-image").show();

        var tag = $("<div></div>"); //This tag will the hold the dialog content.

        $.ajax({
            url: url,
            type: 'GET',
            error: function() {
                alert('Could not load form')
            },
            success: function(data, textStatus, jqXHR) {
                tag.html(data).dialog({
                    modal: true,
                    title: title,
                    height: height,
                    width: width,
                    hide: "scale",
                    show : "scale",
                    open: function (event, ui) {
                        fn();
                        $("#loading-image").hide();
                    },
                    close: function (event, ui) {
                        $('input.DyndatepickerImage1').datepicker("destroy");
                        $(this).dialog("destroy");
                        $(this).remove();
                    }
                }).dialog('open');
            }
        });
    };

    var display = function(html, title, height, width, closeFn) {
        $("#loading-image").show();

        var tag = $("<div></div>"); //This tag will the hold the dialog content.
        tag.html(html).dialog({
            modal: true,
            title: title,
            height: height,
            width: width,
            hide: "scale",
            show : "scale",
            open: function (event, ui) {
                $("#loading-image").hide();
            },
            close: function (event, ui) {
                closeFn();
                $('input.DyndatepickerImage1').datepicker("destroy");
                $(this).dialog("destroy");
                $(this).remove();
            }
        }).dialog('open');
    };

    return {
        ajax : ajax,
        display: display
    }

}());