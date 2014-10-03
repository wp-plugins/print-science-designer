<?php
/**
 * Plugin Name: Print Science Designer 
 * Plugin URI: http://printscience.com/designer/
 * Description: Link between WooCommerce and the Print Science Designer to 	allow for product personalization and online design
 * Version: 1.0.7
 * Author: Print Science
 * Author URI: http://printscience.com
 * (c) 2014 Print Science. All rights reserved
 * */
include('lib/xmlrpc.inc');

function personalize_init() {
    //echo WP_LANG_DIR;     

    $locale = apply_filters('plugin_locale', get_locale(), 'personalize');
    load_plugin_textdomain('personalize', false, dirname(plugin_basename(__FILE__)) . '/language/');
    load_textdomain('personalize', WP_LANG_DIR . '/print_science_designer-' . $locale . '.mo');
    //load_textdomain( 'woocommerce', dirname(plugin_basename( __FILE__ ))."/lan/$locale.mo" );
}

add_action('plugins_loaded', 'personalize_init');

function register_session() {

    if (!session_id())
        session_start();
}

add_action('init', 'register_session');



global $wpdb;
$api_info_table = $wpdb->prefix . 'api_info';
define('API_INFO_TABLE', $api_info_table);
register_activation_hook(__FILE__, 'ps_install');
register_deactivation_hook(__FILE__, 'ps_uninstall');
add_action('admin_menu', 'ps_admin_menu');

function ps_install() {
    global $wpdb;
    $api_info_table = $wpdb->prefix . 'api_info';
    if ($wpdb->get_var("SHOW TABLES LIKE $api_info_table") != $api_info_table) {

        $wpdb->query("CREATE TABLE $api_info_table (id INT NOT NULL AUTO_INCREMENT PRIMARY KEY ,username VARCHAR( 255 ) NOT NULL ,api_key VARCHAR( 255 ) NOT NULL ,version VARCHAR( 255 ) NOT NULL ,url VARCHAR( 255 ) NOT NULL,image_url VARCHAR( 255 ) NOT NULL,window_type VARCHAR( 255 ) NOT NULL,background_color VARCHAR( 255 ) NOT NULL,opacity VARCHAR( 255 ) NOT NULL,margin VARCHAR( 255 ) NOT NULL)");
    }
}

function ps_uninstall() {
    global $wpdb;
    $api_info_table = $wpdb->prefix . 'api_info';
    $structure = "drop table if exists $api_info_table";
    $wpdb->query($structure);
}

function ps_admin_menu() {
    add_options_page('Personalization API', 'Personalization API', 'manage_options', 'personalize.php', 'ps_config_view');
}

