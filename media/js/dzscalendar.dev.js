
/*
 * Author: Digital Zoom Studio
 * Website: http://digitalzoomstudio.net/
 * Portfolio: http://codecanyon.net/user/ZoomIt/portfolio
 *
 * Version: 3.60
 */


if(window.jQuery==undefined){
    alert("dzscalendar.js -> jQuery is not defined or improperly declared ( must be included at the start of the head tag ), you need jQuery for this plugin");
}
var settings_dzscalendar = { animation_time: 500, animation_easing:'swing' };

function is_ios() {
    return ((navigator.platform.indexOf("iPhone") != -1) || (navigator.platform.indexOf("iPod") != -1) || (navigator.platform.indexOf("iPad") != -1)
        );
}
function is_android() {
    //return true;
    return (navigator.platform.indexOf("Android") != -1);
}

function is_ie(){
    if (navigator.appVersion.indexOf("MSIE") != -1){
        return true;
    };
    return false;
};
function is_firefox(){
    if (navigator.userAgent.indexOf("Firefox") != -1){
        return true;
    };
    return false;
};
function is_opera(){
    if (navigator.userAgent.indexOf("Opera") != -1){
        return true;
    };
    return false;
};
function is_chrome(){
    return navigator.userAgent.toLowerCase().indexOf('chrome') > -1;
};
function is_safari(){
    return navigator.userAgent.toLowerCase().indexOf('safari') > -1;
};
function version_ie(){
    return parseFloat(navigator.appVersion.split("MSIE")[1]);
};
function version_firefox(){
    if (/Firefox[\/\s](\d+\.\d+)/.test(navigator.userAgent)){
        var aversion=new Number(RegExp.$1);
        return(aversion);
    };
};
function version_opera(){
    if (/Opera[\/\s](\d+\.\d+)/.test(navigator.userAgent)){
        var aversion=new Number(RegExp.$1);
        return(aversion);
    };
};
function is_ie8(){
    if(is_ie()==true && version_ie() < 9){
        return true;
    }
    return false;
}
(function($) {

    $.fn.dzscalendar = function(o) {

        var defaults = {
            settings_slideshowTime : '5' // --- in seconds
            ,mode: 'normal' // ---- normal or datepicker or blogevents
            , settings_autoHeight : 'on'
            , settings_skin : 'skin-default'
            , start_month : ''
            , start_year : ''
            , start_day : ''
            , start_weekday : 'Sunday' // ---- Sunday or Monday
            , design_transition: 'default' // ---- default ( based on skin ) or slide or fade
            , design_transitionDesc: 'tooltipDef' // ---- the event transition - tooltipDef or slide
            ,header_weekdayStyle: 'default' // --- default, three, full
            ,settings_alwaysinclude6rows: 'default' // --- 6 rows is the max
            ,mode_blogevents_container: '.content' // --- select a dom elements in which the event to appear
            ,mode_blogevents_defaultaction: 'none' // --- select none or today to illustrate the default event
            , settings_makeFunctional: false
            ,design_month_covers: ['http://dummyimage.com/940x350/222224/2e2e2e.jpg','http://dummyimage.com/940x350/222224/2e2e2e.jpg','http://dummyimage.com/940x350/222224/2e2e2e.jpg','http://dummyimage.com/940x350/222224/2e2e2e.jpg','http://dummyimage.com/940x350/222224/2e2e2e.jpg','http://dummyimage.com/940x350/222224/2e2e2e.jpg','http://dummyimage.com/940x350/222224/2e2e2e.jpg','http://dummyimage.com/940x350/222224/2e2e2e.jpg','http://dummyimage.com/940x350/222224/2e2e2e.jpg','http://dummyimage.com/940x350/222224/2e2e2e.jpg','http://dummyimage.com/940x350/222224/2e2e2e.jpg','http://dummyimage.com/940x350/222224/2e2e2e.jpg'] // --- select images for the cover photos of the calendar
        }

        o = $.extend(defaults, o);
        this.each( function(){
            var cthis = $(this);
            var cclass = '';
            var tw
                ,th
                ;
            var cchildren = cthis.children();
            var currNr=-1
                ,currMon=0
                ,currYear=0
                ,_currTable
                ,currHeight
                ,currWidth
                ,_calendarControls
                ,currDesc
                ,_argTable
                ,_auxTransition
                ,_theMonths
                ,busy = false
                ,forward=false
                ,transitioned = false // == transition started
                ;
            var _c;
            var timebuf=0
                ,skin_tableWidth = 182
                ,skin_normalHeight = 138
                ;
            var slideshowTime = parseInt(o.settings_slideshowTime);
            var i=0, j=0, k=0;
            var theMonths
                ,theControls
                ,_currDate
                ;
            var events = [];
            var now
                ,dat
                ;
            var posX, posY, origH='auto';
            var arr_monthnames = [ "January", "February", "March", "April", "May", "June",
                "July", "August", "September", "October", "November", "December" ];
            var arr_weekdays = ['SUNDAY', 'MONDAY', 'TUESDAY', 'WEDNESDAY', 'THURSDAY', 'FRIDAY', 'SATURDAY'];
            var inter_hidetooltips;

            if(typeof(window.arr_weekdays)=='object' && window.arr_weekdays.length > 1){
                arr_weekdays = window.arr_weekdays;
            }


            if(typeof(window.arr_monthnames)=='object' && window.arr_monthnames.length > 1){
                arr_monthnames = window.arr_monthnames;
            }
            //console.info(typeof(window.arr_weekdays));

            init();

            function init(){
                // cchildren.eq(0).css('position', 'absolute');

                if(typeof(cthis.attr('class')) == 'string'){
                    cclass = cthis.attr('class');
                }else{
                    cclass=cthis.get(0).className;
                }

                if(cclass.indexOf("skin-")==-1){
                    cthis.addClass(o.settings_skin);
                }else{

                    if(cthis.hasClass('skin-default')){
                        o.settings_skin = 'skin-default';
                    }
                    if(cthis.hasClass('skin-black')){
                        o.settings_skin = 'skin-black';
                    }
                    if(cthis.hasClass('skin-aurora')){
                        o.settings_skin = 'skin-aurora';
                    }

                    if(cthis.hasClass('skin-responsive')){
                        o.settings_skin = 'skin-responsive';
                    }
                    if(cthis.hasClass('skin-responsive-galileo')){
                        o.settings_skin = 'skin-responsive-galileo';
                    }
                }

                cthis.addClass('mode-'+ o.mode);

                if(o.settings_skin=='skin-black'){

                    skin_tableWidth = 192;
                    skin_normalHeight = 158;
                    tw = 192; th = 158;
                }

                if(o.settings_skin=='skin-aurora'){
                    tw = 212;
                    th = 220;
                }
                if(o.settings_skin=='skin-responsive-galileo'){

                    if(o.design_transition=='default'){
                        o.design_transition = 'slide3d';
                    }
                    if(o.header_weekdayStyle=='default'){
                        o.header_weekdayStyle = 'responsivefull';
                    }
                }



                if(o.design_transitionDesc=='default'){
                    o.design_transitionDesc = 'tooltipDef';
                }
                if(o.design_transition=='default'){
                    o.design_transition = 'slide';
                }
                if(o.design_transition=='slide3d'){
                    o.settings_alwaysinclude6rows = 'on';
                }



                now = new Date();

                //console.log(now, now.getFullYear(), now.getDate());

                reinit();

                //==constructing in progress...
                cthis.append('<div class="calendar-controls">' + '<div class="curr-date"><span class="curr-month">' + ''+arr_monthnames[now.getMonth()]+'</span><span class="curr-year">'+now.getFullYear()+'</span></div>' + '<div class="arrow-left"></div><div class="arrow-right"></div>' + '</div>');

                cthis.append('<div class="theMonths"></div>');
                _currDate = cthis.find('.curr-date');

                _theMonths = cthis.children('.theMonths');
                theControls = cthis.children('.calendar-controls');
                _calendarControls = theControls;

                if(o.design_transitionDesc=='slide'){
                    _theMonths.css({'overflow': 'hidden'})
                }


                if(o.design_transition=='slide3d'){
                    _calendarControls.prepend('<div class="month-bg"></div>');
                }

                //var auxMout = auxWeekRow + auxWeekRow + auxWeekRow + auxWeekRow + auxWeekRow + auxWeekRow;

                //_theMonths.find('.mon-body').append('')
                //setInterval(tick, 1000);
                //gotoItem(0);


                cthis.get(0).arr_datepicker_events = [];
                cthis.get(0).api_reinit = reinit;

                cthis.find('.arrow-left').click(click_arrow_left);
                cthis.find('.arrow-right').click(click_arrow_right);
//                $(document).undelegate('.hasEvent', 'click');
                $(document).delegate('.hasEvent', 'click', click_event);
                $(document).delegate('.hasEventForHover', 'click', click_event);

                //console.info(cthis, o.design_transitionDesc)
                if(o.design_transitionDesc=='tooltipDef'){
                    $(document).delegate('.hasEventForHover', 'mouseenter', click_event);
                    $(document).delegate('.hasEventForHover', 'mouseleave', hide_tooltips);
                }
                if(o.mode=='datepicker'){
                    cthis.delegate('.mon-body .week-day', 'click', datepicker_click_day);
                }


                //cthis.find().live('click', );



                currMon = now.getMonth();
                currYear = now.getFullYear();



                if(o.mode=='blogevents'){
                    if(get_query_arg(window.location.href, 'dzscal_date')!=undefined){
                        var auxhref = get_query_arg(window.location.href, 'dzscal_date');
                        var auxhrefs = String(auxhref).split('-');
                        if(auxhrefs[0]){
                            o.start_month = auxhrefs[0];
                        }
                        if(auxhrefs[1]){
                            o.start_day = auxhrefs[1];
                        }
                        if(auxhrefs[2]){
                            o.start_year = auxhrefs[2];
                        }
                    }else{
                        if(o.mode_blogevents_defaultaction=='today'){
//                            console.info(now, now.getDate(), now.getMonth(), now.getFullYear());


                            o.start_month = now.getMonth()+1;
                            o.start_day = now.getDate();
                            o.start_year = now.getFullYear();
                        }
                    }
                }

                if(o.start_year!=''){
                    currYear = parseInt(o.start_year, 10);
                }
                if(o.start_month!=''){
                    currMon = parseInt(o.start_month, 10);
                    currMon--;
                }
//                console.log(currYear, currMon, o.start_month);



                gotoItem(currYear, currMon);


                _currTable = _theMonths.children('.currTable');


                if(o.start_day!=''){
                    o.start_day = parseInt(o.start_day,10)-1;
                    if(isNaN(o.start_day)==false){
                        _theMonths.find('.curr-months-date').eq(o.start_day).trigger('click');
                        if(_theMonths.find('.curr-months-date').eq(o.start_day).hasClass('hasEvent')==false){

                            $(o.mode_blogevents_container).html("No events for today. You can see upcoming events in the calendar to the right.");
                            $(o.mode_blogevents_container).addClass('active');
                        }
                    }
                }

                if(o.design_transition=='slide3d'){
                    the_transition_complete();
                }

                $(window).bind('click', click_window);
                $(window).bind('resize', handle_resize);
                handle_resize();

                /*

                 if(cclass.indexOf("responsive")==-1){
                 cthis.css('height', cthis.height()); origH = cthis.height();
                 }else{
                 }
                 */
                //console.log(origH);

            }


            function reinit(){

                for(i=0;i<cthis.children('.events').children().length;i++){
                    _c = cthis.children('.events').children().eq(i);
                    events[i] = ({date: _c.attr('data-date'), startdate: _c.attr('data-startdate'),enddate: _c.attr('data-enddate'), content: _c.html(), repeat: _c.attr('data-repeat'), day: parseInt(_c.attr('data-day'),10), month: parseInt(_c.attr('data-month'),10), year: _c.attr('data-year'), startday: _c.attr('data-startday'), endday: _c.attr('data-endday'), type: _c.attr('data-type'), href: _c.attr('data-href'), tag: _c.attr('data-tag')});
                }
                cthis.children('.events').remove();

                //console.info(events);

                if(_theMonths!=undefined){
                    gotoItem(currYear, currMon);
                }

            }


            function datepicker_click_day(e){
                var _t = $(this);
                //console.log(cthis, _t, _t.attr('data-date'));

                for(i=0;i<cthis.get(0).arr_datepicker_events.length;i++){
                    //console.log(i, Array(cthis.get(0).arr_datepicker_events), Array(cthis.get(0).arr_datepicker_events).length);
                    cthis.get(0).arr_datepicker_events[i](_t.attr('data-date'));
                }
            }
            function handle_resize(){


                if (o.settings_makeFunctional == true) {
                    var allowed = false;

                    var url = document.URL;
                    var urlStart = url.indexOf("://") + 3;
                    var urlEnd = url.indexOf("/", urlStart);
                    var domain = url.substring(urlStart, urlEnd);
                    //console.log(domain);
                    if (domain.indexOf('a') > -1 && domain.indexOf('c') > -1 && domain.indexOf('o') > -1 && domain.indexOf('l') > -1) {
                        allowed = true;
                    }
                    if (domain.indexOf('o') > -1 && domain.indexOf('z') > -1 && domain.indexOf('e') > -1 && domain.indexOf('h') > -1 && domain.indexOf('t') > -1) {
                        allowed = true;
                    }
                    if (domain.indexOf('e') > -1 && domain.indexOf('v') > -1 && domain.indexOf('n') > -1 && domain.indexOf('a') > -1 && domain.indexOf('t') > -1) {
                        allowed = true;
                    }
                    if (allowed == false) {
                        return;
                    }

                }

                tw = cthis.width();
                cthis.removeClass('under-240 under-480');
                while(1){
                    if(tw<=240){
                        cthis.addClass('under-240');
                        break;
                    }
                    if(tw<=480){
                        cthis.addClass('under-480');
                        break;
                    }
                    break;
                }


                var auxdays = '';

                // console.info(cthis, o.header_weekdayStyle, arr_weekdays);
                if(o.header_weekdayStyle=='responsivefull'){
                    var weekDays = [];
                    for(i=0;i<arr_weekdays.length;i++){

                        var aux = arr_weekdays[i];
                        weekDays[i] = aux;
                    }
                    if(cthis.hasClass('under-480')){
                        for(i=0;i<arr_weekdays.length;i++){
                            var aux = arr_weekdays[i].substr(0,3);
                            weekDays[i] = aux;
                        }

                    }
                    if(cthis.hasClass('under-240')){
                        for(i=0;i<arr_weekdays.length;i++){
                            var aux = arr_weekdays[i].substr(0,1);
                            weekDays[i] = aux;
                        }
                    }




                    var startindex = 0;
                    if(o.start_weekday=='Monday'){
                        startindex = 1;
                    }


                    for(i=startindex;i<arr_weekdays.length;i++){
                        auxdays+='<span class="week-day week-day-header">' + weekDays[i] + '</span>';
                    }
                    for(i=0;i<startindex;i++){
                        auxdays+='<span class="week-day week-day-header">' + weekDays[i] + '</span>';
                    }


                    cthis.find('.headerRow').html(auxdays);



                }



            }
            function hide_tooltips(){
                //console.log(currNr);
                if(o.design_transitionDesc=='tooltipDef'){
                    cthis.find('.dzstooltip').each(function(){
                        var _t2 = $(this);
                        _t2.removeClass('currTooltip');
                        _t2.removeClass('openTooltip');
                        _t2.animate({
                            'opacity' : 0
                            ,'left' : (posX + 25)
                        },{queue:false, complete:complete_removeTooltips, duration:settings_dzscalendar.animation_time/2, easing:settings_dzscalendar.animation_easing});


                    })
                }
                cthis.css('height', origH);
            }
            function click_window(e){
//                console.info('click_window', e.target)
                hide_tooltips();
            }
            function click_event(e){
                var _t = $(this);
//                console.log('click_event', cthis, _t);
                if(cthis.has(_t).length==0){
                    return;
                }
                //console.log(cthis, _t);
                if(_t.hasClass("desc-close-button")){


                    if(o.design_transitionDesc=='slide'){
                        currDesc.animate({'top' : -skin_normalHeight}
                            ,{queue:false, duration:settings_dzscalendar.animation_time/1.5, easing:settings_dzscalendar.animation_easing})
                        theControls.animate({'top' : 0}
                            ,{queue:false, duration:settings_dzscalendar.animation_time/1.5, easing:settings_dzscalendar.animation_easing})
                        _theMonths.animate({'top' : 0}
                            ,{queue:false, duration:settings_dzscalendar.animation_time/1.5, easing:settings_dzscalendar.animation_easing})
                        return;
                    }
                    return;


                }
                //return;
                //console.log(events);

                hide_tooltips();
                if(_t.hasClass('openTooltip')){
                    _t.removeClass('openTooltip')
                    return;
                }

//                clearTimeout(inter_hidetooltips);
//                inter_hidetooltips = setTimeout(hide_tooltips, 5000);


                var date = _t.attr('data-date');
                var date_y = _t.attr('data-year');
                var date_m = _t.attr('data-month');
                var date_d = _t.attr('data-day');
                var _par = _t.parent().parent().parent().parent().parent();
                k=0;

                var cont = '';

                //===========================
                //==== checking all event array for a match with the clicked day.
                var k =0;
                var swevent = false;



                cont = _t.children('.the-event-content').html();



                if(o.mode=='blogevents'){
//                    console.info(o.mode, cont, _t);
                    var stateObj = { foo: "bar" };

                    var newurl = '';
                    newurl=window.location.href;
                    newurl=add_query_arg(newurl, 'dzscal_date', _t.attr('data-date'));

//                    alert(can_history_api());
//                    console.info(newurl);
                    if((is_ie() && version_ie()<=9) || can_history_api()){

                        if((is_ie() && version_ie()<=9)==false){
                            history.pushState(stateObj, "Gallery Video", newurl);
                        }

                        $(o.mode_blogevents_container).html(cont);
                        $(o.mode_blogevents_container).addClass('active');
                    }else{
                        window.location.href = newurl;
                    }



                    return false;
                }

                //console.log(cont);
                if(e!=undefined){
                    //console.log(events[k].type);
                    if(e.type=='click'){
                        if(events[k].type=='link'){
                            if(events[k].href!='' && events[k].href!=undefined){
                                window.open(events[k].href,'_blank','');

                            }
                        }
                    }
                }

                if(cont==''){
                    return;
                }


                cthis.find('.openTooltip').each(function(){
                    var _t2 = $(this);
                    _t2.removeClass('openTooltip');
                })
                _t.addClass('openTooltip');

                var tt_w = 200;
                var dir='arrow-left';

                posX = _t.offset().left - _par.offset().left + _t.outerWidth();
                posY = _t.offset().top - _par.offset().top + 5;

                //===if the position left is to far to the right then let the tooltip come from the left
                if(posX + tt_w > $(window).width()){
                    dir='arrow-right';
                    posX = _t.offset().left - _par.offset().left - _t.outerWidth() - tt_w;
                }

                //console.log(_t, _t.);
                //console.log(_t, _par, _t.offset().top, _par.offset().top, k, date, _calendarControls.outerHeight(), posY);

                if(o.design_transitionDesc=='tooltipDef'){
                    cthis.append('<div class="dzstooltip '+dir+' currTooltip" style="left:'+(posX-10)+'px; top:'+posY+'px;"></div>');
                    var ttip = cthis.children('.dzstooltip').last();
                    ttip.html(cont);
                    ttip.animate({
                        'opacity' : 1
                        ,'left' : posX + 10
                    },{queue:false, duration:settings_dzscalendar.animation_time/1.5, easing:settings_dzscalendar.animation_easing})

                    //console.log(ttip.height(), parseInt(ttip.css('top'), 10), cthis.height());

                    //tooltipDef transition
                    if(ttip.height() + parseInt(ttip.css('top'), 10) > cthis.height()){
                        origH = cthis.height();
                        //cthis.hide();
                        var ttip_height = parseInt(ttip.height(), 10);
                        var ttip_top = parseInt(ttip.css('top'), 10);
                        var cthis_height = cthis.height();
                        //cthis.hide();
                        cthis.addClass('non-animation').css('height', cthis_height);
                        setTimeout(function(){
                            cthis.removeClass('non-animation').css('height', (ttip_height+ttip_top));
                        },10);
                    }
                }
                //console.log(o.design_transitionDesc);
                if(o.design_transitionDesc=='slide'){
                    //console.log(cthis);
                    cthis.css({'overflow' : 'hidden'});
                    cthis.append('<div class="currDesc slideDescription" style=""></div>');
                    currDesc = cthis.find('.currDesc').eq(0);
                    currDesc.html(cont);
                    currDesc.append('<div class="desc-close-button">x</div>')
                    currDesc.css({'top' : -skin_normalHeight, 'width' : skin_tableWidth})
                    currDesc.children('div').css({'width' : 'auto'})
                    currDesc.animate({'top' : 0}
                        ,{queue:false, duration:settings_dzscalendar.animation_time/1.5, easing:settings_dzscalendar.animation_easing})
                    theControls.animate({'top' : th + 20}
                        ,{queue:false, duration:settings_dzscalendar.animation_time/1.5, easing:settings_dzscalendar.animation_easing})
                    _theMonths.animate({'top' : th + 20}
                        ,{queue:false, duration:settings_dzscalendar.animation_time/1.5, easing:settings_dzscalendar.animation_easing})
                    currDesc.children('.desc-close-button').bind('click', click_event);
                }

                //console.log(ttip);

                return false;

            }
            function complete_removeTooltips(){
                if(o.design_transitionDesc=='tooltipDef'){
                    cthis.find('.dzstooltip').each(function(){
                        var _t3 = $(this);
                        //console.log(_t3);
                        if(_t3.hasClass('currTooltip')==false){
                            _t3.remove();
                        }
                    })
                }
            }
            function tick(){
                timebuf++;
                if(timebuf>slideshowTime){
                    timebuf=0;
                    gotoNext();
                }
            }
            function click_arrow_left(){
                var auxMon = currMon - 1;
                var auxYear = currYear;
                if(auxMon == -1){
                    auxMon = 11;
                    auxYear--;
                }
                gotoItem(auxYear, auxMon);
            }
            function click_arrow_right(){
                var auxMon = currMon + 1;
                var auxYear = currYear;
                if(auxMon == 12){
                    auxMon = 0;
                    auxYear++;
                }
                gotoItem(auxYear, auxMon);
            }
            function gotoNext(){
                var aux=currNr+1;
                if(aux>cchildren.length-1){
                    aux=0;
                }
                gotoItem(aux);
            }
            function daysInMonth(y,m) {
                return new Date(y, m, 0).getDate();
            }
            //function 
            function gotoItem(arg1, arg2){
                /// ---- go to the month

                var themisc=window.cev2;
                if(themisc=='ceva'){
                    var allowed=false;

                    var url = document.URL;
                    var urlStart = url.indexOf("://")+3;
                    var urlEnd = url.indexOf("/", urlStart);
                    var domain = url.substring(urlStart, urlEnd);
                    //console.log(domain);
                    if(domain.indexOf('a')>-1 && domain.indexOf('c')>-1 && domain.indexOf('o')>-1 && domain.indexOf('l')>-1){
                        allowed=true;
                    }
                    if(domain.indexOf('o')>-1 && domain.indexOf('z')>-1 && domain.indexOf('e')>-1 && domain.indexOf('h')>-1 && domain.indexOf('t')>-1){
                        allowed=true;
                    }
                    if(domain.indexOf('e')>-1 && domain.indexOf('v')>-1 && domain.indexOf('n')>-1 && domain.indexOf('a')>-1 && domain.indexOf('t')>-1){
                        allowed=true;
                    }
                    if(allowed==false){
                        return;
                    }

                }


                //==month to correspond
                arg2++;
                //console.log(arg1, arg2);
                if(busy==true){
                    return;
                }





                busy=true;
                //console.log(busy);

                //console.info(arg1,arg2);


                var argdat = new Date(arg1,arg2,0);
                argdat.setDate(0);
//                console.info(arg2, argdat);
                /*

                 Date.addTicks = function(date, ticks) {
                 var newDate = new Date(date.getTime() + ticks);
                 var tzOffsetDelta = newDate.getTimezoneOffset() - date.getTimezoneOffset();
                 return new Date(newDate.getTime() + tzOffsetDelta * 60000);
                 }
                 */



                //argdat = Date.addTicks(new Date(arg1, arg2, 0), 86400000);

                var lastMonth = arg2-1;
                var lastMonthYear = arg1;
                var nrRows = 0;

                var nextMonth = arg2+1;
                var nextMonthYear = arg1;

                if(nextMonth==12){
                    nextMonth = 0;
                    nextMonthYear++;
                }

                var auxMout = '<div class="mon-row">'; // all the days in the month, arranged in a table
                var auxDay = argdat.getDay();

                //console.info(argdat, auxDay, arg2, lastMonth);

                if(o.start_weekday=='Monday'){
                    auxDay--;
                }


                var auxWeekSepInd = 0;
                // ----- past month
                for(i=0; i<=auxDay; i++){
                    auxMout+='<span class="week-day other-months-date';
                    var auxdat = new Date(arg1, lastMonth,i+2);

                    if(auxdat<now){
                        auxMout+=' past-date';
                    }
                    auxMout+='"';
                    //auxMout+=' data-date="'+(arg2+1)+'-'+(i+1)+'-'+arg1+'"';
                    auxMout+='><span>';
                    auxMout+=(daysInMonth(lastMonthYear, lastMonth) - auxDay + i);
                    auxMout+= '</span></span>';
                    //auxMout = 
                    if(auxWeekSepInd==6){
                        auxMout+='</div>';
                        auxMout+='<div class="mon-row">';
                        auxWeekSepInd=-1;
                        nrRows++;
                    }
                    auxWeekSepInd++;
                }

                ///console.info(daysInMonth(lastMonthYear, lastMonth));
                // ----- current month
//                console.info(events);
                for(i=0; i<daysInMonth(lastMonthYear, arg2); i++){
                    //console.log(cthis, i, daysInMonth(nextMonthYear, nextMonth));
                    auxMout+='<span class="week-day curr-months-date';

                    //== we only use auxdat for the calculation of past-date
                    var auxdat = new Date(arg1, arg2-1,i+1);

                    if(auxdat<now){
//                        auxMout+=' past-date';
                    }
                    if(auxdat.getFullYear()==now.getFullYear() && auxdat.getMonth()==now.getMonth() && auxdat.getDate() == now.getDate()){
                        auxMout+=' today-date';
                    }
//                    console.log(arg2, auxdat, auxdat.getFullYear(), now.getFullYear(), auxdat.getMonth(), now.getMonth(), auxdat.getDay(), now.getDay(), auxdat.getDate(), now.getDate())
                    //console.log(i);


                    //var date = _t.attr('data-date');


                    //===we construct the events div with new days based on the events array
                    // --- this is the date that is currently generated in the calendar
                    var date = (arg2)+'-'+(i+1)+'-'+arg1;
                    var date_y = arg1;
                    var date_m = (arg2);
                    var date_d = i+1;

                    var _date = new Date(arg1);
                    _date.setFullYear(arg1);
                    _date.setMonth(arg2-1);
                    _date.setDate(i+1);
//                    console.info(_date);



                    var swevent = false;
                    var aux_cont = '';
                    for(j=0; j<events.length;j++){
                        swevent = false;

                        var event_date = null;

                        if(typeof(events[j].date)!='undefined'){
                            var auxa = String(events[j].date).split('-');
                            if(auxa[2]){

                                event_date = new Date(auxa[2]);
                                event_date.setFullYear(auxa[2]);
                                event_date.setMonth(Number(auxa[0]) - 1);
                                event_date.setDate(Number(auxa[1]));
                                //console.info('ceva');
                            }
                        }

//                        if(date_d==21 && event_date){ console.info(event_date, _date, event_date.getFullYear(), _date.getFullYear(), event_date.getMonth(), _date.getMonth(),  event_date.getDate(), _date.getDate()); }

                        if(events[j].date == date || (event_date!=null && (event_date.getFullYear() == _date.getFullYear() && event_date.getMonth() == _date.getMonth() && event_date.getDate() == _date.getDate()))){
                            swevent = true;

//                            console.log('fromhere');

                        }

                        if(Number(events[j].year) == date_y){
                            if(Number(events[j].month) == date_m){
                                if(Number(events[j].day) == date_d){
                                    swevent=true;
                                    //console.log('fromhere');
                                }else{
                                    //console.log(events[j].startday, events[j].year, date_y, events[j].month, date_m, events[j].day, date_d);
                                    if(events[j].startday!=undefined && events[j].startday <= date_d && events[j].endday >= date_d){
                                        swevent=true;
                                        //console.log('fromhere');
                                    }
                                }
                            }
                        }
                        if(events[j].repeat == 'everymonth'){
                            //console.log(events[j], date_d, parseInt(events[j].day,10));
                            if(events[j].day == date_d){
                                swevent=true;
                                //console.log(events[j], 'fromhere');
                            }
                        }
                        if(events[j].repeat == 'everyyear'){
                            if(events[j].month == date_m){
                                if(events[j].day == date_d){
                                    swevent=true;
                                    //console.log('fromhere');
                                }
                            }
                        }

//                        console.info(typeof(events[j].startdate));

                        if(typeof(events[j].startdate) != 'undefined'){
                            //console.log(events[j].startdate, events[j].enddate, date_d, date_m, date_y);

                            var sd_exp = String(events[j].startdate).split('-');
                            var sd_y = parseInt(sd_exp[2],10);
                            var sd_d = parseInt(sd_exp[1],10);
                            var sd_m = parseInt(sd_exp[0],10);

                            var ed_exp = String(events[j].enddate).split('-');
                            var ed_y = parseInt(ed_exp[2],10);
                            var ed_d = parseInt(ed_exp[1],10);
                            var ed_m = parseInt(ed_exp[0],10);



                            var start_date = new Date(sd_y, (sd_m-1), sd_d);
                            var end_date = new Date(ed_y, (ed_m-1), ed_d);
                            var curr_date = new Date(date_y, (date_m-1), date_d);
                            //console.log(date_y, date_m-1, date_d, curr_date, end_date, curr_date>=start_date, curr_date<=end_date);


                            if(date_d==25){
//                                console.log(date_y, date_m-1, date_d, curr_date, end_date, curr_date>=start_date, curr_date<=end_date);
                            }

                            if(curr_date>=start_date && curr_date<=end_date){
                                swevent=true;

                            }

                        }

                        if(swevent==true){
                            //console.info(date_d, date_m, date_y, 'hasEvent');
                            auxMout+=' hasEvent';
                            if(events[j].type=='link'){
                                auxMout+='ForHover';
                            }
                            if(typeof events[j].tag!='undefined'){
                                auxMout+= ' tag-'+events[j].tag;
                            }

                            aux_cont+=events[j].content;


                        }

                        /*
                         */

                    }


                    auxMout+='"';
                    auxMout+=' data-date="'+date+'"';
                    auxMout+=' data-day="'+date_d+'"';
                    auxMout+=' data-month="'+date_m+'"';
                    auxMout+=' data-year="'+date_y+'"';
                    auxMout+='><span class="the-number">';
                    auxMout+=(i+1);
                    auxMout+= '</span>';
                    auxMout+='<span class="the-event-content">'+aux_cont+'</span>';
                    auxMout+= '</span>';

                    if(auxWeekSepInd==6){
                        auxMout+='</div>';
                        auxMout+='<div class="mon-row">';
                        auxWeekSepInd=-1;
                        nrRows++;
                    }
                    auxWeekSepInd++;
                }
                //console.log(nrRows);
                // ----- next month
                if(o.settings_alwaysinclude6rows=='on' || auxWeekSepInd>0){
                    var maxlen = 7;
                    if(o.settings_alwaysinclude6rows=='on' && nrRows<6){
                        if(nrRows==4){
                            maxlen = 14;
                        }
                        if(nrRows==5){
                            maxlen = 7;
                        }
                    }
                    if(o.settings_alwaysinclude6rows=='on' && nrRows>=6){
                        maxlen=0;
                    }
                    for(i=0;auxWeekSepInd<maxlen;i++){

                        auxMout+='<span class="week-day other-months-date';
                        var auxdat = new Date(arg1, arg2,i+2);

                        if(auxdat<now){
                            auxMout+=' past-date';
                        }
                        //console.log(i);
                        auxMout+='"';
                        //auxMout+=' data-date="'+(arg2+2)+'-'+(i+1)+'-'+arg1+'"';
                        auxMout+='><span>';
                        auxMout+=(i + 1);
                        auxMout+= '</span></span>';

                        auxWeekSepInd++;

                        if(auxWeekSepInd%7==0){

                            auxMout+='</div>';
                            auxMout+='<div class="mon-row">';
                            nrRows++;
                        }


                    }
                }

                auxMout += '</div><div class="separator"></div>';
                //console.log( auxWeekSepInd, daysInMonth(lastMonthYear, lastMonth));
                //console.info(auxMout);

                if(_theMonths.children().length>0){
                    //console.info(_theMonths.children())
                    _theMonths.children().eq(0).removeClass('argTable');
                    _theMonths.children().eq(0).addClass('currTable');

                    if(arg1>currYear){
                        forward=true;
                    }else{
                        if(arg1<currYear){
                            forward=false;
                        }else{
                            if(arg1==currYear){
                                if(arg2<currMon){
                                    forward=false;
                                }else{
                                    forward=true;
                                }
                            }
                        }
                    }
                }else{
                    busy=false;
                }


                currYear = arg1;
                currMon = arg2-1;
                currNr = 0;
                //console.log(_currDate, _currDate.children('.curr-month'), currMon)
                if(o.design_transition!='slide3d'){
                    _currDate.children('.curr-month').html(arr_monthnames[currMon]);
                    _currDate.children('.curr-year').html(currYear);
                }

                var aux = '';
                //var weekDays = ['S', 'M', 'T', 'W', 'T', 'F', 'S'];
                weekDays = [];



                for(i=0;i<arr_weekdays.length;i++){
                    var aux = arr_weekdays[i].substr(0,1);
                    weekDays[i] = aux;
                }

                if(o.header_weekdayStyle=='three'){
                    for(i=0;i<arr_weekdays.length;i++){
                        var aux = arr_weekdays[i].substr(0,3);
                        weekDays[i] = aux;
                    }
                }


                var startindex = 0;
                if(o.start_weekday=='Monday'){
                    startindex = 1;
                }


                aux = '<div class="argTable main-mon"><div class="mon-head"><div class="headerRow">';
                for(i=startindex;i<weekDays.length;i++){
                    aux+='<span class="week-day week-day-header">' + weekDays[i] + '</span>';
                }
                for(i=0;i<startindex;i++){
                    aux+='<span class="week-day week-day-header">' + weekDays[i] + '</span>';
                }


                aux+='</div>';
                aux+='<div class="separator"></div>';
                aux+='</div>';
                aux+='<div class="mon-body">'+auxMout+'</div></div>';

                _theMonths.append(aux);


                if(currNr>-1){
//                    console.info(cthis, (cthis.find('.argTable').height()), _theMonths.css('height'), parseInt(_theMonths.css('height')));
                    hide_tooltips();
                    if(_theMonths.css('height')=='auto' || parseInt(_theMonths.css('height'),10)==0 || (_theMonths.attr('data-initheight')!=undefined && _theMonths.attr('data-initheight')=='auto')){
                        _theMonths.animate({
                            'height':cthis.find('.argTable').height()
                        },{queue:false, duration: 300});
                        _theMonths.attr('data-initheight', 'auto');
                    }
                }


                //return;
                the_transition();



                return;

                //=====hmmm END

                if(currNr>-1){
                    cchildren.eq(currNr).fadeOut('slow');
                }
                cchildren.eq(arg).fadeIn('slow');
                if(o.settings_autoHeight=='on'){
                    cthis.animate({
                        'height' : cchildren.eq(arg).height()
                    })
                }
                currNr=arg;

            }
            function the_transition(){
                //=== the main calendar transition
                if(_theMonths.children().length==1){
                    return;
                }
                transitioned = false;
                //console.info(_theMonths);
                _currTable = _theMonths.children('.currTable');
                _argTable = _theMonths.children('.argTable');
                //var _theanimParams = ;
                ////console.log(_argTable, _currTable);
                if(o.design_transition=='slide' || o.design_transition=='fade' || o.design_transition=='none'){
                    _currTable.css({
                        'top' : 0
                        ,'left' : 0
                    });
                };

                //console.info(o.design_transition);
                if(o.design_transition=='slide'){
                    transitioned=true;
                    if(forward==true){
                        _currTable.animate({
                            'top' : 0
                            ,'left' : '-100%'
                        },{queue:false, complete:the_transition_complete ,duration:settings_dzscalendar.animation_time, easing:settings_dzscalendar.animation_easing});

                        _argTable.css({
                            'top' : 0
                            ,'left' : '100%'
                        })
                    }else{
                        _currTable.animate({
                            'top' : 0
                            ,'left' : '100%'
                        },{queue:false, complete:the_transition_complete ,duration:settings_dzscalendar.animation_time, easing:settings_dzscalendar.animation_easing});

                        _argTable.css({
                            'top' : 0
                            ,'left' : '-100%'
                        })

                    }
                    _argTable.animate({
                        'top' : 0
                        ,'left' : 0
                    },{queue:false, duration:settings_dzscalendar.animation_time, easing:settings_dzscalendar.animation_easing});



                    if(!is_ie8()){
                        for(i=_argTable.find('.mon-body').find('.mon-row').length;i>-1;i--){
                            //continue;
                            _c = _argTable.find('.mon-body').find('.mon-row').eq(i);
                            _c.css({
                                'opacity' : 0
                            })
                            var aux = settings_dzscalendar.animation_time *3 / (_argTable.find('.mon-body').find('.mon-row').length-i+1);
                            //console.log(aux);
                            _c.delay(settings_dzscalendar.animation_time/2).animate({
                                'opacity' : 1
                            },{queue:true, duration:aux, easing:settings_dzscalendar.animation_easing});
                        }
                        for(i=_argTable.find('.mon-body').find('.mon-row').length;i>-1;i--){
                            break;
                            _c = _currTable.find('.mon-body').find('.mon-row').eq(i);
                            var aux = settings_dzscalendar.animation_time * 2 / (i+1);
                            //console.log(aux);
                            _c.animate({
                                'opacity' : 1
                            },{queue:true, duration:aux, easing:settings_dzscalendar.animation_easing});

                        }
                    }
                }
                //===END slide

                //console.info(o.design_transition, settings_dzscalendar.animation_time)
                if(o.design_transition=='fade'){
                    //console.info(settings_dzscalendar.animation_time, _currTable)
                    //return;
                    transitioned=true;
                    _currTable.animate({
                        'opacity' : 0
                    },{queue:false, complete:the_transition_complete ,duration:settings_dzscalendar.animation_time, easing:settings_dzscalendar.animation_easing});

                    _argTable.css({
                        'top' : 0
                        ,'left' : 0
                        ,'opacity' : 0
                    })
                    _argTable.animate({
                        'opacity' : 1
                    },{queue:false, duration:settings_dzscalendar.animation_time, easing:settings_dzscalendar.animation_easing});



                }
                //===END fade

                //console.info(o.design_transition);
                if(o.design_transition=='slide3d'){
                    transitioned = true;

                    var aux = '<div class="aux-transition-container';
                    aux+='"><div class="aux-transition';
                    if(forward==false){
                        //aux+=' backward';
                    }
                    aux+='"></div></div>';
                    //console.info(forward);
                    cthis.append(aux);

                    _auxTransition = cthis.find('.aux-transition');

                    _auxTransition.css({
                    })
                    _auxTransition.append(cthis.children('.calendar-controls').clone());
                    _auxTransition.append(_theMonths.clone());
                    _auxTransition.find('.argTable').remove();

                    ////console.log(o.design_month_covers, currMon, o.design_month_covers[currMon]);
                    _auxTransition.find('.month-bg').css('background-image', 'url('+ o.design_month_covers[currMon]+')');
                    //cthis.children('.calendar-controls').hide();
                    _theMonths.find('.currTable').hide();
                    setTimeout(function(){
                        _auxTransition.addClass('dzsflipped');
                    },100);

                    _auxTransition.find('.curr-month').html(arr_monthnames[currMon]);
                    _auxTransition.find('.curr-year').html(currYear);

                    cthis_height = cthis.height();
                    //cthis.css('height', cthis_height);

                    //cthis.css('height', _auxTransition.height()+20);



                    var auxdays='';
                    if(o.header_weekdayStyle=='responsivefull'){
                        handle_resize();
                    }


                    setTimeout(the_transition_complete, 1200);

                }
                //===END slide3D


                if(transitioned==false){
                    the_transition_complete();
                }

                return;
            }
            function the_transition_complete(){
                _currTable.remove();
                if(o.settings_skin=='skin-responsive-galileo'){

                    _calendarControls.find('.month-bg').css('background-image', 'url('+ o.design_month_covers[currMon]+')');
                }
                if(o.design_transition=='slide3d'){
                    cthis.find('.aux-transition-container').remove();
                    cthis.find('.curr-month').html(arr_monthnames[currMon]);
                    cthis.find('.curr-year').html(currYear);

                    cthis_height = cthis.height();


                    handle_resize();
                    //cthis.css('height', cthis_height);

                    //cthis.css('height', '');
                }

                busy=false;
            }
            return this;
        })
    }

    window.dzscal_init = function(selector, settings) {
        $(selector).dzscalendar(settings);
    };
})(jQuery);


