<?php

/**
 * Plugin Name: Mural Routes – Custom Gravity Forms YouTube/Vimeo URL Validator
 * Description: Adds a checkbox option to Website fields in Gravity Forms to restrict input to YouTube/Vimeo URLs only.
 * Version: 1.0
 * Author: Adam Wills
 */

// Exit if accessed directly
if (!defined('ABSPATH')) {
    exit;
}

// Check if Gravity Forms is active
add_action('plugins_loaded', 'check_gravity_forms');

function check_gravity_forms(): void
{
    if (class_exists('GFForms')) {
        add_action('gform_field_standard_settings', 'add_youtube_vimeo_setting', 10, 2);
        add_action('gform_editor_js', 'youtube_vimeo_setting_js');
        add_filter('gform_field_validation', 'validate_youtube_vimeo_url', 10, 4);
    }
}

function add_youtube_vimeo_setting($position, $form_id) {
    if ($position == 25) {
        ?>
        <li class="youtube_vimeo_url_setting field_setting">
            <input type="checkbox" id="field_youtube_vimeo_url" onclick="SetFieldProperty('youtubeVimeoOnly', this.checked);" />
            <label for="field_youtube_vimeo_url" class="inline">
                <?php _e("YouTube/Vimeo URLs only", "gravityforms"); ?>
            </label>
        </li>
        <?php
    }
}

function youtube_vimeo_setting_js() {
    ?>
    <script type='text/javascript'>
        jQuery(document).ready(function($) {
            fieldSettings.website += ', .youtube_vimeo_url_setting';

            $(document).on('gform_load_field_settings', function(event, field, form) {
                $("#field_youtube_vimeo_url").prop("checked", field.youtubeVimeoOnly == true);
            });
        });
    </script>
    <?php
}

function validate_youtube_vimeo_url($result, $value, $form, $field) {
    if ($field->type == 'website' && $field->youtubeVimeoOnly && !empty($value)) {
        $valid_url = preg_match('/^(https?:\/\/)?(www\.)?(youtube\.com|youtu\.be|vimeo\.com)\/.+$/i', $value);

        if (!$valid_url) {
            $result['is_valid'] = false;
            $result['message'] = 'Please enter a valid YouTube or Vimeo URL.';
        }
    }
    return $result;
}