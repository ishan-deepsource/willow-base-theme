acf.add_action('append', function (el) {
  window.setTimeout(function(){ // Add slight delay to allow fields to render before init
    initMarkdownFields(el);
  }, 100)
});

acf.add_action('ready', function(){
  jQuery(".acf-actions > a[data-name='edit']").on('click', function (event) {
    window.setTimeout(function(){ // Add slight delay to allow fields to render before init
      initMarkdownFields(event.target);
    }, 1200)
  })
});

acf.add_action('ready', initMarkdownFields);

function initMarkdownFields(el) {
  jQuery(el).find('.acf-field-simple-mde').each(function () {
    if (jQuery(this).is(":visible")) { // Only render visible elements
      window.createSimpleMde(this, jQuery(this).data('simple-mde-config'));
      window.initCounters();
    }
  })
}

acf.add_action('show_field', initMarkdownFields);