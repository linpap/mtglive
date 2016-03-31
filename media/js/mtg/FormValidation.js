Mtg.namespace('FormValidation');

Mtg.FormValidation = (function() {
    "use strict";

    var isErrorPopupShowing = false;

    var errors = [];

    var forms = [
        '#registration',
        '#userLogin',
        '#frmNewDepartment',
        '#frmUpdateDepartment',
        '#frmNewClient',
        '#frmUpdateClient',
        '#frmNewUser',
        '#frmUpdateUser',
        '#frmUpdateUserExpertise',
        '#frmUpdateUserEducation',
        '#frmUpdateUserRelatives',
        '#frmClientAddMaal',
        '#frmClientAddFeaTiltak',
        '#frmClientAddInsTiltak',
        '#frmClientAddGovTiltak',
        '#frmClientAddObservation',
        '#frmNewClientVaktrapport',
        '#frmClientEditMaal',
        '#frmEditClientVaktrapport',
        '#frmClientLogg',
        '#frmClientForce',
        '#frmClientMedicinePlan',
        '#frmClientAddPlacement',
        '#frmClientAddNetwork',
        '#frmInfoAdd',
        '#frmEditMapping',
        '#frmSearch',
        '#frmSystemConfig',
        '#frmEditClientScoreCard'
    ];

    var config = {
        form : forms.join(),
        validateOnBlur : false,
        borderColorOnError : '#C90312',
        addValidClassOnAll : true,
        showHelpOnFocus : false,
        addSuggestions : false,
        errorMessagePosition : 'top',
        ignore: '.ignore',
        scrollToTopOnError : false,
        onElementValidate : function(valid, $el, $form, errorMess) {
            if( !valid ) {
                Mtg.FormValidation.errors.push({el: $el, error: $el.attr('data-error-message') });
            }
        },
        onError : function(form) {
           var html = '<ul>';

            $.each(Mtg.FormValidation.errors, function(i, el) {
                if(typeof el.error !== typeof undefined) {
                    html += '<li>' + el.error + '</li>';
                }
            });

            html += '</ul>';
            Mtg.FormValidation.errors = [];

            if(!isErrorPopupShowing) {
                Mtg.Dialog.display(html, 'Feil!', 300, 600, function() {
                    isErrorPopupShowing = false;
                });
                isErrorPopupShowing = true;
            }
        }
    };

    var init = function() {
        $.validate(config);
    };

    var validate = function(form) {
        form = $(form);
        console.log('motof', Mtg.FormValidation.errors);
        if( !$(form).isValid(config, {}, false) ) {
            console.log(Mtg.FormValidation.errors);
        }

    };

    init();
    return {
        validate : validate,
        errors : errors
    };
}());