// JavaScript Document
$(function(){
                var base = 'body';
                $('a[href^="#"]').each(function(){
                        var name = $(this).attr('href').substr(1);
                        var anchor = document.getElementById(name) || document.getElementsByName(name);
                        if(anchor = (anchor.item)?anchor.item(0):anchor){
                                var offset = $(base+' > .rollbar-content').height() - $(anchor).offset().top;
                                $(this).click(function(){
                                        $(base).trigger('rollbar',-offset);
                                });
                        }       
                });
        });
window.arr_weekdays = ['SØNDAG', 'MANDAG', 'TIRSDAG', 'ONSDAG', 'TORSDAG', 'FREDAG', 'LØRDAG'];
 window.arr_monthnames = [ "Januar", "Februar", "Mars", "April", "Mai", "Juni",
 "Juli", "August", "September", "Oktober", "November", "Desember" ];