// admin config view 
function ps_config_view() {
    global $wpdb;
    $api_info_table = $wpdb->prefix . 'api_info';
    if (isset($_POST) && ($_POST['save'] != '' || $_POST['test_connection'])) {



        $arr_api_info = $wpdb->get_results('SELECT id FROM ' . $api_info_table . ' where id=1');


        extract($_POST);
        /*         * ************************************CHECK FOR API CONNECTION********************************** */
        $client = new xmlrpc_client($url);
        switch ($version) {
            case '1.0.0':
                $function = new xmlrpcmsg('beginPersonalization', array(
                    php_xmlrpc_encode($username),
                    php_xmlrpc_encode($api_key),
                    php_xmlrpc_encode(''),
                    php_xmlrpc_encode(''),
                    php_xmlrpc_encode(''),
                    php_xmlrpc_encode(''),
                    php_xmlrpc_encode('')
                ));
                break;
            case '2.0.0':
                $function = new xmlrpcmsg('beginPersonalization', array(
                    php_xmlrpc_encode($username),
                    php_xmlrpc_encode($api_key),
                    php_xmlrpc_encode(''),
                    php_xmlrpc_encode(''),
                    php_xmlrpc_encode(''),
                    php_xmlrpc_encode(''),
                    php_xmlrpc_encode(''),
                    php_xmlrpc_encode('en'),
                    php_xmlrpc_encode(''),
                    php_xmlrpc_encode('')
                ));
                break;
            case '4.0.0':
                $function = new xmlrpcmsg('beginPersonalization', array(
                    php_xmlrpc_encode($username),
                    php_xmlrpc_encode($api_key),
                    php_xmlrpc_encode(''),
                    php_xmlrpc_encode(''),
                    php_xmlrpc_encode(''),
                    php_xmlrpc_encode(''),
                    php_xmlrpc_encode(''),
                    php_xmlrpc_encode('en')
                ));
                break;
            default:
                $function = new xmlrpcmsg('beginPersonalization', array(
                    php_xmlrpc_encode($username),
                    php_xmlrpc_encode($api_key),
                    php_xmlrpc_encode(''),
                    php_xmlrpc_encode(''),
                    php_xmlrpc_encode(''),
                    php_xmlrpc_encode(''),
                    php_xmlrpc_encode('')
                ));
                break;
        }
        $response = $client->send($function);
        $APIErrorCode = $response->errno;
        /*         * ************************************CHECK FOR API CONNECTION********************************** */
        $AuthenticationError = false;
        if ($APIErrorCode == '3' || $APIErrorCode == '1') {
            $AuthenticationError = true;
        }
        if (isset($_POST['test_connection'])) {
            if ($APIErrorCode == '3' || $APIErrorCode == '1') {
                $AuthenticationError = true;
            } else {
                $Authenticationsucc = true;
            }
        }
        //if (isset($_POST['save'])) {
        if (count($arr_api_info) == 0) {

            $wpdb->query("INSERT INTO  $api_info_table (username,api_key,version,url,image_url,window_type ,background_color,opacity,margin) VALUES('" . $_POST[username] . "','" . $_POST[api_key] . "','" . $_POST[version] . "','" . $_POST[url] . "','" . $_POST[image_url] . "','" . $_POST[window_type] . "','" . $_POST[background_color] . "','" . $_POST[opacity] . "','" . $_POST[margin] . "')");
        } else {

            $wpdb->query("UPDATE $api_info_table SET username='" . $_POST[username] . "',api_key='" . $_POST[api_key] . "',version='" . $_POST[version] . "',url='" . $_POST[url] . "',image_url='" . $_POST[image_url] . "',window_type='" . $_POST[window_type] . "',background_color='" . $_POST[background_color] . "',opacity='" . $_POST[opacity] . "',margin='" . $_POST[margin] . "' WHERE id=1");
        }
        //}
    }
    $arr_api_info = $wpdb->get_results('SELECT * FROM ' . $api_info_table . ' where id=1');
    $username = $arr_api_info[0]->username;
    $api_key = $arr_api_info[0]->api_key;
    $version = $arr_api_info[0]->version;
    $url = $arr_api_info[0]->url;
    $image_url = $arr_api_info[0]->image_url;
    $window_type = $arr_api_info[0]->window_type;
    $background_color = $arr_api_info[0]->background_color;
    $opacity = $arr_api_info[0]->opacity;
    $margin = $arr_api_info[0]->margin;
    ?>
    <style>
        .api_form{
            width:100%;
        }
        .row{
            width:100%;
            float:left;
            margin: 5px 0;
        }
        .row label{
            float: left;
            width: 18%;
        }
        .row input,.row select{
            width:20%; 
        }	
        .row .saveb{
            margin: 0 0 0 229px;
            width: 10%;		  
        }
        .auth_error {color:red; font-size: 13px; float: left; background: #FAF4ED;padding: 3px 40px; text-align: center;width: auto;}
        .auth_succ {color:green; font-size: 13px; float: left; background: #FAF4ED;padding: 3px 40px; text-align: center;width: auto;}
    </style>
    <link rel="stylesheet" media="screen" type="text/css" href="<?php echo plugins_url(); ?>/print_science_designer/css/colorpicker.css" />

    <script type="text/javascript" src="<?php echo plugins_url(); ?>/print_science_designer/js/colorpicker.js"></script>
    <script type="text/javascript" src="<?php echo plugins_url(); ?>/print_science_designer/js/eye.js"></script>
    <script type="text/javascript" src="<?php echo plugins_url(); ?>/print_science_designer/js/layout.js?ver=1.0.2"></script>


    <div class="api_form">

        <h2><?php echo __('Product Personalization Settings', 'personalize'); ?></h2>
        <?php
        if ($AuthenticationError) {
            echo '<span class="auth_error">Your API credentials are not correct. Please enter the correct API username and key.</span>';
        }
        if ($Authenticationsucc) {
            echo '<span class="auth_succ">Your API credentials are working fine.</span>';
        }
        ?>
        <form action="" method="post">
            <div class="row"><label><?php echo __('API Username', 'personalize'); ?></label><input name="username" id="username" value="<?php echo $username; ?>"/></div>
            <div class="row"><label><?php echo __('API Key', 'personalize'); ?></label><input name="api_key" id="api_key" value="<?php echo $api_key; ?>"/></div>
            <div class="row"><label><?php echo __('API Version', 'personalize'); ?></label>
                <select name="version" id="version">
                    <?php
                    //$arr_versions=array('1.0.0','2.0.0','4.0.0');
                    $arr_versions = array('4.0.0');
                    foreach ($arr_versions as $versionV) {
                        $selected = '';
                        if ($versionV == $version) {
                            $selected = "selected";
                        }
                        ?>  
                        <option value="<?php echo $versionV; ?>" <?php echo $selected; ?>><?php echo $versionV; ?></option>
                    <?php } ?>
                </select>			   

            </div>
            <div class="row"><label><?php _e('API URL', 'personalize'); ?> </label><input name="url" id="url" value="<?php echo $url; ?>"/></div>
            <div class="row"><label><?php echo __('API Image URL', 'personalize'); ?></label><input name="image_url" id="image_url" value="<?php echo $image_url; ?>"/></div>
            <div class="row"><label><?php echo __('Window type for launch of Designer', 'personalize'); ?></label>
                <select name="window_type" id="window_type">
                    <?php
                    $arr_types = array('New Window', 'Modal Pop-up window');
                    foreach ($arr_types as $type) {
                        $selected = '';
                        if ($type == $window_type) {
                            $selected = "selected";
                        }
                        ?>  
                        <option value="<?php echo $type; ?>" <?php echo $selected; ?>><?php echo $type; ?></option>
                    <?php } ?>
                </select>	  
            </div>
            <div class="row"><label><?php echo __('Background color of margin surrounding the modal window', 'personalize'); ?></label><input name="background_color" id="colorpickerField1" value="<?php echo $background_color; ?>"/><span><?php echo __('* Specify the background color', 'personalize'); ?> </span></div>
            <div class="row"><label><?php echo __('Opacity of modal window', 'personalize'); ?> </label><input name="opacity" id="opacity" value="<?php echo $opacity; ?>"/><span>*(%)</span></div>
            <div class="row"><label> <?php echo __('Width of margin surrounding the modal window', 'personalize'); ?></label><input name="margin" id="margin" value="<?php echo $margin; ?>"/><span> *(px)</span></div>

            <div class="row"><input class="saveb" type="submit" name="save" id="save" value="<?php echo __('Save Settings', 'personalize'); ?>"/>
                <input class="saveb" type="submit" name="test_connection" id="test_connection" value="<?php echo __('Test Connection', 'personalize'); ?>" style="margin: 0; width:12%;"/>
            </div>
        </form>
    </div>

    <?php
}

/** admin end on product page start
 * Custom Tabs for Product display
 * 
 * Outputs an extra tab to the default set of info tabs on the single product page.
 */
function personalize_tab_options_tab() {
    ?>
    <li class="inventory_tab inventory_options"><a href="#personalization"><?php _e('Personalization', 'personalize'); ?></a></li>
    <?php
}

add_action('woocommerce_product_write_panel_tabs', 'personalize_tab_options_tab');

/**
 * Custom Tab Options
 * 
 * Provides the input fields and add/remove buttons for custom tabs on the single product page.
 */
function personalize_tab_options() {
    global $post;
    $personalize = get_post_meta($post->ID, 'personalize', true)
    ?>
    <div id="personalization" class="panel woocommerce_options_panel">
        <div class="options_group">
            <p class="form-field"> 
                <?php woocommerce_wp_select(array('id' => 'personalize', 'class' => 'wc_input_price short', 'label' => __('Enable Personalization', 'personalize'), 'options' => array('n' => 'No', 'y' => 'Yes'), 'description' => __('Enable personalization via Print Science Designer.', 'woothemes'), 'value' => $personalize)); ?>

                <?php woocommerce_wp_text_input(array('id' => 'a_product_id', 'class' => 'wc_input_price short', 'label' => __('Product ID', 'personalize'))); ?>
                <?php woocommerce_wp_text_input(array('id' => 'a_template_id', 'class' => 'wc_input_price short', 'label' => __('Template ID', 'personalize'))); ?>
            </p>

        </div>	
    </div>
    <?php
}

add_action('woocommerce_product_write_panels', 'personalize_tab_options');

/**
 * Process meta
 * 
 * Processes the custom tab options when a post is saved
 */
function process_product_meta_personalize_tab($post_id) {
    update_post_meta($post_id, 'personalize', $_POST['personalize']);
    update_post_meta($post_id, 'a_product_id', $_POST['a_product_id']);
    update_post_meta($post_id, 'a_template_id', $_POST['a_template_id']);
}

add_action('woocommerce_process_product_meta', 'process_product_meta_personalize_tab');

function admin_enqueue($hook) {
    wp_enqueue_style('print_science', plugins_url() . '/print_science_designer/css/admin.css');
    wp_enqueue_script('function', plugins_url() . '/print_science_designer/js/admin.js', array(), '1.0.0', true);
}

add_action('admin_enqueue_scripts', 'admin_enqueue');


/** frontend
 *
 * add script 	   
 */
add_action('wp_enqueue_scripts', 'pz_scripts');

function pz_scripts() {
    wp_enqueue_style('print_science', plugins_url() . '/print_science_designer/css/style.css');
    wp_enqueue_style('modalPopLite', plugins_url() . '/print_science_designer/css/modalPopLite.css');
    wp_enqueue_script('modalPopLite.min', plugins_url() . '/print_science_designer/js/modalPopLite.min.js', array(), '1.0.0', true);

    wp_enqueue_script('function', plugins_url() . '/print_science_designer/js/function.js', array(), '1.0.0', true);
}

add_action('wp_enqueue_scripts', 'lightbox');

function lightbox() {
    global $woocommerce;
    $suffix = defined('SCRIPT_DEBUG') && SCRIPT_DEBUG ? '' : '.min'; {
        wp_enqueue_script('prettyPhoto', $woocommerce->plugin_url() . '/assets/js/prettyPhoto/jquery.prettyPhoto' . $suffix . '.js', array('jquery'), $woocommerce->version, true);
        wp_enqueue_script('prettyPhoto-init', $woocommerce->plugin_url() . '/assets/js/prettyPhoto/jquery.prettyPhoto.init' . $suffix . '.js', array('jquery'), $woocommerce->version, true);
        wp_enqueue_style('woocommerce_prettyPhoto_css', $woocommerce->plugin_url() . '/assets/css/prettyPhoto.css');
    }
}

add_filter('wp_footer', 'model_div');

function model_div() {
    $successUrl = serverURL() . $_SERVER['REQUEST_URI'];
    if ($_REQUEST['r'] == 's') {
        $successUrl = remove_query_arg(array('r'), $successUrl);

        /* if(isset($_REQUEST['variation_id']) && $_REQUEST['variation_id']!=''){
          $successUrl=  remove_query_arg(array('variation_id'),$successUrl);
          } */
    }
    if ($_REQUEST['r'] == 'e') {
        $successUrl = remove_query_arg(array('r'), $successUrl);
    }
    if ($_REQUEST['cancel'] == '1') {
        $successUrl = remove_query_arg(array('cancel'), $successUrl);
    }
    if ($_REQUEST['fail'] == '1') {
        $successUrl = remove_query_arg(array('fail'), $successUrl);
        $successUrl = add_query_arg(array('wc_error' => 'API is unable to connect'), $successUrl);
    }
    if (isset($_REQUEST['re_edit'])) {
        $successUrl = remove_query_arg(array('re_edit'), $successUrl);
        $successUrl = remove_query_arg(array('cart_item_key'), $successUrl);
        /* if(isset($_REQUEST['variation_id']) && $_REQUEST['variation_id']!=''){
          $successUrl=  remove_query_arg(array('variation_id'),$successUrl);
          } */
    }
    global $wpdb;
    $opacity = "0.6";
    $background_color = "#000000";
    $margin = 12;
    $api_info_table = $wpdb->prefix . 'api_info';
    $arr_api_info = $wpdb->get_results('SELECT * FROM ' . $api_info_table . ' where id=1');
    $image_url = $arr_api_info[0]->image_url;
    $background_color = $arr_api_info[0]->background_color;
    $opacity = $arr_api_info[0]->opacity;
    $margin = $arr_api_info[0]->margin;
    $serverURL = serverURL();
    echo '<style>
		.modalPopLite-mask {
			background-color:#' . $background_color . ' !important;
		}	
		#popup-wrapper
		{
			width:1150px;
			height:600px;
			left:0!important;
			top:30!important;
			background-color: #' . $background_color . ' !important;
		}
		.modalPopLite-wrapper
		{
			border:none!important;	
		}
		.modalPopLite-mask {
			opacity:' . $opacity . ' !important;
		}
		#popup_frame{
			border:0px;
		}	
		</style><a id="close-btn" ></a><input type="hidden" name="host" id="host" value="' . serverURL() . '"/><input type="hidden" name="server_url" id="server_url" value="' . $successUrl . '"/><input type="hidden" name="margin" id="margin" value="' . $margin . '"/><div id="popup-wrapper"><iframe id="popup_frame" name="popup_frame" style="width: 1399px; height: 716px;" src=""></iframe></div>';
}

/* * * Change text of add to cart for personalization
 *
 */

add_filter('add_to_cart_text', 'woo_custom_cart_button_text'); // < 2.1
add_filter('woocommerce_product_add_to_cart_text', 'woo_custom_cart_button_text'); // 2.1 +
add_filter('woocommerce_product_single_add_to_cart_text', 'woo_custom_cart_button_text'); // 2.1 +

function woo_custom_cart_button_text() {
    global $post;

    $custom_tab_options = array(
        'personalize' => get_post_meta($post->ID, 'personalize', true),
        'a_product_id' => get_post_meta($post->ID, 'a_product_id', true),
    );

    if ($custom_tab_options['personalize'] == 'y') {
        return 'Personalize';
    } else {
        return __('Add to cart', 'woocommerce');
    }
}

add_filter('single_add_to_cart_text', 'woo_custom_cart_button_text');

/** Change class of add to cart for personalization
 * 
 * 
 *
 */
add_filter('add_to_cart_class', 'woo_custom_cart_button_class');

function woo_custom_cart_button_class() {

    global $post;
    global $wpdb;
    $api_info_table = $wpdb->prefix . 'api_info';
    $custom_tab_options = array(
        'personalize' => get_post_meta($post->ID, 'personalize', true),
        'a_product_id' => get_post_meta($post->ID, 'a_product_id', true),
    );
    $arr_api_info = $wpdb->get_results('SELECT window_type FROM ' . $api_info_table . ' where id=1');
    $window_type = $arr_api_info[0]->window_type;

    $arr_types = array('New Window', 'Modal Pop-up window');

    if ($custom_tab_options['personalize'] == 'y') {
        if ($window_type == 'Modal Pop-up window') {
            return 'personalizep';
        } else {
            return 'personalize';
        }
    } else {
        return 'add_to_cart_button';
    }
}

/**
 * 
 * Change text of add to cart into personalization
 *
 */
// add_filter('woocommerce_add_to_cart_message', 'on_add_cart');	


add_action('init', 'on_add_cart', 10);

function on_add_cart() {
    global $wpdb;
    if ((isset($_REQUEST['product']) || isset($_REQUEST['post_type'])) && !isset($_REQUEST['add-to-cart']) && !isset($_REQUEST['add'])) {
        // add_filter( 'wp_footer' , 'model_div' );
    }
    $api_info_table = $wpdb->prefix . 'api_info';
    $arr_api_info = $wpdb->get_results('SELECT window_type FROM ' . $api_info_table . ' where id=1');
    $window_type = $arr_api_info[0]->window_type;
    if (isset($_REQUEST['re_edit']) && $_REQUEST['re_edit'] != '') {
        if (isset($_REQUEST['variation_id']) && $_REQUEST['variation_id'] != '') {
            do_action('revise_api_content', $_REQUEST['re_edit'], $_REQUEST['variation_id'], $_REQUEST['cart_item_key']);
        } else {
            do_action('revise_api_content', $_REQUEST['re_edit'], 0, $_REQUEST['cart_item_key']);
        }
    }

    //replace class of button on product detail page
    if ($window_type != 'New Window') {
        add_filter('wp_head', 'personalize_script');
    }

    if (!isset($_REQUEST['a'])) {
        $product_id = $_REQUEST['add-to-cart'];

        $custom_tab_options = array(
            'personalize' => get_post_meta($product_id, 'personalize', true),
            'a_product_id' => get_post_meta($product_id, 'a_product_id', true),
        );
        if ($custom_tab_options['personalize'] == 'n') {
            if (isset($_REQUEST['add-to-cart'])) {


                if (isset($_SESSION['pro_' . $_REQUEST['add-to-cart'] . '_' . $_REQUEST['variation_id']])) {
                    unset($_SESSION['pro_' . $_REQUEST['add-to-cart'] . '_' . $_REQUEST['variation_id']]);
                } else {
                    if (isset($_SESSION['pro_' . $_REQUEST['add-to-cart']])) {
                        unset($_SESSION['pro_' . $_REQUEST['add-to-cart']]);
                    }
                }
            }
        } else {
            if ((isset($_REQUEST['add-to-cart']) || isset($_REQUEST['added-to-cart']) ) && !isset($_REQUEST['add']) && !isset($_REQUEST['q']) && (!isset($_REQUEST['personalize']))) {
                $serverURL = serverURL() . $_SERVER['REQUEST_URI'];

                if (isset($_REQUEST['add-to-cart'])) {
                    $successUrl = remove_query_arg(array('add-to-cart'), $serverURL);
                    $product_id = $_REQUEST['add-to-cart'];
                }
                if (isset($_REQUEST['added-to-cart'])) {
                    $successUrl = remove_query_arg(array('added-to-cart'), $serverURL);
                    $product_id = $_REQUEST['added-to-cart'];
                }
                $successUrl = add_query_arg(array('add' => $product_id), $successUrl);
                if (isset($_REQUEST['quantity']) && $_REQUEST['quantity'] != '') {
                    $successUrl = add_query_arg(array('q' => $_REQUEST['quantity']), $successUrl);
                }
                foreach ($_REQUEST as $valuea => $valueV) {
                    if ($valuea != 'product' && $valuea != 'quantity' && $valuea != 'add-to-cart' && $valuea != 'product_id') {
                        $successUrl = add_query_arg(array($valuea => $valueV), $successUrl);
                    }
                }
                wp_redirect($successUrl);
                exit;
            }

            if (isset($_REQUEST['add']) && $_REQUEST['add'] != '' && (!isset($_REQUEST['add-to-cart']) || !isset($_REQUEST['added-to-cart'])) && (!isset($_REQUEST['personalize']))) {
                $serverURL = serverURL();
                //die('2');	
                $successUrl = remove_query_arg(array('add'), $serverURL . $_SERVER['REQUEST_URI']);

                $product_id = $_REQUEST['add'];
                $custom_tab_options = array(
                    'personalize' => get_post_meta($product_id, 'personalize', true),
                    'a_product_id' => get_post_meta($product_id, 'a_product_id', true),
                );


                if ($custom_tab_options['personalize'] == 'y') {
                    if ($window_type == 'New Window') {    //new window on personalize click
                        $serverURL = serverURL();
                        $successUrl = add_query_arg(array('ps_product_id' => $product_id, 'a' => 'w'), $serverURL . $_SERVER['REQUEST_URI']);
                        wp_redirect($successUrl);
                        exit;
                    } else {
                        $serverURL = serverURL();
                        $successUrl = add_query_arg(array('ps_product_id' => $product_id, 'a' => 'w'), $serverURL . $_SERVER['REQUEST_URI']);
                        wp_redirect($successUrl);
                        exit;
                    }
                }
            }

            //add_filter('add_to_cart_redirect', 'add_to_cart_redirect');
            add_filter('wc_add_to_cart_message', 'custom_add_to_cart_message');
            if ($window_type == 'Modal Pop-up window') {
                $custom_redirect = $_GET['custom'];
                $request_url = $_SERVER['REQUEST_URI'];
                if ($custom_redirect != '1' && $_GET['add'] > 0) {
                    ?>
                    <script>
                        window.parent.location = '<?php echo $request_url; ?>&custom=1';

                    </script>
                    <?php
                    exit;
                }
            }
        }
    }
}

function personalize_script() {
    echo "<script> jQuery(document).ready(function($) {jQuery.each(jQuery('.product_type_simple'), function() {       if(jQuery(this).html()=='Personalize'){   jQuery(this).removeClass('add_to_cart_button').addClass('personalizep'); }});if(jQuery('.cart .button').html()=='Personalize'){
	  jQuery('.cart .single_add_to_cart_button').removeClass('single_add_to_cart_button').addClass('personalizep');}else if(jQuery('.cart .button').html()=='Add to cart'){jQuery('.cart .personalizep').removeClass('personalizep').addClass('single_add_to_cart_button');}});</script>";
}

