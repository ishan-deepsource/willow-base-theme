(function () {
    function disableElements(selector) {
        $(selector).attr('readonly', 'readonly');
    }
    function disableSelectOptions(selector) {
        document.querySelectorAll(selector).forEach(function (ele){
            ele.find($('option:not(:selected)').attr('disabled', true));
        });
    }
    document.addEventListener("DOMContentLoaded", function(event) {
        // Disable fields on: Edit Tag
        disableElements('body.taxonomy-post_tag form[name=edittag] table.form-table:first-of-type input');
        disableElements('body.taxonomy-post_tag form[name=edittag] table.form-table:first-of-type textarea');
        disableSelectOptions('body.taxonomy-post_tag form[name=edittag] table.form-table:first-of-type select');

        // Disable fields on: Edit Category
        disableElements('body.taxonomy-category form[name=edittag] table.form-table:first-of-type input');
        disableElements('body.taxonomy-category form[name=edittag] table.form-table:first-of-type textarea');
        disableSelectOptions('body.taxonomy-category form[name=edittag] table.form-table:first-of-type select');
    });
})();