(function () {
  function parseLink(plaintext) {
    return plaintext.replace(/\[(.*)\]\((.*) (.*)\)/, function(fullMatch, text, link, attributeData) {
      var attributes = JSON.parse(attributeData);
      var attr = '';
      Object.keys(attributes).forEach(function (key) {
        attr += `${key}="${attributes[key]}" `;
      });
      return`<a href="${link}" ${attr}>${text}</a>`;
    });
  }

  function parseCite(plaintext) {
    return plaintext.replace(/(^|[^~])(?:~([^~]+)~)/, '$1<cite>$2</cite>');
  }

  function previewContent(plaintext) {
    marked.setOptions({
      gfm: true,
    });
    return marked(parseCite(parseLink(plaintext)));
  }

  function createSimpleMde(textArea, options) {
    if (jQuery(textArea).hasClass('simple-mde-instantiated')) {
      return;
    }
    let countTimeout;
    var mdeOptions = {
      element: textArea,
      previewRender: previewContent,
      spellChecker: true,
      forceSync: true,
      status: ["autosave", "lines", "words", {
        className: "keystrokes",
        defaultValue: function(el, codeMirror) {
          this.charCount = characterCount(codeMirror.getValue());
          el.innerHTML = "Characters: <span class='composite-character-counter' style='min-width:0;margin-left:0'>0</span>";
          setTimeout(function() {
            sumUpAllFields();
          }, 5000);
        },
        onUpdate: function(el, codeMirror) {
          window.clearTimeout(countTimeout)
          countTimeout = window.setTimeout(() => {
            this.charCount = characterCount(codeMirror.getValue());
            el.innerHTML = "Characters: <span class='composite-character-counter' style='min-width:0;margin-left:0'>" + this.charCount + "</span>";
            if (!firstRun) {
              sumUpAllFields();
            }
          }, 1000);
        }
      },
      {
        className: "keystrokes",
        defaultValue: function(el, codeMirror) {
            this.initialCharCount = characterCount(codeMirror.getValue());
            el.innerHTML = "Initial Characters: <span class='initial-counter' style='min-width:0;margin-left:0'>0</span>";
            
        },
        onUpdate: function(el, codeMirror) {
            el.innerHTML = "Initial Characters: <span class='initial-counter' style='min-width:0;margin-left:0'>" + this.initialCharCount + "</span>";
        }
      }], // Another optional usage, with a custom status bar item that counts keystrokes
    };
    if (typeof dictionary !== "undefined") {
      mdeOptions.dictionary = dictionary;
    }
    var toolbar = {
      toolbar: [
        "bold",
        "italic",
        "heading-2",
        "heading-3",
        "|",
        "unordered-list",
        "ordered-list",
        {
          name: "link",
          action: createLinkModal,
          className: "fa fa-link",
          title: "Create Link"
        },
        "|",
        "preview",
        "guide"
      ]
    };

    if ('simple' === options) {
      toolbar = {
        toolbar: [
          "bold",
          "italic",
          "|",
          "unordered-list",
          "ordered-list",
          {
            name: "link",
            action: createLinkModal,
            className: "fa fa-link",
            title: "Create Link"
          },
          "|",
          "preview",
          "guide"
        ]
      };
    }
    const smde = new SimpleMDE(Object.assign(mdeOptions, toolbar));
    jQuery(textArea).addClass('simple-mde-instantiated');
    smde.codemirror.on('blur', function() {
      jQuery(textArea).trigger('change');
    });
    const widgetType = jQuery(textArea).closest('.layout').data('layout');
    if (widgetType === 'text_item') {
      const elements = jQuery(textArea).closest('.layout').find('.keystrokes');
      elements.each(function(index, element) {
        jQuery(element).addClass('composite-body-text-counter');
      });
    }
  };

  window.createSimpleMde = createSimpleMde;

  let isRunning = false;
  let firstRun = true;
  let windowReady = false;
  function sumUpAllFields() {
    // PREVENTING FUNCTION TO RUN MORE THAN ONCE, AND NOT FOR EVERY FIELD INSTATIATED
    if (!isRunning) {
      isRunning = true;
      // LIVE UPDATING THE CHARACTER COUNT
      let total = 0;
      const elements = document.querySelectorAll('.composite-character-counter');
      elements.forEach(function(item) {
        const value = item.innerHTML;
        total += parseInt(value);
      });
      document.getElementById('wp-admin-bar-character-count').innerHTML = "<span style='margin: 0 10px; font-weight: 700; text-decoration: underline;'>Characters: " + total + "</span>";

      // LIVE UPDATING THE BODY TEXT CHARACTER COUNT
      let bodyTextTotal = 0;
      const bodyTextElements = document.querySelectorAll('.composite-body-text-counter .composite-character-counter');
      bodyTextElements.forEach(function(item) {
        const value = item.innerHTML;
        bodyTextTotal += parseInt(value);
      });
      document.getElementById('wp-admin-bar-body-text-count').innerHTML = "<span style='margin: 0 10px; font-weight: 700; text-decoration: underline;'>Body Text Count: " + bodyTextTotal + "</span>";
      
      if (firstRun) {
        // SETTING THE INITIAL CHARACTER COUNT ONCE ON PAGE LOAD
        document.getElementById('wp-admin-bar-initial-character-count').innerHTML = "<span style='margin: 0 10px; font-weight: 700; text-decoration: underline;'>Initial Characters: " + total + "</span>";

        // SETTING THE INITIAL BODY TEXT CHARACTER COUNT ONCE ON PAGE LOAD
        document.getElementById('wp-admin-bar-initial-body-text-count').innerHTML = "<span style='margin: 0 10px; font-weight: 700; text-decoration: underline;'>Initial Body Text Count: " + bodyTextTotal + "</span>";
        firstRun = false;
      }
      isRunning = false;
    }
  }

  function createLinkModal(editor) {
    var cm = editor.codemirror;
    var selectedText = cm.getSelection();
    var text = '';
    var url = '';
    var title = '';
    var target = 'off';
    var nofollow = 'off';
    if(selectedText) {
      var markdownMatch = selectedText.match(/\[(.*)\]\(([^\s]+) ?(.*)?\)/);
      if(markdownMatch) {
        text = markdownMatch[1];
        url = markdownMatch[2];
        if(markdownMatch[3]) {
          var attrs = JSON.parse(markdownMatch[3]);
          if(attrs.hasOwnProperty('title')) {
            title = attrs.title;
          }
          if(attrs.hasOwnProperty('target')) {
            target = 'on';
          }
          if(attrs.hasOwnProperty('rel')) {
            nofollow = 'on';
          }
        }
      } else {
        text = selectedText;
      }
    }
    var modalContainer = jQuery(document.createElement('div'));
    modalContainer.css({
      'position': 'fixed',
      'top': 0,
      'left': 0,
      'right': 0,
      'bottom': 0,
      'min-height': '360px',
      'background': 'rgba(0,0,0,0.7)',
      'z-index': '160000'
    });
    var modal = jQuery(document.createElement('div'));
    modalContainer.append(modal);
    modal.css({
      'width': '500px',
      'position': 'absolute',
      'top': '20%',
      'left': '50%',
      'margin-left': '-250px',
      'background': '#fcfcfc',
      '-webkit-box-shadow': '0 5px 15px rgba(0,0,0,0.7)',
      'box-shadow': '0 5px 15px rgba(0,0,0,0.7)',
      'padding': '10px 20px'
    });
    modal.append(`
    <h3 style="text-center">Insert link</h3>
    <hr />
    <table class="form-table">
      <tbody>
        <tr>
            <td><label>Link text*</label></td>
            <td><input type="text" id="simpleMDE-link-text" style="width: 100%;" /></td>
        </tr>
        <tr>
            <td><label>Link URL*</label></td>
            <td><input type="text" id="simpleMDE-link-url" style="width: 100%;" /></td>
        </tr>
        <tr>
            <td><label>Link title</label></td>
            <td><input type="text" id="simpleMDE-link-title" style="width: 100%;" /></td>
        </tr>
        <tr>
            <td><label>Link target</label></td>
            <td>
                <fieldset>
                    <label>
                        <input type="radio" checked name="simpleMDE-link-target" value="off" /> Same window
                    </label>
                    <br />
                    <label>
                        <input type="radio" name="simpleMDE-link-target" value="on" /> New window
                    </label>
                </fieldset>
            </td>
        </tr>
        <tr>
            <td><label>Link REL</label></td>
            <td>
                <fieldset>
                    <label>
                        <input type="radio" name="simpleMDE-link-nofollow" value="off" /> Normal link
                    </label>
                    <br />
                    <label>
                        <input type="radio" checked name="simpleMDE-link-nofollow" value="on" /> Nofollow link
                    </label>
                </fieldset>
            </td>
        </tr>
      </tbody>
    </table>
    <hr />
    `);
    var btn = jQuery(document.createElement('button'));
    btn.addClass('button button-primary button-large').css({
      'float': 'right',
      'clear': 'both',
    }).text('Update');
    modal.append(btn);
    btn.click(function(){
      var output = '[';
      output += jQuery('#simpleMDE-link-text').val();
      output += '](';
      output += jQuery('#simpleMDE-link-url').val().replace(/\(|\)/g, (char) => {
        return char === '(' ? '%28' : '%29';
      });
      var attributes = {};
      var title = jQuery('#simpleMDE-link-title').val();
      if(title) {
        attributes.title = title;
      }
      if(jQuery('input[name="simpleMDE-link-target"]:checked').val() === 'on') {
        attributes.target = '_blank';
      }
      if(jQuery('input[name="simpleMDE-link-nofollow"]:checked').val() === 'on') {
        attributes.rel = 'nofollow';
      } else {
        attributes.rel = 'follow';
      }
      if(Object.keys(attributes).length !== 0) {
        output += ' ' + JSON.stringify(attributes);
      }
      output += ')';
      cm.replaceSelection(output);
      jQuery(cm.getTextArea()).trigger('change');
      modalContainer.remove();
    });
    jQuery(document).keyup(function(e) {
      if (e.keyCode === 27){
        modalContainer.remove();
      }
    });
    jQuery('body').append(modalContainer);
    jQuery('#simpleMDE-link-text').val(text);
    jQuery('#simpleMDE-link-url').val(url);
    jQuery('#simpleMDE-link-title').val(title);
    jQuery.each(jQuery('input[name="simpleMDE-link-target"]'), function() {
      jQuery(this).prop("checked", jQuery(this).val() === target);
    });
    jQuery.each(jQuery('input[name="simpleMDE-link-nofollow"]'), function() {
      jQuery(this).prop("checked", jQuery(this).val() === nofollow);
    });
  }

  function characterCount(string) {
    if (string.length === undefined) {
        return 0;
    }
    return string.replace(/\[.*]\(.*\)/g, '')
      .replace( /^[#]+[ ]+(.*)$/gm, '$1')
      .replace( /\*\*?(.*?)\*?\*/gm, '$1')
      .length;
  }
})();
