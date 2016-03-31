// JavaScript Document
$(document).ready(function(){
	
	$('.onSelectAction').change(function(){
        //TODO: horrible hack to add a confirmation box when adding "Løpende tiltak" to a "vaktrapport"
        if($(this).find(':selected').attr('id') === 'mto-shown-in-vaktrap') {
            if(!confirm('Ønsker du å vise løpende tiltak i vaktrapporten?')) {
                return false;
            }
        }

        var action = $(this).val();

        if(action.length>0)
		{
			var formId = '#'+$(this).data('form');
			$(formId).attr('action', action);
			$(formId).submit();
		}
	});
	// When check box whose id = checkbox_all is checked/unchecked all check box of that form are checked/unchecked
	$("#checkbox_all").click(function () {
		var wrapperId = '#'+$(this).data('form');
        if ($(wrapperId + " #checkbox_all").is(':checked')) {
            $(wrapperId + " input[type=checkbox]").each(function () {
                $(this).prop("checked", true);
            });

        } else {
            $(wrapperId + " input[type=checkbox]").each(function () {
                $(this).prop("checked", false);
            });
        }
    });
	// When check box whose id = checkbox_all1 is checked/unchecked all check box of that form are checked/unchecked
	$("#checkbox_all1").click(function () {
		var wrapperId = '#'+$(this).data('form');
        if ($(wrapperId + " #checkbox_all1").is(':checked')) {
            $(wrapperId + " input[type=checkbox]").each(function () {
                $(this).prop("checked", true);
            });

        } else {
            $(wrapperId + " input[type=checkbox]").each(function () {
                $(this).prop("checked", false);
            });
        }
    });
	// When check box whose id = checkbox_all2 is checked/unchecked all check box of that form are checked/unchecked
	$("#checkbox_all2").click(function () {
		var wrapperId = '#'+$(this).data('form');
        if ($(wrapperId + " #checkbox_all2").is(':checked')) {
            $(wrapperId + " input[type=checkbox]").each(function () {
                $(this).prop("checked", true);
            });

        } else {
            $(wrapperId + " input[type=checkbox]").each(function () {
                $(this).prop("checked", false);
            });
        }
    });
	// When check box whose id = checkbox_all2 is checked/unchecked all check box of that form are checked/unchecked
	$("#checkbox_all3").click(function () {
		var wrapperId = '#'+$(this).data('form');
        if ($(wrapperId + " #checkbox_all3").is(':checked')) {
            $(wrapperId + " input[type=checkbox]").each(function () {
                $(this).prop("checked", true);
            });

        } else {
            $(wrapperId + " input[type=checkbox]").each(function () {
                $(this).prop("checked", false);
            });
        }
    });
});