function popup_script() {
    
}

function add_to_cart_redirect($url) {
    global $woocommerce;   // If product is of the subscription type
    if (is_numeric($_REQUEST['add-to-cart'])) {  // Remove default cart message
        //$woocommerce->clear_messages();  // Redirect to checkout<br /><br />
        $url = add_query_arg(array('r' => 's', 'ps_product_id' => $_REQUEST['personalize'], 'variation_id' => $_REQUEST['variation_id']), $woocommerce->cart->get_cart_url());
    }
    return $url;
}

function open_on_ini() {
    global $wpdb;
    $api_info_table = $wpdb->prefix . 'api_info';
    // code to initiate request for api
    if (isset($_REQUEST['a'])) {
        $product_id = $_REQUEST['ps_product_id'];
        $personalize = get_post_meta($product_id, 'personalize', true);
        $templateId = get_post_meta($product_id, 'a_product_id', true);
        $Template_ID = get_post_meta($product_id, 'a_template_id', true);
        if ($Template_ID != '') {
            $TemplatexML = php_xmlrpc_encode($Template_ID);
        }
        $arr_api_info = $wpdb->get_results('SELECT * FROM ' . $api_info_table . ' where id=1');
        $username = $arr_api_info[0]->username;
        $api_key = $arr_api_info[0]->api_key;
        $apiVersion = $arr_api_info[0]->version;
        $apiUrl = $arr_api_info[0]->url;
        $image_url = $arr_api_info[0]->image_url;
        $window_type = $arr_api_info[0]->window_type;
        $background_color = $arr_api_info[0]->background_color;
        $opacity = $arr_api_info[0]->opacity;
        $margin = $arr_api_info[0]->margin;
        $serverURL = serverURL();
        $successUrl = remove_query_arg(array('ps_product_id', 'a'), $serverURL . $_SERVER['REQUEST_URI']);
        //$successUrl=add_query_arg(array('r'=>'s','personalize'=>$product_id),$successUrl);
        $successUrl = add_query_arg(array('personalize' => $product_id), $successUrl); //die();
        $successUrl = add_query_arg(array('add-to-cart' => $_REQUEST['ps_product_id'], 'add' => $_REQUEST['ps_product_id']), $successUrl);
        if (!isset($_REQUEST['q']) && $_REQUEST['q'] == '') {
            $successUrl = add_query_arg(array('q' => 1), $successUrl);
            $successUrl = add_query_arg(array('quantity' => 1), $successUrl);
        } else {
            $successUrl = add_query_arg(array('quantity' => $_REQUEST['q']), $successUrl);
        }
        //echo $successUrl;

        $failUrl = remove_query_arg(array('add', 'q', 'ps_product_id', 'a'), $serverURL . $_SERVER['REQUEST_URI']);
        $failUrl = add_query_arg(array('fail' => '1'), $failUrl);

        $cancelUrl = remove_query_arg(array('add', 'q', 'ps_product_id', 'a'), $serverURL . $_SERVER['REQUEST_URI']);
        $cancelUrl = add_query_arg(array('cancel' => '1'), $cancelUrl);

        $client = new xmlrpc_client($apiUrl);
        $function = null;
        $user_id = 0;
        $user_id = get_current_user_id();
        $comment = '';
        if ($user_id > '0') {
            $comment = '"User: "' . $user_id;
        }

        switch ($apiVersion) {
            case '1.0.0':
                $function = new xmlrpcmsg('beginPersonalization', array(
                    php_xmlrpc_encode($username),
                    php_xmlrpc_encode($api_key),
                    php_xmlrpc_encode($templateId),
                    php_xmlrpc_encode($successUrl),
                    php_xmlrpc_encode($failUrl),
                    php_xmlrpc_encode($cancelUrl),
                    php_xmlrpc_encode($comment)
                ));
                break;
            case '2.0.0':
                $function = new xmlrpcmsg('beginPersonalization', array(
                    php_xmlrpc_encode($username),
                    php_xmlrpc_encode($api_key),
                    php_xmlrpc_encode($templateId),
                    php_xmlrpc_encode($successUrl),
                    php_xmlrpc_encode($failUrl),
                    php_xmlrpc_encode($cancelUrl),
                    php_xmlrpc_encode($comment),
                    php_xmlrpc_encode('en'),
                    $TemplatexML,
                    php_xmlrpc_encode('')
                ));
                break;
            case '4.0.0':
                $function = new xmlrpcmsg('beginPersonalization', array(
                    php_xmlrpc_encode($username),
                    php_xmlrpc_encode($api_key),
                    php_xmlrpc_encode($templateId),
                    php_xmlrpc_encode($successUrl),
                    php_xmlrpc_encode($failUrl),
                    php_xmlrpc_encode($cancelUrl),
                    php_xmlrpc_encode($comment),
                    php_xmlrpc_encode('en'),
                    $TemplatexML
                ));
                break;
            default:
                $function = new xmlrpcmsg('beginPersonalization', array(
                    php_xmlrpc_encode($username),
                    php_xmlrpc_encode($api_key),
                    php_xmlrpc_encode($templateId),
                    php_xmlrpc_encode($successUrl),
                    php_xmlrpc_encode($failUrl),
                    php_xmlrpc_encode($cancelUrl),
                    php_xmlrpc_encode($comment),
                    php_xmlrpc_encode('en'),
                    $TemplatexML
                ));
                break;
        }
        $response = $client->send($function);

        $sessionkey = $response->value()->arrayMem(0)->scalarval();
        $preview_url = $response->value()->arrayMem(1)->scalarval();
        $_SESSION['product_id'] = $_REQUEST['ps_product_id'];
        $_SESSION['sessionkey'] = $sessionkey;

        wp_redirect($preview_url);
        exit;
    }


    if ($_REQUEST['r'] == 's') {
        $product_id = $_SESSION['product_id'];
        $session_key = $_SESSION['sessionkey'];

        unset($_SESSION['product_id']);
        unset($_SESSION['sessionkey']);
        unset($_SESSION['pro_']);
        if ($product_id != '') {

            if (isset($_REQUEST['variation_id']) && $_REQUEST['variation_id'] != '') {
                /* $unique_cart_item_key = md5(microtime().rand()."Hi Mom!");
                  $_SESSION['pro_'.$product_id.'_'.$_REQUEST['variation_id']]=$session_key;
                  $_SESSION['unique_cart_item_key_'.$product_id]=$unique_cart_item_key; */
            } else {
                //$_SESSION['pro_'.$product_id]=$session_key;
            }
        }


        add_filter('wp_head', 'close_div');
    }
    if ($_REQUEST['r'] == 'e') {

        add_filter('wp_head', 'close_div');
    }
    if ($_REQUEST['cancel'] == '1') {

        add_filter('wp_head', 'close_div');
    }
    if (isset($_REQUEST['fail'])) {

        add_filter('wp_head', 'close_div');
    }
}

