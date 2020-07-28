(function() {
  function getParam(name) {
    var url = window.location.href;
    name = name.replace(/[\[\]]/g, "\\$&");
    var regex = new RegExp("[?&]" + name + "(=([^&#]*)|&|#|$)"),
      results = regex.exec(url);
    if (!results) return null;
    if (!results[2]) return '';
    return decodeURIComponent(results[2].replace(/\+/g, " "));
  }
  window.onload = function() {
    const focalPoint = new FocalPoint(
      document.querySelector('.wp_attachment_holder .wp_attachment_image img'),
      document.getElementById('attachments-' + getParam('post') + '-focal_point')
    );
    focalPoint.addToggleButton();
  }
})();
