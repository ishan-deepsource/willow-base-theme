function getApiData(){
    jQuery('.acf-fields > .acf-field[data-name=embed_url] .acf-input > .acf-input-wrap > input').bind('change keyup paste', function(){
        var $this = jQuery(this);
        var val = $this.val();
        if(val !== '') {
            var container = $this.closest('[data-layout=video]')
            var nameField = container.find('.acf-field[data-name=name]');
            var descField = container.find('.acf-field[data-name=description]');
            var thumbsField = container.find('.acf-field[data-name=thumbnail_url]');
            var dateField = container.find('.acf-field[data-name=upload_date]');
            var cUrlField = container.find('.acf-field[data-name=content_url]');
            var durationVideoField = container.find('.acf-field[data-name=duration]');
            var strich = '';

            function timeCalc(value){
                const sec = parseInt(value, 10);
                let hours   = Math.floor(sec / 3600);
                let minutes = Math.floor((sec - (hours * 3600)) / 60);
                let seconds = sec - (hours * 3600) - (minutes * 60);

                (hours.toString().length===1?hours = "0"+hours:hours = hours);
                (minutes.toString().length===1?minutes = "0"+minutes:minutes = minutes);
                (seconds.toString().length===1?seconds = "0"+seconds:seconds = seconds);
                return hours+":"+minutes+":"+seconds;
            }

            if(val.indexOf('youtube') >= 0){
                strich = val.split("watch?v=").pop();

                function parseDurationString( durationString ){
                    var stringPattern = /^PT(?:(\d+)D)?(?:(\d+)H)?(?:(\d+)M)?(?:(\d+(?:\.\d{1,3})?)S)?$/;
                    var stringParts = stringPattern.exec( durationString );
                    return timeCalc((
                        (
                            (
                                ( stringParts[1] === undefined ? 0 : stringParts[1]*1 )  /* Days */
                                * 24 + ( stringParts[2] === undefined ? 0 : stringParts[2]*1 ) /* Hours */
                            )
                            * 60 + ( stringParts[3] === undefined ? 0 : stringParts[3]*1 ) /* Minutes */
                        )
                        * 60 + ( stringParts[4] === undefined ? 0 : stringParts[4]*1 ) /* Seconds */
                    ));
                }

                $.ajax({
                    url: 'https://www.googleapis.com/youtube/v3/videos?id='+strich+'&key=AIzaSyBm6oQHLW8qMuJM4MQNY-gK7jhWDI3YuAs&part=snippet,contentDetails',
                    type: 'get',
                    dataType: 'JSON',
                    success: function(response){
                        var title = response.items[0].snippet.title;
                        var description = response.items[0].snippet.description;
                        var thumbnail = response.items[0].snippet.thumbnails.default.url;
                        var uploadDate = response.items[0].snippet.publishedAt;
                        var contentUrl = val.replace('/watch?v=', '/embed/');
                        var duration = parseDurationString(response.items[0].contentDetails.duration);

                        nameField.find('.acf-input .acf-input-wrap input[type=text]').val(title);
                        descField.find('.acf-input textarea').val(description);
                        thumbsField.find('.acf-input .acf-input-wrap input[type=text]').val(thumbnail);
                        dateField.find('.acf-input .acf-input-wrap input[type=text]').val(uploadDate);
                        cUrlField.find('.acf-input .acf-input-wrap input[type=text]').val(contentUrl);
                        durationVideoField.find('.acf-input .acf-input-wrap input[type=hidden]').val(duration);
                        durationVideoField.find('.acf-input .acf-input-wrap input[type=text]').val(duration);
                    }
                });
            }
            if(val.indexOf('vimeo') >= 0){
                strich = val.split("vimeo.com/").pop();

                $.ajax({
                    url: 'https://v1.nocodeapi.com/bonner/vimeo/FwQzwSpuHlTLGfVL/videoInfo?video_id='+strich,
                    type: 'get',
                    dataType: 'JSON',
                    success: function(response){
                        var title = response.name;
                        var description = response.description;
                        var thumbnail = response.pictures.sizes[0].link;
                        var uploadDate = response.created_time;
                        var contentUrl = 'https://player.vimeo.com/video/'+strich+'?h=614254eeef&amp;badge=0&amp;autopause=0&amp;player_id=0&amp;app_id=225822';
                        var duration = timeCalc(response.duration);

                        nameField.find('.acf-input .acf-input-wrap input[type=text]').val(title);
                        descField.find('.acf-input textarea').val(description);
                        thumbsField.find('.acf-input .acf-input-wrap input[type=text]').val(thumbnail);
                        dateField.find('.acf-input .acf-input-wrap input[type=text]').val(uploadDate);
                        cUrlField.find('.acf-input .acf-input-wrap input[type=text]').val(contentUrl);
                        durationVideoField.find('.acf-input .acf-input-wrap input[type=text]').val(duration);
                    }
                });
            }
        }
    });
}

getApiData();

jQuery('.acf-actions a.button-primary').click(function(){
    setTimeout(function(){
        if(jQuery(".acf-fc-popup a").length) {
            jQuery(".acf-fc-popup a[data-layout=video]").click(function(){
                setTimeout(function(){
                    getApiData();
                },1000);
            });
        }
    },1000);
});