function close_div() {
    global $wpdb;
    $api_info_table = $wpdb->prefix . 'api_info';
    $arr_api_info = $wpdb->get_results('SELECT window_type FROM ' . $api_info_table . ' where id=1');
    $window_type = $arr_api_info[0]->window_type;
    if ($window_type != 'New Window') {
        echo "<script>jQuery(document).ready(function($) {closethepopup();});</script>";
    }
}

add_action('init', 'open_on_ini');

function serverURL() {
    $pageURL = 'http';
    if ($_SERVER["HTTPS"] == "on") {
        $pageURL .= "s";
    }
    $pageURL .= "://";
    if ($_SERVER["SERVER_PORT"] != "80") {
        $pageURL .= $_SERVER["SERVER_NAME"] . ":" . $_SERVER["SERVER_PORT"];
    } else {
        $pageURL .= $_SERVER["SERVER_NAME"];
    }
    return $pageURL;
}

/* * overwrite display of cart* */
add_filter('woocommerce_before_cart_contents', 'cart_product_pz');

function cart_product_pz() {
    global $woocommerce;
    global $wpdb;
    //priyanka

    foreach ($woocommerce->cart->get_cart() as $cart_item_key => $values) {

        $_product = $values['data'];
        if ($_product->exists() && $values['quantity'] > 0) {
            ?>

            <tr class = "<?php echo esc_attr(apply_filters('woocommerce_cart_table_item_class', 'cart_table_item_print', $values, $cart_item_key)); ?>">
                <!-- Remove from cart link -->
                <td class="product-remove">
                    <?php
                    echo apply_filters('woocommerce_cart_item_remove_link', sprintf('<a href="%s" class="remove" title="%s">&times;</a>', esc_url($woocommerce->cart->get_remove_url($cart_item_key)), __('Remove this item', 'woocommerce')), $cart_item_key);
                    ?>
                </td>

                <!-- The thumbnail -->
                <td class="product-thumbnail">
                    <?php
                    if ((isset($values['variation_id']) && $values['variation_id'] != '' && isset($_SESSION['pro_' . $values['product_id'] . '_' . $values['variation_id'] . '_' . $cart_item_key]) && $_SESSION['pro_' . $values['product_id'] . '_' . $values['variation_id'] . '_' . $cart_item_key] != '') || isset($_SESSION['pro_' . $values['product_id'] . '_' . $cart_item_key]) && $_SESSION['pro_' . $values['product_id'] . '_' . $cart_item_key] != '') {
                        if ((isset($values['variation_id']) && $values['variation_id'] != '')) {
                            do_action('get_response_from_api', 'imagepath', $_SESSION['pro_' . $values['product_id'] . '_' . $values['variation_id'] . '_' . $cart_item_key]);
                            $successUrl = add_query_arg(array('re_edit' => $values['product_id'], 'variation_id' => $values['variation_id'], 'cart_item_key' => $cart_item_key), $_SERVER['REQUEST_URI']);
                        } else {
                            do_action('get_response_from_api', 'imagepath', $_SESSION['pro_' . $values['product_id'] . '_' . $cart_item_key]);
                            $successUrl = add_query_arg(array('re_edit' => $values['product_id'], 'cart_item_key' => $cart_item_key), remove_query_arg(array('variation_id'), $_SERVER['REQUEST_URI']));
                        }





                        $api_info_table = $wpdb->prefix . 'api_info';
                        $arr_api_info = $wpdb->get_results('SELECT * FROM ' . $api_info_table . ' where id=1');
                        $window_type = $arr_api_info[0]->window_type;
                        if ($window_type == 'New Window') {
                            $classN = "personalize";
                        } else {
                            $classN = "personalizep";
                        }
                        ?>
                        <div class="editimagediv">
                            <a class="<?php echo $classN; ?> button product_type_simple" data-product_sku="" data-product_id="15" data-rel="nofollow" href="<?php echo $successUrl; ?>"> <?php echo _e('Re-edit', 'woocommerce'); ?></a>
                        </div>	
                    <?php } else { ?>
                        <?php
                        $thumbnail = apply_filters('woocommerce_in_cart_product_thumbnail', $_product->get_image(), $values, $cart_item_key);

                        if (!$_product->is_visible() || (!empty($_product->variation_id) && !$_product->parent_is_visible() )) {

                            echo $thumbnail;
                        } else {
                            echo '<a href="' . wp_get_attachment_url(get_post_thumbnail_id($values['product_id'])) . '" data-rel="prettyPhoto" rel="prettyPhoto">' . $thumbnail . '</a>';
                        }
                    }
                    ?>


                </td>

                <!-- Product Name -->
                <td class="product-name">
                    <?php
                    if (!$_product->is_visible() || (!empty($_product->variation_id) && !$_product->parent_is_visible() ))
                        echo apply_filters('woocommerce_in_cart_product_title', $_product->get_title(), $values, $cart_item_key);
                    else
                        printf('<a href="%s">%s</a>', esc_url(get_permalink(apply_filters('woocommerce_in_cart_product_id', $values['product_id']))), apply_filters('woocommerce_in_cart_product_title', $_product->get_title(), $values, $cart_item_key));

                    echo '<br/>';
                    // Meta data
                    echo $woocommerce->cart->get_item_data($values, true);

                    // Backorder notification
                    if ($_product->backorders_require_notification() && $_product->is_on_backorder($values['quantity']))
                        echo '<p class="backorder_notification">' . __('Available on backorder', 'woocommerce') . '</p>';
                    ?>

                </td>

                <!-- Product price -->
                <td class="product-price">
                    <?php
                    $product_price = get_option('woocommerce_tax_display_cart') == 'excl' ? $_product->get_price_excluding_tax() : $_product->get_price_including_tax();

                    echo apply_filters('woocommerce_cart_item_price_html', woocommerce_price($product_price), $values, $cart_item_key);
                    ?>
                </td>

                <!-- Quantity inputs -->
                <td class="product-quantity">
                    <?php
                    if ($_product->is_sold_individually()) {
                        $product_quantity = sprintf('1 <input type="hidden" name="cart[%s][qty]" value="1" />', $cart_item_key);
                    } else {

                        $step = apply_filters('woocommerce_quantity_input_step', '1', $_product);
                        $min = apply_filters('woocommerce_quantity_input_min', '', $_product);
                        $max = apply_filters('woocommerce_quantity_input_max', $_product->backorders_allowed() ? '' : $_product->get_stock_quantity(), $_product);

                        $product_quantity = sprintf('<div class="quantity"><input type="number" name="cart[%s][qty]" step="%s" min="%s" max="%s" value="%s" size="4" title="' . _x('Qty', 'Product quantity input tooltip', 'woocommerce') . '" class="input-text qty text" maxlength="12" /></div>', $cart_item_key, $step, $min, $max, esc_attr($values['quantity']));
                    }

                    echo apply_filters('woocommerce_cart_item_quantity', $product_quantity, $cart_item_key);
                    ?>
                </td>

                <!-- Product subtotal -->
                <td class="product-subtotal">
                    <?php
                    echo apply_filters('woocommerce_cart_item_subtotal', $woocommerce->cart->get_product_subtotal($_product, $values['quantity']), $values, $cart_item_key);
                    ?>
                </td>
            </tr>
            <script>jQuery(document).ready(function() {

                    var qtyname = 'cart[<?php echo $cart_item_key; ?>][qty]';
                    jQuery('[name="' + qtyname + '"]').change(function() {
                        var newquantity = jQuery('[name="' + qtyname + '"]').val();
                        jQuery('[name="' + qtyname + '"]').val(newquantity); //alert(jQuery('[name="'+qtyname+'"]').val());
                    });

                });
            </script>
            <?php
        }
    }
}

