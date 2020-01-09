<?php
/**
* Plugin Name: WP Custom LeadGen Form
* Description: Provide shortcode for display form on frontend of LeadGen Form.
* Author:      Incipient Info
* Version:     1.0.0
* Text Domain: wp-leadgen
*/

// disable direct access
if (!defined('ABSPATH'))
    exit(); // Exit if accessed directly

// Define All Default things For Plugin
define('WPLEADGEN_VERSION', '1.0.0');
define('WPLEADGEN_PL_URL', plugins_url('/', __FILE__));
define('WPLEADGEN_PL_PATH', plugin_dir_path(__FILE__));


// Include Files For Shortcode
include( WPLEADGEN_PL_PATH . 'include/leadgen-form-shortcodes.php' );

// Setup Admin Menu Section
function wpleadgen_setup_menu() {
    add_menu_page('LeadGen Form Plugin', 'Lead Gen Form', 'manage_options', 'wp-leadgen', 'wpleadgen_init');
}
add_action('admin_menu', 'wpleadgen_setup_menu');


// Document Page For Shortcode
function wpleadgen_init() {
    ?>
    <div class="leadgen_admin_document">
        <h1>LeadGen Form Shortcode Documentation:</h1>
        <h4>Shortcode : [wpleadgenform label_name="Name" required_name="true" maxlength_name="5" rows_message="5"]</h4>
        <p>Here we have 5 Fields in Form which are :</p>
        <ol>
            <li>name</li>
            <li>email</li>
            <li>phone</li>
            <li>budget</li>
            <li>message</li>
        </ol>

        <h4>Here we can add attributes in shortcode for each upper mentioned field like this:</h4>
        <p>If you want to change Label of any field then you need to add attribute in shortcode like this:</p>
        <h5>label_name="Name", label_email="Email Address"</h5>

        <p>If you want to add maxlength for any field then you need to add attribute in shortcode like this:</p>
        <h5>maxlength_name="40", maxlength_email="20"</h5>

        <p>If you want to make any field required then you need to add attribute in shortcode like this:</p>
        <h5>required_name="true", required_email="true"</h5>

        <p>For Message field you can set cols & rows like this:</p>
        <h5>rows_message="10", cols_message="50"</h5>

        <p>If you want to change Label of Submit button then you need to add attribute in shortcode like this:</p>
        <h5>label_submit="Add Customer"</h5>

    </div>
    <?php
}

//Enqueue Assests ( CSS & JS )
function wpleadgen_assests_enqueue() {
    // Register Style
    wp_enqueue_style('wpleadgen-custom-style', WPLEADGEN_PL_URL . 'assets/css/custom-style.css', array(), WPLEADGEN_VERSION, 'all');

    // Script register
    wp_enqueue_script('wpleadgen-jquery-validate', WPLEADGEN_PL_URL . 'assets/js/jquery.validate.js', array('jquery'), WPLEADGEN_VERSION, true);
    wp_enqueue_script('wpleadgen-custom-script', WPLEADGEN_PL_URL . 'assets/js/custom.js', array('jquery'), WPLEADGEN_VERSION, true);
    wp_localize_script('wpleadgen-custom-script', 'FrontAjax', array('ajaxurl' => admin_url('admin-ajax.php')));
}
add_action('wp_enqueue_scripts', 'wpleadgen_assests_enqueue');


// Create Customer Custom post type
function wpleadgen_custom_postype() {
    $leadgen_args = array(
        'labels' => array('name' => esc_attr__('Customers', 'wp-leadgen')),
        'menu_icon' => 'dashicons-email',
        'public' => true,
        'can_export' => true,
        'show_in_nav_menus' => false,
        'show_ui' => true,
        'show_in_rest' => true,
        'capability_type' => 'post',
        'capabilities' => array('create_posts' => 'do_not_allow'),
        'map_meta_cap' => true,
        'supports' => array('title', 'editor')
    );
    register_post_type('customer', $leadgen_args);
}
add_action('init', 'wpleadgen_custom_postype');

// Add Meta Boxes For Edit all Data in Admin Dashboard
function add_custom_meta_box() {
    add_meta_box("customer-data-box", "Customer Data", "custom_meta_box_markup", "customer", "side", "high", null);
}
add_action("add_meta_boxes", "add_custom_meta_box");

function custom_meta_box_markup($post) {
    wp_nonce_field(basename(__FILE__), "customer-data-nonce");
    ?>
    <div>
        <label for="email">Email</label>
        <input name="email" type="text" value="<?php echo get_post_meta($post->ID, "email", true); ?>">

        <label for="phone">Phone</label>
        <input name="phone" type="text" value="<?php echo get_post_meta($post->ID, "phone", true); ?>">

        <label for="budget">budget</label>
        <input name="budget" type="text" value="<?php echo get_post_meta($post->ID, "budget", true); ?>">
    </div>
    <?php
}

function save_custom_meta_box($post_id, $post, $update) {
    if (!isset($_POST["customer-data-nonce"]) || !wp_verify_nonce($_POST["customer-data-nonce"], basename(__FILE__)))
        return $post_id;

    if (!current_user_can("edit_post", $post_id))
        return $post_id;

    if (defined("DOING_AUTOSAVE") && DOING_AUTOSAVE)
        return $post_id;

    $slug = "customer";
    if ($slug != $post->post_type)
        return $post_id;

    $phone = "";
    $email = "";
    $budget = "";

    if (isset($_POST["email"])) {
        $email = $_POST["email"];
    }
    update_post_meta($post_id, "email", $email);

    if (isset($_POST["phone"])) {
        $phone = $_POST["phone"];
    }
    update_post_meta($post_id, "phone", $phone);

    if (isset($_POST["budget"])) {
        $budget = $_POST["budget"];
    }
    update_post_meta($post_id, "budget", $budget);
}
add_action("save_post", "save_custom_meta_box", 10, 3);

// Add Columns to Dashboard All Customers Screen for Filter
function customer_columns($columns) {
    $columns['phone'] = 'Phone No';
    $columns['email'] = 'Email ID';
    $columns['budget'] = 'Desired Budget';
    $columns['datetime'] = 'Date/Time';
    return $columns;
}
add_filter('manage_edit-customer_columns', 'customer_columns');

function customer_column_data($column, $post_id) {
    global $post;

    switch ($column) {

        /* If displaying the 'phone' column. */
        case 'phone' :

            /* Get the genres for the post. */
            $phone = get_post_meta($post_id, 'phone', true);

            /* If terms were found. */
            if (!empty($phone)) {
                echo $phone;
            }

            break;


        /* If displaying the 'email' column. */
        case 'email' :

            /* Get the genres for the post. */
            $email = get_post_meta($post_id, 'email', true);

            /* If terms were found. */
            if (!empty($email)) {
                echo $email;
            }

            break;

        /* If displaying the 'budget' column. */
        case 'budget' :

            /* Get the genres for the post. */
            $budget = get_post_meta($post_id, 'budget', true);

            /* If terms were found. */
            if (!empty($budget)) {
                echo $budget;
            }

            break;

        /* If displaying the 'budget' column. */
        case 'datetime' :

            /* Get the genres for the post. */
            $datetime = get_post_meta($post_id, 'datetime', true);

            /* If terms were found. */
            if (!empty($datetime)) {
                echo $datetime;
            }

            break;

        /* Just break out of the switch statement for everything else. */
        default :
            break;
    }
}
add_action('manage_customer_posts_custom_column', 'customer_column_data', 10, 2);