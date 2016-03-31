var editor, html = '';
	CKEDITOR.config.autoParagraph = false;
	/**function createEditor(name) {
        CKEDITOR.replace( 'editor_' + name);
	}

	function removeEditor(name) {
        var editor = CKEDITOR.instances['editor_' + name],
            content;

        editor.updateElement();
        content = editor.getData();

        if(editor)  {
            editor.destroy(true);
        }
	} **/

	$(document).ready(function(){
		$('.ckeditorClassSubmit').unbind().click(function(e, options) {
			 options = options || {};
			// Prevent the form being submitted just yet
			e.preventDefault();
			var editors = $("textarea.ckeditorClass");
			if (editors.length) {
				editors.each(function() {
					var name = $(this).attr('data-name');
					if(name != null && name !== undefined && name!='')
					{
						var editorExtra = $(this).attr('data-instance');
						if (editorExtra!= null && editorExtra!== undefined && editorExtra!='') 
						{
							$('#editor_'+name).val(CKEDITOR.instances[editorExtra].getData());
							var areaValue = '';
							areaValue = CKEDITOR.instances[editorExtra].getData();
							CKEDITOR.remove(editorExtra);	
							$('#editor_'+name).html(areaValue);
							//$('#contents_'+name).css('display','block');
							//$(this).css('display','block');
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