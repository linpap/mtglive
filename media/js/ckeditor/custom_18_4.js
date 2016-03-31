var editor, html = '';
	CKEDITOR.config.autoParagraph = false;
	function createEditor(name) {
		var instanceName =  $('#editorcontents_'+name).attr('data-instance');
		if(instanceName != null && instanceName !== undefined && instanceName=='')
		{
			var editors = $("textarea.ckeditorClass");
	
			if (editors.length) {
				editors.each(function() {
					var name = $(this).attr('data-name');
					if(name != null && name !== undefined && name!='')
					{
						var editorExtra = $(this).attr('data-instance');
						if (editorExtra!= null && editorExtra!== undefined && editorExtra!='') 
						{
							$('#editorcontents_'+name).val($(this).val(CKEDITOR.instances[name].getData())); 
							CKEDITOR.remove(editorExtra);
							$('#editor_'+name).html('');
							$('#contents_'+name).css('display','block');
							$(this).css('display','block');
							$(this).attr('data-instance','');
						}
					}
					else if(name!='')
					{
						$(this).attr('data-instance','');
					}
				});
			}
			
			/*for(var i in CKEDITOR.instances) {
				alert(i);
				alert($(this).attr('data-instance'));
			}*/

			if (!instanceName)
			{
				// Create a new editor inside the <div id="editor">, setting its value to html
				var config = {};
				html = $('#editorcontents_'+name).val();
				editor = CKEDITOR.appendTo( 'editor_'+name, config, html );
				var test = editor.name;
				$('#contents_'+name).css('display','none');
				$('#editorcontents_'+name).css('display','none');
				$('#editorcontents_'+name).attr('data-instance',test);
			}
		}
	}

	function removeEditor(name) {
		

		// Retrieve the editor contents. In an Ajax application, this data would be
		// sent to the server or used in any other way.
		if($('#editorcontents_'+name).attr('data-instance'))
		{	
			var editor = $('#editorcontents_'+name).attr('data-instance');
			$('#editorcontents_'+name).val(CKEDITOR.instances[editor].getData());
			CKEDITOR.remove(editor);
			$('#editor_'+name).html('');
			$('#contents_'+name).css('display','block');
			$('#editorcontents_'+name).css('display','block');
			$('#editorcontents_'+name).attr('data-instance','')
			
		}
	}
	
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