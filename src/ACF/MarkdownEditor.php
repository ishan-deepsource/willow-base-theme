<?php

namespace Bonnier\Willow\Base\ACF;

use Bonnier\Willow\Base\Models\WpComposite;
use Bonnier\Willow\MuPlugins\Helpers\LanguageProvider;

class MarkdownEditor extends \acf_field
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

        $this->name = 'markdown-editor';


        /*
        *  label (string) Multiple words, can include spaces, visible when selecting a field type
        */

        $this->label = __('Markdown Editor', 'acf-markdown-editor');


        /*
        *  category (string) basic | content | choice | relational | jquery | layout | CUSTOM GROUP NAME
        */

        $this->category = 'basic';


        /*
        *  defaults (array) Array of default settings which are merged into the field object.
         * These are used later in settings
        */

        $this->defaults = array(
            'font_size' => 14,
        );


        /*
        *  l10n (array) Array of strings that are used in JavaScript.
         * This allows JS strings to be translated in PHP and loaded via:
        *  var message = acf._e('markdown-editor', 'error');
        */

        $this->l10n = array(
            'error' => __('Error! Please enter a higher value', 'acf-markdown-editor'),
        );


        /*
        *  settings (array) Store plugin settings (url, path, version) as a reference for later use with assets
        */

        /*
        * ADDING OUR CHARACTER COUNT AND INITIAL COUNT TO THE ADMIN BAR
        */
        add_action('admin_bar_menu', [$this, 'add_character_count_to_admin_bar'], 110);

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

        acf_render_field_setting(
            $field,
            [
                'label' => __('Simple MDE Configuration', 'acf-markdown-editor'),
                'instructions' => __('Write', 'acf-markdown-editor'),
                'type' => 'radio',
                'name' => 'simple_mde_config',
                'required' => 0,
                'conditional_logic' => 0,
                'wrapper' => [
                    'width' => '',
                    'class' => '',
                    'id' => '',
                ],
                'choices' => [
                    'standard' => 'Standard',
                    'simple' => 'Simple',
                ],
                'allow_null' => 0,
                'other_choice' => 0,
                'save_other_choice' => 0,
                'default_value' => 'standard',
                'layout' => 'vertical',
                'return_format' => 'value',
            ]
        );
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
        ?>
        <textarea rows="8"
                  class="acf-field-simple-mde"
                  data-simple-mde-config='<?php echo $field['simple_mde_config'] ?>'
                  name="<?php echo esc_attr($field['name']) ?>"
        ><?php echo $field['value'] ?></textarea>
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
        wp_enqueue_script('marked-js', 'https://cdn.jsdelivr.net/npm/marked/marked.min.js');

        // register & include JS
        wp_register_script(
            'acf-input-simple-mde',
            get_theme_file_uri('/assets/js/simplemde.min.js'),
            ['acf-input'],
            filemtime(get_theme_file_path('/assets/js/simplemde.min.js'))
        );
        wp_enqueue_script(
            'acf-input-simple-mde',
            '',
            [],
            filemtime(get_theme_file_path('/assets/js/simplemde.min.js'))
        );

        wp_enqueue_script(
            'acf-input-markdown-editor',
            get_theme_file_uri('/assets/js/acf/fields/markdown-editor.js'),
            ['marked-js'],
            filemtime(get_theme_file_path('/assets/js/acf/fields/markdown-editor.js'))
        );

        wp_enqueue_script(
            'acf-markdown-editor-init',
            get_theme_file_uri('/assets/js/acf/fields/markdown-acf-init.js'),
            ['acf-input', 'acf-input-markdown-editor'],
            filemtime(get_theme_file_path('/assets/js/acf/fields/markdown-acf-init.js'))
        );

        //ContentHub composite fields validation
        $current_screen = get_current_screen();
        if (isset($current_screen->id) && $current_screen->id === WpComposite::POST_TYPE) {
            wp_enqueue_script(
                'acf-composite-validation',
                get_theme_file_uri('/assets/js/acf/fields/composite-validation.js'),
                ['acf-input'],
                filemtime(get_theme_file_path('/assets/js/acf/fields/composite-validation.js'))
            );
        }
        //ContentHub composite fields validation
        $current_screen = get_current_screen();
        if (isset($current_screen->id) && $current_screen->id === 'page') {
            wp_enqueue_script(
                'acf-page-validation',
                get_theme_file_uri('/assets/js/acf/fields/page-validation.js'),
                ['acf-input'],
                filemtime(get_theme_file_path('/assets/js/acf/fields/page-validation.js'))
            );
        }

        $language = 'da';
        if (isset($_GET['post'])) {
            $language = LanguageProvider::getPostLanguage($_GET['post']);
        } elseif (isset($_GET['new_lang'])) {
            $language = $_GET['new_lang'];
        }
        $dictionary = '/assets/js/lang/' . $language . '.dic.txt';
        $affFile = '/assets/js/lang/' . $language . '.aff.txt';
        $dictionaryExists = file_exists(get_theme_file_path($dictionary));
        $affExists = file_exists(get_theme_file_path($affFile));
        if ($language && $dictionaryExists && $affExists) {
            wp_localize_script(
                'acf-input-markdown-editor',
                'dictionary',
                [
                    'dic' => sprintf(
                        '%s%s?ver=%s',
                        parse_url(get_theme_file_uri(), PHP_URL_PATH),
                        $dictionary,
                        filemtime(get_theme_file_path($dictionary))
                    ),
                    'aff' => sprintf(
                        '%s%s?ver=%s',
                        parse_url(get_theme_file_uri(), PHP_URL_PATH),
                        $affFile,
                        filemtime(get_theme_file_path($affFile))
                    )
                ]
            );
        }

        // register & include CSS
        wp_register_style(
            'acf-input-markdown-editor',
            get_theme_file_uri('/assets/css/simplemde.min.css'),
            ['acf-input'],
            filemtime(get_theme_file_path('/assets/css/simplemde.min.css'))
        );
        wp_enqueue_style(
            'acf-input-markdown-editor', '', [],
            filemtime(get_theme_file_path('/assets/css/simplemde.min.css'))
        );

        wp_register_script(
            'text-field-character-counter',
            get_theme_file_uri('/assets/js/text-field-character-counter.js'),
            ['acf-input', 'acf-input-markdown-editor', 'acf-input-markdown-editor'],
            filemtime(get_theme_file_path('/assets/js/text-field-character-counter.js'))
        );
        wp_enqueue_script(
            'text-field-character-counter',
            '',
            ['acf-input', 'acf-input-markdown-editor', 'acf-input-markdown-editor'],
            filemtime(get_theme_file_path('/assets/js/text-field-character-counter.js'))
        );
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
        return wp_unslash($value);
    }

    public function update_value($value, $post_id, $field)
    {
        // acf saves calls stripslashes_deep() on save which removes all slashes from content
        // to allow slashes we call wp_slash() on value before it is saved to the database
        return wp_slash($value);
    }

    public function add_character_count_to_admin_bar($admin_bar)
    {
        $admin_bar->add_menu(array(
            'id'    => 'initial-character-count',
            'parent' => null,
            'group'  => null,
            'title' => 'Initial Characters: ', //you can use img tag with image link. it will show the image icon Instead of the title.
            'meta' => [
                'title' => __('Initial Characters', 'textdomain'), //This title will show on hover
                'class' => 'admin-menu-initial-character-count',
            ]
        ));
        $admin_bar->add_menu(array(
            'id'    => 'character-count',
            'parent' => null,
            'group'  => null,
            'title' => 'Characters Count: ', //you can use img tag with image link. it will show the image icon Instead of the title.
            'meta' => [
                'title' => __('Characters Count', 'textdomain'), //This title will show on hover
                'class' => 'admin-menu-character-count',
            ]
        ));
        $admin_bar->add_menu(array(
            'id'    => 'initial-body-text-count',
            'parent' => null,
            'group'  => null,
            'title' => 'Initial Body Text Count: ', //you can use img tag with image link. it will show the image icon Instead of the title.
            'meta' => [
                'title' => __('Initial Body Text Count', 'textdomain'), //This title will show on hover
                'class' => 'admin-menu-initial-body-text-count',
            ]
        ));
        $admin_bar->add_menu(array(
            'id'    => 'body-text-count',
            'parent' => null,
            'group'  => null,
            'title' => 'Body Text Count: ', //you can use img tag with image link. it will show the image icon Instead of the title.
            'meta' => [
                'title' => __('Body Text Count', 'textdomain'), //This title will show on hover
                'class' => 'admin-menu-body-text-count',
            ]
        ));
    }
}
