(function ($) {
    function disableElements(selector) {
        $(selector).attr('readonly', 'readonly');
    }
    function disableSelectOptions() {
        $('#select-edit-term-language #term_lang_choice option:not(:selected)').each(function (index) {
            $(this).prop('disabled', true);
        });
        $('.term-parent-wrap #parent option:not(:selected)').each(function (index) {
            $(this).prop('disabled', true);
        });
    }
    document.addEventListener("DOMContentLoaded", function(event) {
        // Disable fields on: Edit Tag
        disableElements('body.taxonomy-post_tag form[name=edittag] table.form-table:first-of-type input');
        disableElements('body.taxonomy-post_tag form[name=edittag] table.form-table:first-of-type textarea');
        // Disable fields on: Edit Category
        disableElements('body.taxonomy-category form[name=edittag] table.form-table:first-of-type input');
        disableElements('body.taxonomy-category form[name=edittag] table.form-table:first-of-type textarea');
        disableSelectOptions();
    });
})(jQuery);