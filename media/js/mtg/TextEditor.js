Mtg.namespace('TextEditor');

Mtg.TextEditor = (function() {
    "use strict";

    var init = function() {
        $('body').on('keyup', '.textarea', function() {
            $(this).closest('.editor_wrapper').find('.wysiwyg').val($(this).html());
        });
    };


    var create = function(name) {
        var editor = $('#editor_' + name),
            textarea = editor.closest('.editor_wrapper').find('.textarea');

        editor.val(textarea.html());
        editor.show();
        textarea.hide();
        CKEDITOR.replace( 'editor_' + name);
    };

    var remove = function(name) {
        var editor = CKEDITOR.instances['editor_' + name],
            ed = $('#editor_' + name),
            textarea = ed.closest('.editor_wrapper').find('.textarea'),
            content;

        editor.updateElement();
        content = editor.getData();

        if(editor)  {
            editor.destroy(true);
        }

        ed.hide();
        textarea.html(content).show();
    };

    init();
    return {
        create : create,
        remove : remove
    };
}());