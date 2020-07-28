<?php

namespace Bonnier\Willow\Base\ACF;

class ImageHotSpotCoordinates extends \acf_field
{
    /*
    *  __construct
    *
    *  This function will setup the field type data
    *
    *  @type function
    *  @date 5/03/2014
    *  @since 5.0.0
    *
    *  @param n/a
    *  @return n/a
    */
    public function __construct()
    {
        /*
        *  name (string) Single word, no spaces. Underscores allowed
        */

        $this->name = 'image-hotspot-coordinates';


        /*
        *  label (string) Multiple words, can include spaces, visible when selecting a field type
        */

        $this->label = __('Image Hotspot Coordinates', 'acf-image-hotpsot-coordinates');


        /*
        *  category (string) basic | content | choice | relational | jquery | layout | CUSTOM GROUP NAME
        */

        $this->category = 'content';


        /*
        *  defaults (array) Array of default settings which are merged into the field object.
         * These are used later in settings
        */

        $this->defaults = [];


        /*
        *  l10n (array) Array of strings that are used in JavaScript.
         * This allows JS strings to be translated in PHP and loaded via:
        *  var message = acf._e('markdown-editor', 'error');
        */

        $this->l10n = array(
            'error' => __('Error! Please enter a higher value', 'acf-image-hotspot-coordinates'),
        );


        /*
        *  settings (array) Store plugin settings (url, path, version) as a reference for later use with assets
        */


        // do not delete!
        parent::__construct();
    }


    /*
    *  render_field_settings()
    *
    *  Create extra settings for your field. These are visible when editing a field
    *
    *  @type action
    *  @since 3.6
    *  @date 23/01/13
    *
    *  @param $field (array) the $field being edited
    *  @return n/a
    */
    public function render_field_settings($field)
    {
        /*
        *  acf_render_field_setting
        *
        *  This function will create a setting for your field.
         * Simply pass the $field parameter and an array of field settings.
        *  The array of settings does not require a `value` or `prefix`;
         * These settings are found from the $field array.
        *
        *  More than one setting can be added by copy/paste the above code.
        *  Please note that you must also have a matching $defaults value for the field name (font_size)
        */
    }

    /*
    *  render_field()
    *
    *  Create the HTML interface for your field
    *
    *  @param $field (array) the $field being rendered
    *
    *  @type action
    *  @since 3.6
    *  @date 23/01/13
    *
    *  @param $field (array) the $field being edited
    *  @return n/a
    */
    public function render_field($field)
    {
        $inputId = uniqid('edit-hotspot-image-input-'); ?>
        <button type="button" class="edit-hotspot-image" data-input-id="<?php echo $inputId ?>">
            Edit Hotspot
        </button>
        <input type="hidden"
               id="<?php echo $inputId ?>"
               name="<?php echo esc_attr($field['name']) ?>"
               value="<?php echo $field['value'] ?>"
        >
        <?php
    }


    /*
    *  input_admin_enqueue_scripts()
    *
    *  This action is called in the admin_enqueue_scripts action on the edit screen where your field is created.
    *  Use this action to add CSS + JavaScript to assist your render_field() action.
    *
    *  @type action (admin_enqueue_scripts)
    *  @since 3.6
    *  @date 23/01/13
    *
    *  @param n/a
    *  @return n/a
    */
    public function input_admin_enqueue_scripts()
    {
        wp_register_script(
            'acf-input-image-hotspot-coordinates',
            get_theme_file_uri('/assets/js/acf/fields/image-hotspot-coordinates.js'),
            ['acf-input', 'focal_point_class'],
            filemtime(get_theme_file_path('/assets/js/acf/fields/image-hotspot-coordinates.js'))
        );
        wp_enqueue_script('acf-input-image-hotspot-coordinates');
    }

    /*
    *  load_value()
    *
    *  This filter is applied to the $value after it is loaded from the db
    *
    *  @type filter
    *  @since 3.6
    *  @date 23/01/13
    *
    *  @param $value (mixed) the value found in the database
    *  @param $post_id (mixed) the $post_id from which the value was loaded
    *  @param $field (array) the field array holding all the field options
    *  @return $value
    */
    public function load_value($value, $post_id, $field)
    {
        return $value;
    }

    public function update_value($value, $post_id, $field)
    {
        return $value;
    }
}