add_action('get_response_from_api', 'get_response_from_api', 1, 2);

function get_response_from_api($rtype, $sessionkey) {

    $imagePath = '';

    if ($sessionkey != '') {
        global $wpdb;
        $api_info_table = $wpdb->prefix . 'api_info';
        $arr_api_info = $wpdb->get_results('SELECT url,version FROM ' . $api_info_table . ' where id=1');
        $apiUrl = $arr_api_info[0]->url;
        $apiVersion = $arr_api_info[0]->version;
        $client = new xmlrpc_client($apiUrl);
        $function = null;
        $function = new xmlrpcmsg('getPreview', array(
            php_xmlrpc_encode($sessionkey)
        ));

        $response = $client->send($function);


        $previewUrls = array();
        $previewUrlMember = $response->value()->structMem('preview_url');
        switch ($apiVersion) {
            case '1.0.0':
                for ($i = 0; $i < $previewUrlMember->arraySize(); $i++) {
                    $temp = $previewUrlMember->arrayMem($i)->scalarval();
                    $temp1 = $temp[1]->scalarval();
                    $previewUrls[] = $temp1;
                }
                break;
            case '2.0.0':
                for ($i = 0; $i < $previewUrlMember->arraySize(); $i++) {
                    $temp = $previewUrlMember->arrayMem($i)->scalarval();
                    $temp1 = $temp[1]->scalarval();
                    $previewUrls[] = $temp1;
                }
                break;
            case '4.0.0':
                for ($i = 0; $i < $previewUrlMember->arraySize(); $i++) {
                    $temp = $previewUrlMember->arrayMem($i)->scalarval();
                    $previewUrls[] = $temp[1]->scalarval();
                }
                break;
            default:
                for ($i = 0; $i < $previewUrlMember->arraySize(); $i++) {
                    $previewUrls[] = $previewUrlMember->arrayMem($i)->scalarval();
                }
                break;
        }
        $pdf_file_url = $response->value()->structMem('pdf_url')->scalarval();

        $imagePath = $previewUrls[0];

        $arr_return = array('pdf_file_url' => $pdf_file_url, 'image_path' => $imagePath);
    }
    if ($rtype == 'imagepath') {

        echo '<a href="' . $imagePath . '" data-rel="prettyPhoto" rel="prettyPhoto"><img src="' . $imagePath . '"/></a>';
    }
    if ($rtype == 'pdf') {
        return $arr_return;
    }
}

