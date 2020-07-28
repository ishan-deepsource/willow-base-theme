acf.add_filter('validation_complete', function( json, $form ){

    $ = jQuery;

    // flexible content class acf-field-58aae476809c6
    // make sure the user hasn't checked more than one 'Lead Image'
    var leadImageCheckboxesChecked = $(".acf-field-58aae476809c6 .acf-flexible-content .values .acf-fields [data-name='lead_image'] input:checkbox:checked");

    if(leadImageCheckboxesChecked.length > 1){
        // create an error message for the abundant Lead Images (but not the first one)
        leadImageCheckboxesChecked.slice(1).each(function() {
            var leadImageError = {input: $(this).attr('name'), message: "Please make sure you only select 1 Lead image!"};

            if(typeof json.errors.length === 'undefined') {
                json.errors = [];
            }

            json.errors.push(leadImageError);
            // invalidate the form
            json.valid = 0;
        });
    }

    // select all Video-widgets with "Teaser Image" checked
    var videoTeaserImage = $(".acf-field-58aae476809c6 .acf-flexible-content .values .acf-fields [data-name='video_teaser_image'] input:checkbox:checked");
    var teaserImage = $('.acf-field-58e38da2194e3 input').val();

    // make sure there is no error with 'Teaser Image value is required' if a video is present with 'Teaser Image' checked
    if(videoTeaserImage.length > 0) {
        // loop over error messages to try to find an error message relating to Site Teaser -> Teaser Image
        for(var i=0; i<json.errors.length; i++) {
            if(json.errors[i].input === 'acf[field_58e38da2194e3]') {
                // remove the error message from the errors array
                json.errors.splice(i, 1);

                // if the removed error was the last then update valid
                if(json.errors.length === 0) {
                    json.valid = 1;
                }

                // hide acf error message bubble for this field
                $('.acf-field-58e38da2194e3 .acf-error-message').hide()
                break;
            }
        }
    }

    // make sure the user hasn't selected multiple Video Teaser Image
    if(videoTeaserImage.length > 1) {
        videoTeaserImage.slice(1).each(function() {
            var teaserImageError = {
                input: $(this).attr('name'),
                message: "Please make sure you have only 1 Video Teaser Image!"
            };

            if (typeof json.errors.length === 'undefined') {
                json.errors = [];
            }

            json.errors.push(teaserImageError);
            // invalidate the form
            json.valid = 0;
        });
    }

    return json;
});
