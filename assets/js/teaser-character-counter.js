(function () {
  function getSelectors() {
    const selectors = [
      '.acf-field-58e38d86194e2', // SELECTING TEASER TITLE FIELD
      '.acf-field-58e38dd0194e4', // SELECTING TEASER DESCRIPTION FIELD
      '.acf-field-5aeac749faaf2', // SELECTING SEO TITLE FIELD
      '.acf-field-5aeac79e8d9d0', // SELECTING SEO DESCRIPTION FIELD
      '.acf-field-5aeac8356eb58', // SELECTING FACEBOOK TITLE FIELD
      '.acf-field-5aeac8546eb5a', // SELECTING FACEBOOK DESCRIPTION FIELD
      '.acf-field-5aeac87839538', // SELECTING TWITTER TITLE FIELD
      '.acf-field-5aeac88c3953a', // SELECTING TWITTER DESCRIPTION FIELD
    ];

    return selectors.join();
  }

  function addCountersAndEventListeners(el) {
    const identifier = 'acf-' + jQuery(el).data('key').replace('_', '-');
    const inputId = '#acf-' + jQuery(el).closest('.acf-field').data('key');
    const initialCharacterCount = jQuery(inputId).val().length ? jQuery(inputId).val().length : 0;
    // PREVENT COUNTER TO BE ADDED DOUBLE TO TEXTAREAS FOR SOME WEIRD REASON
    if (!jQuery(el).next().hasClass(identifier + '-character-counters')) {
      jQuery('<div class="' + identifier + '-character-counters teaser-counter" style="float: right;">Initial Characters: <span class="initial-character-counter" style="margin-right: 5px;">' + initialCharacterCount + '</span>Characters: <span class="character-counter">' + initialCharacterCount + '</span></div>').insertBefore(jQuery(el).find('.acf-label'));
    }
    el.addEventListener('keyup', countCharacters);
  }

  function initCounters() {
    const textInputs = jQuery(getSelectors()); 
    textInputs.each(function(index, el) {
      addCountersAndEventListeners(el);
    });
  }

  function countCharacters(e) {
    let el = e.target;
    const inputId = '#acf-' + jQuery(el).closest('.acf-field').data('key');
    jQuery(el).closest('.acf-field').find('.teaser-counter .character-counter').get(0).innerHTML = jQuery(inputId).val().length;
    
  }

  acf.add_action('ready', initCounters);
}
)();