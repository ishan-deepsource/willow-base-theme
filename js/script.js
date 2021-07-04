var s = jQuery(".acf-taxonomy-field[data-taxonomy=category]").children().clone();
var v1 = jQuery(".acf-taxonomy-field[data-taxonomy=post_tag]").children().clone();


jQuery( '.post_lang_choice' ).change(function() {  
  var url = window.location.href; 
  var id = url.substring(url.lastIndexOf("post=")+5,url.lastIndexOf("&action"));
    setTimeout(function(){
       jQuery(".acf-taxonomy-field[data-taxonomy=category]").html(s);
       jQuery('.acf-taxonomy-field[data-taxonomy=category] select').select2({        
         ajax: {
           url: '/wp/wp-admin/admin-ajax.php?_fs_blog_admin=true',
           type:  'POST',
           data: { 
              action: 'acf/fields/taxonomy/query',
              s: '',
              paged: 1,
              field_key: jQuery('.acf-taxonomy-field[data-taxonomy=category] select').attr('id').substring(4),
              nonce: window.ajax_var.nonce,
              post_id: id
            },
           success: function (data) {
               return data;
           },
           error: function () {
               alert("error");
           }
         }
       });       
       jQuery(".acf-taxonomy-field[data-taxonomy=category] select").addClass('select2-hidden-accessible');
       jQuery(".acf-taxonomy-field[data-taxonomy=post_tag]").html(v1);
       jQuery('.acf-taxonomy-field[data-taxonomy=post_tag] select').select2({
         ajax: {
           url: '/wp/wp-admin/admin-ajax.php?_fs_blog_admin=true',
           type:  'POST',
           data: { 
              action: 'acf/fields/taxonomy/query',
              s: '',
              paged: 1,
              field_key: jQuery('.acf-taxonomy-field[data-taxonomy=post_tag] select').attr('id').substring(4),
              nonce: window.ajax_var.nonce,
              post_id: id
            },
           success: function (response) {
               return response;
           },
           error: function () {
               alert("error");
           }
         }
       });   
       jQuery(".acf-taxonomy-field[data-taxonomy=post_tag] select").addClass('select2-hidden-accessible');
       jQuery('.select2-container').addClass('-acf');
      },3000);   
   });