add_action('revise_api_content', 'revise_api_content', 1, 3);

function revise_api_content($product_id, $variation_id, $cart_item_key) {

    if ($variation_id == 0) {
        $sessionKey = $_SESSION['pro_' . $product_id . '_' . $cart_item_key];
    } else {
        $sessionKey = $_SESSION['pro_' . $product_id . '_' . $variation_id . '_' . $cart_item_key];
    }

    global $wpdb;
    $api_info_table = $wpdb->prefix . 'api_info';
    $arr_api_info = $wpdb->get_results('SELECT * FROM ' . $api_info_table . ' where id=1');
    $apiUrl = $arr_api_info[0]->url;
    $apiVersion = $arr_api_info[0]->version;
    $username = $arr_api_info[0]->username;
    $api_key = $arr_api_info[0]->api_key;
    $client = new xmlrpc_client($apiUrl);
    $serverURL = serverURL();
    $successUrl1 = remove_query_arg(array('re_edit'), $serverURL . $_SERVER['REQUEST_URI']);
    $successUrl = add_query_arg(array('r' => 'e'), $successUrl1);
    $failUrl = add_query_arg(array('fail' => '1'), $successUrl1);
    $cancelUrl = add_query_arg(array('cancel' => '1'), $successUrl1);

    $user_id = 0;
    $user_id = get_current_user_id();
    $comment = '';
    if ($user_id > '0') {
        $comment = '"User: "' . $user_id;
    }

    $function = null;
    // check api version
    switch ($apiVersion) {
        case '1.0.0':
            $function = new xmlrpcmsg('resumePersonalization', array(
                php_xmlrpc_encode($username),
                php_xmlrpc_encode($api_key),
                php_xmlrpc_encode($sessionKey),
                php_xmlrpc_encode($templateId),
                php_xmlrpc_encode($successUrl),
                php_xmlrpc_encode($failUrl),
                php_xmlrpc_encode($cancelUrl),
                php_xmlrpc_encode($comment),
                $TemplatexML,
            ));
            break;
        case '2.0.0':
            $function = new xmlrpcmsg('resumePersonalization', array(
                php_xmlrpc_encode($username),
                php_xmlrpc_encode($api_key),
                php_xmlrpc_encode($sessionKey),
                php_xmlrpc_encode($templateId),
                php_xmlrpc_encode($successUrl),
                php_xmlrpc_encode($failUrl),
                php_xmlrpc_encode($cancelUrl),
                php_xmlrpc_encode($comment),
                $TemplatexML,
            ));
            break;
        case '4.0.0':
            $function = new xmlrpcmsg('resumePersonalization', array(
                php_xmlrpc_encode($username),
                php_xmlrpc_encode($api_key),
                php_xmlrpc_encode($sessionKey),
                php_xmlrpc_encode($templateId),
                php_xmlrpc_encode($successUrl),
                php_xmlrpc_encode($failUrl),
                php_xmlrpc_encode($cancelUrl),
                php_xmlrpc_encode($comment),
                $TemplatexML,
            ));
            break;
        default:
            $function = new xmlrpcmsg('resumePersonalization', array(
                php_xmlrpc_encode($username),
                php_xmlrpc_encode($api_key),
                php_xmlrpc_encode($sessionKey),
                php_xmlrpc_encode($templateId),
                php_xmlrpc_encode($successUrl),
                php_xmlrpc_encode($failUrl),
                php_xmlrpc_encode($cancelUrl),
                php_xmlrpc_encode($comment),
                $TemplatexML,
            ));
            break;
    }
    $response = $client->send($function);

    $sessionkey = $response->value()->arrayMem(0)->scalarval();
    $preview_url = $response->value()->arrayMem(1)->scalarval();
    if (isset($_REQUEST['variation_id']) && $_REQUEST['variation_id'] != '') {
        $_SESSION['pro_' . $product_id . '_' . $_REQUEST['variation_id'] . '_' . $_REQUEST['cart_item_key']] = $sessionkey;
    } else {
        $_SESSION['pro_' . $product_id . '_' . $_REQUEST['cart_item_key']] = $sessionkey;
    }

    wp_redirect($preview_url);
    exit;
}

