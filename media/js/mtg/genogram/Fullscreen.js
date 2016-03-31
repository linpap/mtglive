Mtg.namespace('Genogram.Fullscreen');

Mtg.Genogram.Fullscreen = (function() {
    "use strict";

    var display = function(element, title) {
        var width = $( "body" ).width() - 75,
            height = $( "body").height() - 25,
            dialog;

        save();

        var dialog = $( element ).dialog({
            autoOpen: false,
            height: height,
            width: width,
            modal: true,
            moveToTop: true,
            title: title,
            close: function(event, id) {
                save();
                saveDocument(function(response) {
                    $('#genogram-editor').html(dialog.html());
                    $('body').find('#myDiagram').css('max-height', '550px');
                    init();
                    location.reload();

                });
            }
        });

        $(element).find('#myDiagram').css('height', height / 1.6 + 'px').css('max-height', '');
        dialog.dialog( "open" );
    };

    return {
        display : display
    }
}());