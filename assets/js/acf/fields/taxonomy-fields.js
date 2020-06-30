(function () {
    function disableElements(selector) {
        document.querySelectorAll(selector).forEach(function (ele){
            ele.disabled = true;
        });
    }
    document.addEventListener("DOMContentLoaded", function(event) {
        // Disable fields on: Edit Tag
        disableElements('body.taxonomy-post_tag form[name=edittag] table.form-table:first-of-type input');
        disableElements('body.taxonomy-post_tag form[name=edittag] table.form-table:first-of-type textarea');
        disableElements('body.taxonomy-post_tag form[name=edittag] table.form-table:first-of-type select');

        // Disable fields on: Edit Category
        disableElements('body.taxonomy-category form[name=edittag] table.form-table:first-of-type input');
        disableElements('body.taxonomy-category form[name=edittag] table.form-table:first-of-type textarea');
        disableElements('body.taxonomy-category form[name=edittag] table.form-table:first-of-type select');
    });
})();