add_action('woocommerce_add_order_item_meta', 'save_item_meta', 10, 3);

function save_item_meta($cart_item_data, $product, $cart_item_key) {
    global $wpdb;


    if ((isset($product['variation_id']) && isset($_SESSION['pro_' . $product['product_id'] . '_' . $product['variation_id'] . '_' . $cart_item_key]) && $_SESSION['pro_' . $product['product_id'] . '_' . $product['variation_id'] . '_' . $cart_item_key] != '' ) || (isset($_SESSION['pro_' . $product['product_id'] . '_' . $cart_item_key]) && $_SESSION['pro_' . $product['product_id'] . '_' . $cart_item_key] != '')) {

        if (isset($product['variation_id']) && $product['variation_id'] != '') {
            $arr_return = get_response_from_api('pdf', $_SESSION['pro_' . $product['product_id'] . '_' . $product['variation_id'] . '_' . $cart_item_key]);
        } else {
            $arr_return = get_response_from_api('pdf', $_SESSION['pro_' . $product['product_id'] . '_' . $cart_item_key]);
        }
        $pdflink = $arr_return['pdf_file_url'];
        $imagelink = $arr_return['image_path'];
        $wpdb->query("INSERT INTO " . $wpdb->prefix . "woocommerce_order_itemmeta ( order_item_id, meta_key, meta_value )	VALUES (" . $cart_item_data . ",'pdf_link','" . $pdflink . "')");
        $wpdb->query("INSERT INTO " . $wpdb->prefix . "woocommerce_order_itemmeta ( order_item_id, meta_key, meta_value )	VALUES (" . $cart_item_data . ",'image_link','" . $imagelink . "')");

        if (isset($product['variation_id']) && $product['variation_id'] != '') {
            unset($_SESSION['pro_' . $product['product_id'] . '_' . $product['variation_id'] . '_' . $cart_item_key]);
        } else {
            unset($_SESSION['pro_' . $product['product_id'] . '_' . $cart_item_key]);
        }
    }
    global $woocommerce;
}

//woocommerce_hidden_order_itemmeta
add_action('admin_menu', 'remove_post_custom_fields', 10);

function remove_post_custom_fields() {
    add_meta_box('woocommerce-order-items1', __('Order Items', 'woocommerce') . ' <span class="tips" data-tip="' . __('Note: if you edit quantities or remove items from the order you will need to manually update stock levels.', 'woocommerce') . '">[?]</span>', 'woocommerce_order_items_meta_box1', 'shop_order', 'normal', 'high');
}

add_action('woocommerce_order_items_meta_box1', 'woocommerce_order_items_meta_box1', 10);

function woocommerce_order_items_meta_box1($post) {

    global $wpdb, $thepostid, $theorder, $woocommerce;
    $fwoo_url = $woocommerce->plugin_url() . 'admin/post-types/writepanels/';
    if (!is_object($theorder))
        $theorder = new WC_Order($thepostid);

    $order = $theorder;

    $data = get_post_meta($post->ID);
    $data = get_post_meta($post->ID);
    ?>
    <div class="woocommerce_order_items_wrapper">
        <table cellpadding="0" cellspacing="0" class="woocommerce_order_items">
            <thead>
                <tr>
                    <th><input type="checkbox" class="check-column" /></th>
                    <th class="item" colspan="2"><?php _e('Item', 'woocommerce'); ?></th>

                    <?php do_action('woocommerce_admin_order_item_headers'); ?>

                    <?php if (get_option('woocommerce_calc_taxes') == 'yes') : ?>
                        <th class="tax_class"><?php _e('Tax Class', 'woocommerce'); ?>&nbsp;<a class="tips" data-tip="<?php _e('Tax class for the line item', 'woocommerce'); ?>." href="#">[?]</a></th>
                    <?php endif; ?>

                    <th class="quantity"><?php _e('Qty', 'woocommerce'); ?></th>

                    <th class="line_cost"><?php _e('Totals', 'woocommerce'); ?>&nbsp;<a class="tips" data-tip="<?php _e('Line subtotals are before pre-tax discounts, totals are after.', 'woocommerce'); ?>" href="#">[?]</a></th>

                    <?php if (get_option('woocommerce_calc_taxes') == 'yes') : ?>
                        <th class="line_tax"><?php _e('Tax', 'woocommerce'); ?></th>
                    <?php endif; ?>
                </tr>
            </thead>
            <tbody id="order_items_list">

                <?php
                // List order items
                $order_items = $order->get_items(apply_filters('woocommerce_admin_order_item_types', array('line_item', 'fee')));

                foreach ($order_items as $item_id => $item) {

                    switch ($item['type']) {
                        case 'line_item' :
                            $_product = $order->get_product_from_item($item);
                            $item_meta = $order->get_item_meta($item_id);

                            include( 'woo_include/order-item-html.php' );
                            break;
                        case 'fee' :
                            include('woo_include/order-fee-html.php' );
                            break;
                    }

                    do_action('woocommerce_order_item_' . $item['type'] . '_html', $item_id, $item);
                }
                ?>
            </tbody>
        </table>
    </div>

    <p class="bulk_actions">
        <select>
            <option value=""><?php _e('Actions', 'woocommerce'); ?></option>
            <optgroup label="<?php _e('Edit', 'woocommerce'); ?>">
                <option value="delete"><?php _e('Delete Lines', 'woocommerce'); ?></option>
            </optgroup>
            <optgroup label="<?php _e('Stock Actions', 'woocommerce'); ?>">
                <option value="reduce_stock"><?php _e('Reduce Line Stock', 'woocommerce'); ?></option>
                <option value="increase_stock"><?php _e('Increase Line Stock', 'woocommerce'); ?></option>
            </optgroup>
        </select>

        <button type="button" class="button do_bulk_action wc-reload" title="<?php _e('Apply', 'woocommerce'); ?>"><span><?php _e('Apply', 'woocommerce'); ?></span></button>
    </p>

    <p class="add_items">
        <select id="add_item_id" class="ajax_chosen_select_products_and_variations" multiple="multiple" data-placeholder="<?php _e('Search for a product&hellip;', 'woocommerce'); ?>" style="width: 400px"></select>

        <button type="button" class="button add_order_item"><?php _e('Add item(s)', 'woocommerce'); ?></button>
        <button type="button" class="button add_order_fee"><?php _e('Add fee', 'woocommerce'); ?></button>
    </p>
    <div class="clear"></div>
    <?php
}

