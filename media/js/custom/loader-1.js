// JavaScript Document
jQuery(document).ready(function($){

        var design_month_covers = ['http://dummyimage.com/950x350/000000/3c3c3d.jpg','http://dummyimage.com/950x350/000000/3c3c3d.jpg','http://dummyimage.com/950x350/000000/3c3c3d.jpg','http://dummyimage.com/950x350/000000/3c3c3d.jpg','http://dummyimage.com/950x350/000000/3c3c3d.jpg','http://dummyimage.com/950x350/000000/3c3c3d.jpg','http://dummyimage.com/950x350/000000/3c3c3d.jpg','http://dummyimage.com/950x350/000000/3c3c3d.jpg','http://dummyimage.com/950x350/000000/3c3c3d.jpg','http://dummyimage.com/950x350/000000/3c3c3d.jpg','http://dummyimage.com/950x350/000000/3c3c3d.jpg','http://dummyimage.com/950x350/000000/3c3c3d.jpg'];


        dzscal_init("#tr1",{});
        dzscal_init("#tr2",{
            start_month: '8'
            ,start_year: '2014'
            ,start_weekday: 'Monday'
        });
        dzscal_init("#tr3",{
            design_transitionDesc: 'slide'
        });
        dzscal_init("#traurora",{
            design_transitionDesc: 'tooltipDef'
            ,design_transition: 'fade'
        });


        dzscal_init("#trauroradatepicker",{
            design_transitionDesc: 'tooltipDef'
            ,mode:'datepicker'
            ,header_weekdayStyle: 'three'
            ,design_transition: 'fade'
        });


        function dp1_event(arg){
            //console.log(arg);
            $('.event-receiver').html('clicked day: ' + arg);
        }

        var dp1 = document.getElementById('trauroradatepicker');
        if(dp1){

            dp1.arr_datepicker_events.push(dp1_event);
        }

        dzscal_init("#trresponsive",{
            header_weekdayStyle: 'three'
            ,design_transition: 'none'
        });


        dzscal_init("#cal-responsive-galileo2",{
            design_month_covers : design_month_covers
        });
        /*
         */
        dzscal_init("#cal-responsive-galileo",{
            design_month_covers : design_month_covers
            ,start_month: '8'
            ,start_year: '2014'
            ,start_weekday: 'Monday'
        });



    });