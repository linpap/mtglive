// JavaScript Document
$(document).ready(function() {
    $("#content").find("[id^='tab']").hide(); // Hide all content
    $("#tab li:first").attr("id","current"); // Activate the first tab
    $("#content #tab1").fadeIn(); // Show first tab's content
    
    $('#tab a').click(function(e) {
        e.preventDefault();
		
        if ($(this).closest("li").attr("id") == "current"){ //detection for current tab
         return;       
        }
        else{             
          $("#content").find("[id^='tab']").hide(); // Hide all content
          $("#tab li").attr("id",""); //Reset id's
          $(this).parent().attr("id","current"); // Activate this
          $('#' + $(this).attr('name')).fadeIn(); // Show content for the current tab
		  if($(this).attr('name')=='tab1'){
			  $("#BORDERTABS1 #tabs1").show();
			  $("#content1 #tab11").show();
			  $("#tabs1 li").attr("id","");
			  $("#tabs1 li:first").attr("id","current1");			 
		  }else if($(this).attr('name')=='tab2'){
			  $("#BORDERTABS2 #tabs2").show();
			  $("#content2 #tab21").show();
			  $("#tabs2 li").attr("id","");
			  $("#tabs2 li:first").attr("id","current2");
		  }else if($(this).attr('name')=='tab3'){
			  $("#BORDERTABS3 #tabs3").show();
			  $("#content3 #tab31").show();
			  $("#tabs3 li").attr("id","");
			  $("#tabs3 li:first").attr("id","current3");
		  }else if($(this).attr('name')=='tab4'){
			  $("#BORDERTABS4 #tabs4").show();
			  $("#content4 #tab41").show();
			  $("#tabs4 li").attr("id","");
			  $("#tabs4 li:first").attr("id","current4");
		  }else if($(this).attr('name')=='tab5'){
			  $("#BORDERTABS5 #tabs5").show();
			  $("#content5 #tab51").show();
			  $("#tabs5 li").attr("id","");
			  $("#tabs5 li:first").attr("id","current5");
		  }else if($(this).attr('name')=='tab6'){
			  $("#BORDERTABS6 #tabs6").show();
			  $("#content6 #tab61").show();
			  $("#tabs6 li").attr("id","");
			  $("#tabs6 li:first").attr("id","current6");
		  }
		  else if($(this).attr('name')=='tab7'){
			  
			  $("#BORDERTABS7 #tabs7").show();
			  $("#content7 #tab71").show();
			  $("#tabs7 li").attr("id","");
			  $("#tabs7 li:first").attr("id","current7");
			  
			  $("#BORDERTABS71 #tabs71").show();
			  $("#content71 #tab711").show();
			  $("#tabs71 li").attr("id","");
			  $("#tabs71 li:first").attr("id","current71");
		  }
        } 
		
    });
	$('#tab7 a').click(function(e) {
        e.preventDefault();
		
        if ($(this).closest("li").attr("id") == "current7"){ //detection for current tab
         return;       
        }
        else{    
         /* $("#content7").find("[id^='tab']").hide(); // Hide all content
          $("#tab7 li").attr("id",""); //Reset id's
          $(this).parent().attr("id","current7"); // Activate this
          $('#' + $(this).attr('name')).fadeIn(); // Show content for the current tab*/
		  
		  if($(this).attr('name')=='tab71'){
			  	  
			  $("#BORDERTABS71 #tabs71").show();
			  $("#content71 #tab711").show();
			  $("#tabs71 li").attr("id","");
			  $("#tabs71 li:first").attr("id","current71");
		  }
        } 
		
    });
	
	$("#content1").find("[id^='tab']").hide(); // Hide all content
    $("#tabs1 li:first").attr("id","current1"); // Activate the first tab
    $("#content1 #tab11").fadeIn(); // Show first tab's content
    
    $('#tabs1 a').click(function(e) {
        e.preventDefault();
        if ($(this).closest("li").attr("id") == "current1"){ //detection for current tab
         return;       
        }
        else{             
          $("#content1").find("[id^='tab']").hide(); // Hide all content
          $("#tabs1 li").attr("id",""); //Reset id's
          $(this).parent().attr("id","current1"); // Activate this
          $('#' + $(this).attr('name')).fadeIn(); // Show content for the current tab
        }
    });
	
	$("#content2").find("[id^='tab']").hide(); // Hide all content
    $("#tabs2 li:first").attr("id","current2"); // Activate the first tab
    $("#content2 #tab21").fadeIn(); // Show first tab's content
    
    $('#tabs2 a').click(function(e) {
        e.preventDefault();
        if ($(this).closest("li").attr("id") == "current2"){ //detection for current tab
         return;       
        }
        else{             
          $("#content2").find("[id^='tab']").hide(); // Hide all content
          $("#tabs2 li").attr("id",""); //Reset id's
          $(this).parent().attr("id","current2"); // Activate this
          $('#' + $(this).attr('name')).fadeIn(); // Show content for the current tab
        }
    });
	
	$("#content3").find("[id^='tab']").hide(); // Hide all content
    $("#tabs3 li:first").attr("id","current3"); // Activate the first tab
    $("#content3 #tab31").fadeIn(); // Show first tab's content
    
    $('#tabs3 a').click(function(e) {
        e.preventDefault();
        if ($(this).closest("li").attr("id") == "current3"){ //detection for current tab
         return;       
        }
        else{             
          $("#content3").find("[id^='tab']").hide(); // Hide all content
          $("#tabs3 li").attr("id",""); //Reset id's
          $(this).parent().attr("id","current3"); // Activate this
          $('#' + $(this).attr('name')).fadeIn(); // Show content for the current tab
        }
    });
	
	$("#content4").find("[id^='tab']").hide(); // Hide all content
    $("#tabs4 li:first").attr("id","current4"); // Activate the first tab
    $("#content4 #tab41").fadeIn(); // Show first tab's content
    
    $('#tabs4 a').click(function(e) {
        e.preventDefault();
        if ($(this).closest("li").attr("id") == "current4"){ //detection for current tab
         return;       
        }
        else{             
          $("#content4").find("[id^='tab']").hide(); // Hide all content
          $("#tabs4 li").attr("id",""); //Reset id's
          $(this).parent().attr("id","current4"); // Activate this
          $('#' + $(this).attr('name')).fadeIn(); // Show content for the current tab
        }
    });
	
	$("#content5").find("[id^='tab']").hide(); // Hide all content
    $("#tabs5 li:first").attr("id","current5"); // Activate the first tab
    $("#content5 #tab51").fadeIn(); // Show first tab's content
    
    $('#tabs5 a').click(function(e) {
        e.preventDefault();
        if ($(this).closest("li").attr("id") == "current5"){ //detection for current tab
         return;       
        }
        else{             
          $("#content5").find("[id^='tab']").hide(); // Hide all content
          $("#tabs5 li").attr("id",""); //Reset id's
          $(this).parent().attr("id","current5"); // Activate this
          $('#' + $(this).attr('name')).fadeIn(); // Show content for the current tab
        }
    });
	
	$("#content6").find("[id^='tab']").hide(); // Hide all content
    $("#tabs6 li:first").attr("id","current6"); // Activate the first tab
    $("#content6 #tab61").fadeIn(); // Show first tab's content
    
    $('#tabs6 a').click(function(e) {
        e.preventDefault();
        if ($(this).closest("li").attr("id") == "current6"){ //detection for current tab
         return;       
        }
        else{             
          $("#content6").find("[id^='tab']").hide(); // Hide all content
          $("#tabs6 li").attr("id",""); //Reset id's
          $(this).parent().attr("id","current6"); // Activate this
          $('#' + $(this).attr('name')).fadeIn(); // Show content for the current tab
        }
    });
	
	$("#content7").find("[id^='tab']").hide(); // Hide all content
    $("#tabs7 li:first").attr("id","current7"); // Activate the first tab
    $("#content7 #tab71").fadeIn(); // Show first tab's content
    
    $('#tabs7 a').click(function(e) {
        e.preventDefault();
        if ($(this).closest("li").attr("id") == "current7"){ //detection for current tab
         return;       
        }
        else{             
          $("#content7").find("[id^='tab']").hide(); // Hide all content
          $("#tabs7 li").attr("id",""); //Reset id's
          $(this).parent().attr("id","current7"); // Activate this
          $('#' + $(this).attr('name')).fadeIn(); // Show content for the current tab
		  
		  
          $('#tab711').fadeIn(); // Show content for the current tab
        }
    });
	
	$("#content71").find("[id^='tab']").hide(); // Hide all content
    $("#tabs71 li:first").attr("id","current71"); // Activate the first tab
    $("#content71 #tab711").fadeIn(); // Show first tab's content
    
    $('#tabs71 a').click(function(e) {
        e.preventDefault();
        if ($(this).closest("li").attr("id") == "current71"){ //detection for current tab
         return;       
        }
        else{      
          $("#content71").find("[id^='tab']").hide(); // Hide all content
          $("#tabs71 li").attr("id",""); //Reset id's
          $(this).parent().attr("id","current71"); // Activate this
          $('#' + $(this).attr('name')).fadeIn(); // Show content for the current tab
        }
    });
	
	
	$('#tabs1').show();
	$('#tabs2').show();
	$('#tabs3').show();
	$('#tabs4').show();
	$('#tabs5').show();
	$('#tabs6').show();
	$('#tabs7').show();
		
	
});