function add_css_to_email() {
    echo '<style type="text/css">
		small { display:none !important;}
		</style>
		';
}

function pz_custom_variation_price_email($itemtable) {
    $itemtable = delete_all_between($itemtable);
    return $itemtable;
}

add_filter('woocommerce_email_order_items_table', 'pz_custom_variation_price_email');

function delete_all_between($string) {
    preg_match_all("'<small>(.*?)</small>'si", $string, $match);

    foreach ($match[1] as $textToDelete) {
        $string = str_replace($textToDelete, '', $string);
    }
    return $string;
}

add_filter('woocommerce_add_to_cart_item_data', 'namespace_force_individual_cart_items', 10, 3);
add_filter('woocommerce_add_cart_item_data', 'namespace_force_individual_cart_items', 10, 3);

function namespace_force_individual_cart_items($cart_item_data, $product_id, $variation_id) {
    global $woocommerce;

    $variation = '';
    $objcart = new WC_Cart();

    $added_to_cart = array();
    $adding_to_cart = get_product($product_id);
    $attributes = $adding_to_cart->get_attributes();

    $variations = array();
    $variation = get_product($variation_id);
    // Verify all attributes
    foreach ($attributes as $attribute) {
        if (!$attribute['is_variation']) {
            continue;
        }

        $taxonomy = 'attribute_' . sanitize_title($attribute['name']);

        if (isset($_REQUEST[$taxonomy])) {

            // Get value from post data
            // Don't use wc_clean as it destroys sanitized characters
            $value = sanitize_title(trim(stripslashes($_REQUEST[$taxonomy])));

            // Get valid value from variation
            $valid_value = $variation->variation_data[$taxonomy];

            // Allow if valid
            if ($valid_value == '' || $valid_value == $value) {
                if ($attribute['is_taxonomy']) {
                    $variations[$taxonomy] = $value;
                } else {
                    // For custom attributes, get the name from the slug
                    $options = array_map('trim', explode(WC_DELIMITER, $attribute['value']));
                    foreach ($options as $option) {
                        if (sanitize_title($option) == $value) {
                            $value = $option;
                            break;
                        }
                    }
                    $variations[$taxonomy] = $value;
                }
                continue;
            }
        }

        $all_variations_set = false;
    }






    $unique_cart_item_key = md5(microtime() . rand() . "Hi Mom!");
    $cart_item_data['unique_key'] = $unique_cart_item_key;
    $cart_id = $objcart->generate_cart_id($product_id, $variation_id, $variations, $cart_item_data);
    if ($variation_id == '') {

        $_SESSION['pro_' . $product_id . '_' . $cart_id] = $_SESSION['sessionkey'];
    } else {

        $_SESSION['pro_' . $product_id . '_' . $variation_id . '_' . $cart_id] = $_SESSION['sessionkey'];
    }

    return $cart_item_data;
}

/* * ***************If productID or TemplateID does not exist on the Designer server, trap error. Display error message and write error message to log.*********************** */

function CheckTemplateID() {

    if ($_POST['post_type'] == 'product') {

        global $wpdb;
        $postID = $_POST['post_ID'];
        $api_info_table = $wpdb->prefix . 'api_info';
        $arr_api_info = $wpdb->get_results('SELECT * FROM ' . $api_info_table . ' where id=1');
        $apiUrl = $arr_api_info[0]->url;
        $apiVersion = $arr_api_info[0]->version;
        $username = $arr_api_info[0]->username;
        $api_key = $arr_api_info[0]->api_key;
        $ProductID = get_post_meta($postID, 'a_product_id', true);
        $TemplateID = get_post_meta($postID, 'a_template_id', true);
        if ($TemplateID != '') {
            $TemplatexML = php_xmlrpc_encode($TemplateID);
        }

        $IsPersonalize = get_post_meta($postID, 'personalize', true);

        $client = new xmlrpc_client($apiUrl);
        $serverURL = serverURL();

        $function = null;
        // check api version
        if ($IsPersonalize == 'y') {
            switch ($apiVersion) {
                case '1.0.0':
                    $function = new xmlrpcmsg('beginPersonalization', array(
                        php_xmlrpc_encode($username),
                        php_xmlrpc_encode($api_key),
                        php_xmlrpc_encode($ProductID),
                        php_xmlrpc_encode(''),
                        php_xmlrpc_encode(''),
                        php_xmlrpc_encode(''),
                        php_xmlrpc_encode('')
                    ));
                    break;
                case '2.0.0':
                    $function = new xmlrpcmsg('beginPersonalization', array(
                        php_xmlrpc_encode($username),
                        php_xmlrpc_encode($api_key),
                        php_xmlrpc_encode($ProductID),
                        php_xmlrpc_encode(''),
                        php_xmlrpc_encode(''),
                        php_xmlrpc_encode(''),
                        php_xmlrpc_encode(''),
                        php_xmlrpc_encode('en'),
                        $TemplatexML
                    ));
                    break;
                case '4.0.0':
                    $function = new xmlrpcmsg('beginPersonalization', array(
                        php_xmlrpc_encode($username),
                        php_xmlrpc_encode($api_key),
                        php_xmlrpc_encode($ProductID),
                        php_xmlrpc_encode(''),
                        php_xmlrpc_encode(''),
                        php_xmlrpc_encode(''),
                        php_xmlrpc_encode(''),
                        php_xmlrpc_encode('en'),
                        $TemplatexML
                    ));
                    break;
                default:
                    $function = new xmlrpcmsg('beginPersonalization', array(
                        php_xmlrpc_encode($username),
                        php_xmlrpc_encode($api_key),
                        php_xmlrpc_encode($ProductID),
                        php_xmlrpc_encode(''),
                        php_xmlrpc_encode(''),
                        php_xmlrpc_encode(''),
                        php_xmlrpc_encode(''),
                        php_xmlrpc_encode(''),
                        $TemplatexML
                    ));
                    break;
            }
            $response = $client->send($function);
            $Error = '';
            if ($response->errno == '1000') {
                $Error = 'Product ID is invalid!';
            }
            if ($response->errno == '4') {
                $Error = 'Invalid Product ID or Template ID';
            }
            if ($response->errno == '4' || $response->errno == '1000') {
                update_option('my_admin_errors', $Error);
                error_log($Error, 0);
            }
        }
    }
}

add_action('save_post', 'CheckTemplateID');

function woocommerce_admin_notice_handler() {
    $errors = get_option('my_admin_errors');
    if ($errors) {
        echo '<div class="error"><p>' . $errors . '</p></div>';
    }
    update_option('my_admin_errors', "");
}

add_action('admin_notices', 'woocommerce_admin_notice_handler');

function custom_add_to_cart_message() {
    global $woocommerce;

    $return_to = get_permalink(woocommerce_get_page_id('shop'));
    $message = sprintf('<a href="%s" class="button wc-forwards">%s</a> %s', $return_to, __('Continue Shopping', 'woocommerce'), __('Product successfully added to your cart.', 'woocommerce'));
    return $message;
}
?>