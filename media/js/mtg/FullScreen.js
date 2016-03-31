Mtg.namespace('FullScreen');

Mtg.FullScreen = (function() {
    "use strict";

    var _elementId;

    var init = function() {
        /**$(document).click(function(e) {
            console.log('click');
            if( e.target.id != 'fullscreen-container') {
                hide();
            }
        });**/
    };

    var display = function(element) {
        var fullscreenEl = $('#fullscreen'),
            html;
            _elementId = $(element).attr('data-fullscreen-element');




        html = $('#' + _elementId).addClass('fullscreen');
//        fullscreenEl.find('.fullscreen-container').html(html);

        //fullscreenEl.show();
    };

    var hide = function() {
        var fullscreenEl = $('#fullscreen');
        fullscreenEl.hide();
    };

    init();
    return {
        display : display
    }
}());