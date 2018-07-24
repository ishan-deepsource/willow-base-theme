<?php

namespace Bonnier\Willow\Base\Actions\Universal;

/**
 * This is a custom made ACF Options page
 * The following Code is Depending on Advanced Custom Fields Plugin
 *
 * @link https://www.advancedcustomfields.com/
 * @link https://www.advancedcustomfields.com/resources/acf_add_options_page/
 *
 */
class ThemeSettings
{
    public function __construct()
    {
        if (function_exists('acf_add_options_page')) {
            $this->registerSettings();
            $this->addOptionsPage();
            $this->AddFieldGroups();
        }
    }

    public function registerSettings()
    {
        global $acf_settings;

        /**
         *  Define the acf theme settings
         */
        $acf_settings               = array(
            'hide_acf_in_backend'       => true,
            'options_page'              => array(
                'page_title'            => __('Theme General Settings'),
                'menu_title'            => __('Theme Settings'),
                'menu_slug'             => 'theme-general-settings',
                'capability'            => 'manage_options',
                'redirect'              => false,
            ),
        );
    }

    public function addOptionsPage()
    {
        global $acf_settings;

        // Check if an options page exists in the acf settings array
        if (array_key_exists('options_page', $acf_settings) && empty($acf_settings['options_page']) == false) {
            // Create the options page
            acf_add_options_page($acf_settings['options_page']);
        }

        // Check if any options sub pages exists in the acf settings array
        if (array_key_exists('options_sub_pages', $acf_settings) && empty($acf_settings['options_sub_pages']) == false) {
            // Loop through each of them
            foreach ($acf_settings['options_sub_pages'] as $options_page) {
                // Create the options sub page
                acf_add_options_sub_page($options_page);
            }
        }
    }

    public function addFieldGroups()
    {
        if (function_exists('acf_add_local_field_group')):
            acf_add_local_field_group(
                array(
                    'key' => 'group_59e9dad3b8e4c',
                    'title' => 'Theme Settings',
                    'fields' => array(
                        array(
                            'key' => 'field_5a0c4ce9f1697',
                            'label' => 'TNS settings',
                            'name' => '',
                            'type' => 'tab',
                            'instructions' => '',
                            'required' => 0,
                            'conditional_logic' => 0,
                            'wrapper' => array(
                                'width' => '',
                                'class' => '',
                                'id' => '',
                            ),
                            'placement' => 'top',
                            'endpoint' => 0,
                        ),
                        array(
                            'key' => 'field_5a0c4e42f1698',
                            'label' => 'TNS settings',
                            'name' => 'tns_settings',
                            'type' => 'textarea',
                            'instructions' =>
                                'Optional TNS settings<br/>
                            Each line should consist of: locale,sitename,contentpath<br/><br/>
                            Example:<br/><br/>
                            da_DK,goerdetselv,goerdetselv<br/>
                            nb_NO,mmk,MMK/Gjoerdetselv<br/>
                            fi,bonnier,teeitse',
                            'required' => 0,
                            'conditional_logic' => 0,
                            'wrapper' => array(
                                'width' => '',
                                'class' => '',
                                'id' => '',
                            ),
                            'default_value' => '',
                            'placeholder' => '',
                            'maxlength' => '',
                            'rows' => '',
                            'new_lines' => '',
                        ),
                        array(
                            'key' => 'field_9ae77743d2153',
                            'label' => 'Facebook settings',
                            'name' => '',
                            'type' => 'tab',
                            'instructions' => '',
                            'required' => 0,
                            'conditional_logic' => 0,
                            'wrapper' => array(
                                'width' => '',
                                'class' => '',
                                'id' => '',
                            ),
                            'placement' => 'top',
                            'endpoint' => 0,
                        ),
                        array(
                            'key' => 'field_d3d84a7bee9f1',
                            'label' => 'Facebook Page IDs',
                            'name' => 'facebook_page_ids',
                            'type' => 'textarea',
                            'instructions' =>
                                'to be outputtet as meta tags<br>
                            One id per line<br><br>
                            Example:<br><br>
                            123123123123<br>
                            434413441421<br>
                            6565656565666',
                            'required' => 0,
                            'conditional_logic' => 0,
                            'wrapper' => array(
                                'width' => '',
                                'class' => '',
                                'id' => '',
                            ),
                            'default_value' => '',
                            'placeholder' => '',
                            'maxlength' => '',
                            'rows' => '',
                            'new_lines' => '',
                        ),
                    ),
                    'location' => array(
                        array(
                            array(
                                'param' => 'options_page',
                                'operator' => '==',
                                'value' => 'theme-general-settings',
                            ),
                        ),
                    ),
                    'menu_order' => 0,
                    'position' => 'normal',
                    'style' => 'default',
                    'label_placement' => 'top',
                    'instruction_placement' => 'label',
                    'hide_on_screen' => '',
                    'active' => 1,
                    'description' => '',
                )
            );
        endif;
    }
}
