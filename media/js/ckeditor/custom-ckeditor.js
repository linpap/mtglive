$(document).ready(function(){
		$('.ckeditorClassSubmit').unbind().click(function(e, options) {
			
			 options = options || {};
			// Prevent the form being submitted just yet
			e.preventDefault();
			var editors = $("textarea.ckeditorClass");
			if (editors.length) {
				editors.each(function() {
					/*var instanceName =  $(this).attr("data-name");
					var instance = CKEDITOR.instances[instanceName];
					if (instance) 
					{ 
						$('#editorcontents_'+instanceName).css('display','block');
						$('#editorcontents_'+instanceName).val($(this).val(instance.getData())); 
						$('#contents_'+name).css('display','block');
					}*/
					var name = $(this).attr('data-name');
					if(name != null && name !== undefined && name!='')
					{
						var editorExtra = $(this).attr('data-instance');
						if (editorExtra!= null && editorExtra!== undefined && editorExtra!='') 
						{
							$('#editorcontents_'+name).val(CKEDITOR.instances[editorExtra].getData()); 
							CKEDITOR.remove(editorExtra);	
							$('#editor_'+name).html('');
							$('#contents_'+name).css('display','block');
							$(this).css('display','block');
							$(this).attr('data-instance','');
						}
					}
					else
					{
						return;
					}
				});
			}
			 $('#'+$('.ckeditorClassSubmit').attr('data-form')).submit();
		});
	});