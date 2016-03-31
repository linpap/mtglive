// JavaScript Document
$(document).ready(function () {
            //for  i8
            $('html').bind('mousewheel', function (event, delta) {
                window.parent.scrollBy(-120 * delta, 0);
                return false;
            });
            //for  i10
            $(window).bind('mousewheel DOMMouseScroll', function (event) {
               
                if (event.ctrlKey == true) {
                    event.preventDefault();
                }
            });
            //$(document).keydown(function (event) {

            //    if (event.ctrlKey == true || event.which == '17') {
            //        //alert(event.which);
            //        // alert(event.ctrlKey);
            //        event.preventDefault();

            //    }
               
            //});
        });

$(document).ready(function () {
            $(document).keydown(function (event) {

                if (event.ctrlKey == true || event.which == '17') {
                    //alert(event.which);
                    // alert(event.ctrlKey);
                    event.preventDefault();

                }
               
            });
        });		