<?php
/*
Plugin Name: Klaviyo Integration for Ninja Forms
Description: Integrates Klaviyo with Ninja Forms to send email in to your specified list.
Version: 1.0
Author: Hassan Ejaz
*/

// Enqueue Klaviyo JavaScript SDK
function klaviyo_ninja_forms_enqueue_scripts() {
    wp_enqueue_script('klaviyo-sdk', 'https://static.klaviyo.com/onsite/js/klaviyo.js?company_id=YOUR_COMPANY_ID', array(), '1.0', true);
}
add_action('wp_enqueue_scripts', 'klaviyo_ninja_forms_enqueue_scripts');

// Add Klaviyo hidden field to Ninja Forms
function klaviyo_ninja_forms_add_fields($fields) {
    $fields['klaviyo'] = array(
        'type' => 'hidden',
        'label' => 'Klaviyo',
    );
    return $fields;
}
add_filter('ninja_forms_fields', 'klaviyo_ninja_forms_add_fields');

// Set Klaviyo field value before form submission
function klaviyo_ninja_forms_set_field_value($form_data) {
    $klaviyo_field_key = 'klaviyo'; // Replace with the Klaviyo field key
    $klaviyo_value = ''; // Replace with the value to be set in Klaviyo field
    
    if (isset($form_data['fields'][$klaviyo_field_key])) {
        $form_data['fields'][$klaviyo_field_key]['value'] = $klaviyo_value;
    }
    
    return $form_data;
}
add_filter('ninja_forms_submit_data', 'klaviyo_ninja_forms_set_field_value');

// Subscribe user to Klaviyo list after form submission
function klaviyo_ninja_forms_subscribe_user($form_id) {
    $klaviyo_list_id = 'YOUR_LIST_ID'; // Replace with your Klaviyo list ID
    $klaviyo_field_key = 'klaviyo'; // Replace with the Klaviyo field key
    
    $form_data = ninja_forms_get_submitted_form($form_id);
    
    if (isset($form_data['fields'][$klaviyo_field_key])) {
        $email = $form_data['fields'][$klaviyo_field_key]['value'];
        
        // Subscribe user to Klaviyo list
        $klaviyo_script = "
            <script>
                var _learnq = _learnq || [];
                _learnq.push(['identify', {
                    '$email': {
                        '$email': '$email'
                    }
                }]);
                _learnq.push(['track', 'Subscribed', {
                    '$email': {
                        '$email': '$email'
                    }
                }]);
            </script>
        ";
        
        echo $klaviyo_script;
    }
}
add_action('ninja_forms_post_process', 'klaviyo_ninja_forms_subscribe_user');