<?php

// disable direct access
if (!defined('ABSPATH')) {
    exit;
}

// shortcode for display Form on Frontend
function wpleadgen_form_shortcode($atts) {

    // attributes
    $atts = shortcode_atts(array(
        'class' => 'wpleadgen_form',
        'label_name' => '',
        'label_phone' => '',
        'label_email' => '',
        'label_budget' => '',
        'label_message' => '',
        'label_submit' => '',
        'maxlength_name' => '',
        'maxlength_phone' => '',
        'maxlength_email' => '',
        'maxlength_budget' => '',
        'maxlength_message' => '',
        'rows_message' => '',
        'cols_message' => '',
        'required_name' => '',
        'required_phone' => '',
        'required_email' => '',
        'required_budget' => '',
        'required_message' => '',
            ), $atts);

    $current_datetime = date('Y-m-d H:i:s', current_time('timestamp'));

    //Check Shortcode Atts
    $label_name = $atts['label_name'] ? $atts['label_name'] : 'Name';
    $label_phone = $atts['label_phone'] ? $atts['label_phone'] : 'Phone No';
    $label_email = $atts['label_email'] ? $atts['label_email'] : 'Email Address';
    $label_budget = $atts['label_budget'] ? $atts['label_budget'] : 'Desired Budget';
    $label_message = $atts['label_message'] ? $atts['label_message'] : 'Message';
    $label_submit = $atts['label_submit'] ? $atts['label_submit'] : 'Submit';

    $maxlength_name = $atts['maxlength_name'] ? $atts['maxlength_name'] : '50';
    $maxlength_phone = $atts['maxlength_phone'] ? $atts['maxlength_phone'] : '10';
    $maxlength_email = $atts['maxlength_email'] ? $atts['maxlength_email'] : '50';
    $maxlength_budget = $atts['maxlength_budget'] ? $atts['maxlength_budget'] : '50';
    $maxlength_message = $atts['maxlength_message'] ? $atts['maxlength_message'] : '200';

    $rows_message = $atts['rows_message'] ? $atts['rows_message'] : '';
    $cols_message = $atts['cols_message'] ? $atts['cols_message'] : '';

    $required_name = $atts['required_name'] == 'true' ? 'required' : '';
    $required_phone = $atts['required_phone'] == 'true' ? 'required' : '';
    $required_email = $atts['required_email'] == 'true' ? 'required' : '';
    $required_budget = $atts['required_budget'] == 'true' ? 'required' : '';
    $required_message = $atts['required_message'] == 'true' ? 'required' : '';


    $leadgen_form = '<form id="leadgen_form" class="' . $atts['class'] . '" method="post">
		<input type="hidden" name="datetime" class="datetime" value="' . $current_datetime . '" />
                <div class="successmessage"> Your lead sent successfully.</div>
                <div class="errormessage"> Please try again something went wrong.</div>
		<div class="form-group">
			<label for="name">' . esc_attr($label_name) . ' :</label>
			<input type="text" name="name" class="form-control ' . esc_attr($required_name) . '" maxlength="' . esc_attr($maxlength_name) . '" />
		</div>

		<div class="form-group">
			<label for="phone">' . esc_attr($label_phone) . ' :</label>
			<input type="tel" name="phone" class="form-control ' . esc_attr($required_phone) . '" maxlength="' . esc_attr($maxlength_phone) . '" />
		</div>

		<div class="form-group">
			<label for="email">' . esc_attr($label_email) . ' :</label>
			<input type="email" name="email" class="form-control ' . esc_attr($required_email) . '" maxlength="' . esc_attr($maxlength_email) . '" />
		</div>

		<div class="form-group">
			<label for="email">' . esc_attr($label_budget) . ' :</label>
			<input type="text" name="budget" class="form-control ' . esc_attr($required_budget) . '" maxlength="' . esc_attr($maxlength_budget) . '" />
		</div>
		
		<div class="form-group">
			<label for="message">' . esc_attr($label_message) . ' :</label>
			<textarea name="message" rows="' . esc_attr($rows_message) . '" cols="' . esc_attr($cols_message) . '" class="form-control ' . esc_attr($required_message) . '" maxlength="' . esc_attr($maxlength_message) . '"></textarea>
		</div>
		<div class="form-group">
			<button type="submit" name="submit" class="btn btn-primary submit_form">' . esc_attr($label_submit) . '</button>
		</div>
	</form>';

    return $leadgen_form;
}
add_shortcode('wpleadgenform', 'wpleadgen_form_shortcode');

// Form Submission with Ajax

function form_submit_with_ajax() {
    global $wpdb;

    $form_data = $_POST;
    $user_id = 1;

    // Create post object
    $customer = array(
        'post_title' => wp_strip_all_tags($form_data['name']),
        'post_content' => $form_data['message'],
        'post_status' => 'publish',
        'post_author' => $user_id,
        'post_type' => 'customer',
        'meta_input' => array(
            'phone' => $form_data['phone'],
            'email' => $form_data['email'],
            'budget' => $form_data['budget'],
            'datetime' => $form_data['datetime']
        )
    );

    // Insert the post into the database
    wp_insert_post($customer);

    wp_die();
}
add_action('wp_ajax_form_submit_with_ajax', 'form_submit_with_ajax');
add_action('wp_ajax_nopriv_form_submit_with_ajax', 'form_submit_with_ajax');