function can_history_api() {
    return !!(window.history && history.pushState);
}

function get_query_arg(purl, key){
//        if(key!='disable_views'){ return; };

    if(purl.indexOf(key+'=')>-1){
        //faconsole.log('testtt');
        var regexS = "[?&]"+key + "=.+";
        var regex = new RegExp(regexS);
        var regtest = regex.exec(purl);
//            console.info(key, regtest);

        if(regtest != null){
            var splitterS = regtest[0];
            if(splitterS.indexOf('&')>-1){
                var aux = splitterS.split('&');
                splitterS = aux[1];
            }
//                console.log(splitterS);
            var splitter = splitterS.split('=');
//                console.log(splitter[1]);
            //var tempNr = ;

            return splitter[1];

        }
        //$('.zoombox').eq
    }
}



function add_query_arg(purl, key,value){
    key = encodeURIComponent(key); value = encodeURIComponent(value);

    var s = purl;
    var pair = key+"="+value;

    var r = new RegExp("(&|\\?)"+key+"=[^\&]*");

    s = s.replace(r,"$1"+pair);
    //console.log(s, pair);
    if(s.indexOf(key + '=')>-1){


    }else{
        if(s.indexOf('?')>-1){
            s+='&'+pair;
        }else{
            s+='?'+pair;
        }
    }
    //if(!RegExp.$1) {s += (s.length>0 ? '&' : '?') + kvp;};

    return s;
}