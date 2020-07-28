(function ($) {

  var activeHotspot = null;

  acf.add_action('remove', function (el) {
    if($.contains(el, '.edit-hotspot-image') && activeHotspot instanceof FocalPoint) {
      activeHotspot.destroy();
      activeHotspot = null;
    }
  });

  acf.add_action('append', function (el) {
    attachClickEventToButtons(el);
  });

  acf.add_action('ready', function (el) {
    attachClickEventToButtons(el);
  });

  function attachClickEventToButtons(el) {
    $(el).find('.edit-hotspot-image').each(function () {
      if ($(this).is(":visible")) { // Only render visible elements
        $(this).click(function(e) {
          toggleFocalPoint(this);
        })
      }
    })
  }

  function toggleFocalPoint(editBtn) {
    var image = $(editBtn).parents('div.layout').find('img');
    var input = $(editBtn).parent().find('input[type="hidden"]');

    if(image.attr('src') === 'undefined') {
      alert('You must select an image first');
      return;
    }

    if(activeHotspot instanceof FocalPoint) {
      activeHotspot.destroy();
      activeHotspot = null;
    }

    activeHotspot = new FocalPoint(image[0], input[0]);
    activeHotspot.displayCrosshair();
  }
})(jQuery);
