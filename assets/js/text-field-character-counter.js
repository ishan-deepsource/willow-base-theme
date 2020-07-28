(function () {
  function getSelectors() {
    const selectors = [
      '#title', // SELECTING THE ARTICLE TITLE FIELD
      '#acf-field_58abfebd21b82', // SELECTING THE ARTICLE DESCRIPTION FIELD
      '.acf-input input[type="text"]:not([disabled])', // FILTERING OUT FIELDS LIKE FOCALPOINT ETC.
      '.acf-input textarea:not(.acf-field-simple-mde)' // FILTERING OUT THE HIDDEN TEXTAREAS CONNECTED TO THE MARKDOWN FIELDS
    ];

    return selectors.join();
  }

  function addCountersAndEventListeners(el) {
    const initialCharacterCount = el.value ? characterCount(el.value) : 0;
    // PREVENT COUNTER TO BE ADDED DOUBLE TO TEXTAREAS FOR SOME WEIRD REASON
    if (!jQuery(el).next().hasClass('composite-character-counters')) {
      jQuery('<div class="composite-character-counters" style="text-align: right;">Initial Characters: <span class="composite-initial-character-counter" style="margin-right: 5px;">' + initialCharacterCount + '</span>Characters: <span class="composite-character-counter">' + initialCharacterCount + '</span></div>').insertAfter(el);
    }
    el.addEventListener('keyup', countCharacters);
    // TODO Attach eventListener for keyup on the specfic input on focus and remove on blur to save resources
    // el.addEventListener('focus', addKeyUpListener)
    // el.addEventListener('blur', addKeyUpListener)
  }

  function initCounters() {
    const classes = document.body.classList;
    if (!whitelistedContentTypes(classes)) {
      return;
    }

    windowReady = true;
    const widgets = jQuery('.acf-field-flexible-content .layout:not(.acf-clone)');
    widgets.each(function (index, widget) {
      const type = jQuery(widget).data('layout');
      if (includedWidgetType(type)) {
        const textInputs = jQuery(widget).find(getSelectors());
        textInputs.each(function (index, el) {
          addCountersAndEventListeners(el);
        });
      }
    })
    // ADDING TITLE AND DESCRIPTION SEPARATELY
    addCountersAndEventListeners(jQuery('#title').get(0));
    addCountersAndEventListeners(jQuery('#acf-field_58abfebd21b82').get(0));
  }

  window.initCounters = initCounters;
  let isRunning = false;
  function sumUpAllNonMarkdownFields() {
    if (isRunning) {
      return;
    }
    isRunning = true;
    const compositeCounters = jQuery('.composite-character-counter');
    let total = 0;
    compositeCounters.each(function (index, item) {
      const value = item.innerHTML;
      total += parseInt(value);
    })
    document.getElementById('wp-admin-bar-character-count').innerHTML = "<span style='margin: 0 10px;'>Characters: " + total + "</span>";
    isRunning = false;
  }

  function countCharacters(e) {
    let countTimeout;
    let el = e.target;
    jQuery(el).next().find('.composite-character-counter').get(0).innerHTML = el.value.length;

    window.clearTimeout(countTimeout);
    countTimeout = window.setTimeout(() => {
      sumUpAllNonMarkdownFields();
    }, 2000);
  }

  acf.addAction('append', function ($el) {
    const type = $el.data('layout');
    const itemClass = $el.attr('class');
    if (includedWidgetType(type) || itemClass === 'acf-row') {
      const textInputs = $el.find(getSelectors());
      textInputs.each(function (index, el) {
        addCountersAndEventListeners(el);
      })
    }
  });

  function characterCount(string) {
    if (string.length === undefined) {
      return 0;
    }
    return string.replace(/\[.*]\(.*\)/g, '')
      .replace(/^[#]+[ ]+(.*)$/gm, '$1')
      .replace(/\*\*?(.*?)\*?\*/gm, '$1')
      .length;
  }

  function includedWidgetType(type) {
    const includedWidgetTypes = [
      'text',
      'image',
      'gallery',
      'infobox',
      'lead_paragraph',
      'paragraph_list',
      'hotspot_image',
      'quote',
    ];

    return includedWidgetTypes.includes(type);
  }

  function whitelistedContentTypes(types) {
    const whitelist = [
      'post-type-contenthub_composite'
    ];

    let containsType = false;
    types.forEach(function (type) {
      if (whitelist.includes(type)) {
        containsType = true;
      }
    });

    return containsType;
  }
}
)();