(function () {
  document.addEventListener("DOMContentLoaded", initMarkdownFields);

  function initMarkdownFields() {
    const descriptionFields = jQuery('textarea[name^="description_"]').css('background-color', 'green');
    descriptionFields.each(function(index, element) {
      window.createSimpleMde(element);
    });
  }
})();