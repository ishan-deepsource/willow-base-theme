setTimeout(function(){
var embedUrl = jQuery('.acf-fields > .acf-field[data-name=embed_url]').attr('data-key');
var name = jQuery('.acf-fields > .acf-field[data-name=name]');
var desc = jQuery('.acf-fields > .acf-field[data-name=description]');
var thumbs = jQuery('.acf-fields > .acf-field[data-name=thumbnailUrl]');
var date = jQuery('.acf-fields > .acf-field[data-name=uploadDate]');
var cUrl = jQuery('.acf-fields > .acf-field[data-name=contentUrl]'); 
var cUrl = jQuery('.acf-fields > .acf-field[data-name=contentUrl]');
var durationVideo = jQuery('.acf-fields > .acf-field[data-name=durationVideo]');  

jQuery('.acf-input-wrap > input[id*='+embedUrl+']').each(function( index ) {
  jQuery(this).on('change keyup paste', function(){  
   if(jQuery(this).val()!==''){  
     if(jQuery(this).val().indexOf('youtube') >= 0){
        var currentUrl = jQuery(this).val();
        var strich = jQuery(this).val().split("watch?v=").pop();
        $.ajax({
            url: 'https://www.googleapis.com/youtube/v3/videos?id='+strich+'&key=AIzaSyBm6oQHLW8qMuJM4MQNY-gK7jhWDI3YuAs&part=snippet,contentDetails',
            type: 'get',
            dataType: 'JSON',
            success: function(response){
             var title = response.items[0].snippet.title;
             var description = response.items[0].snippet.description;
             var thumbnail = response.items[0].snippet.thumbnails.default.url;
             var uploadDate = response.items[0].snippet.publishedAt;
             var contentUrl = currentUrl.replace('/watch?v=', '/embed/');
             var duration = response.items[0].contentDetails.duration;

             jQuery(name).find('.acf-input .acf-input-wrap input[type=text]').val(title);
             jQuery(desc).find('.acf-input textarea').val(description);
             jQuery(thumbs).find('.acf-input .acf-input-wrap input[type=text]').val(thumbnail);
             jQuery(date).find('.acf-input .acf-input-wrap input[type=text]').val(uploadDate);
             jQuery(cUrl).find('.acf-input .acf-input-wrap input[type=text]').val(contentUrl);
             jQuery(durationVideo).find('.acf-input .acf-input-wrap input[type=text]').val(duration);

            }
        });
     }
     if(jQuery(this).val().indexOf('vimeo') >= 0){      
      var strich = jQuery(this).val().split("vimeo.com/").pop();

      function iso8601(value){
        const sec = parseInt(value, 10);
        let hours   = Math.floor(sec / 3600);
        let minutes = Math.floor((sec - (hours * 3600)) / 60);
        let seconds = sec - (hours * 3600) - (minutes * 60);
        
        (hours!==0?hours = hours+"H":hours = "");
        (minutes!==0?minutes = minutes+"M":minutes = "");
        (seconds!==0?seconds = seconds+"S":seconds = "");
        return "PT"+hours+minutes+seconds;
      }
   
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
         var duration = iso8601(response.duration);

         jQuery(name).find('.acf-input .acf-input-wrap input[type=text]').val(title);
         jQuery(desc).find('.acf-input textarea').val(description);
         jQuery(thumbs).find('.acf-input .acf-input-wrap input[type=text]').val(thumbnail);
         jQuery(date).find('.acf-input .acf-input-wrap input[type=text]').val(uploadDate);
         jQuery(cUrl).find('.acf-input .acf-input-wrap input[type=text]').val(contentUrl);
         jQuery(durationVideo).find('.acf-input .acf-input-wrap input[type=text]').val(duration);

        }
    });      
    }            
   } 
 }); 
});
},1000);