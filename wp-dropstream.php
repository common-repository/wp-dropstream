<?php
/*
Plugin Name: WP-Dropstream
Plugin URI: http://getdropstream.com
Description: A brief description of the Plugin.
Version: 1.2.3
Author: Dropstream
Author URI: http://getdropstream.com
License: http://getdropstream.com/terms
*/
#error_reporting(0);

function _log_ds( $message ) {
  if( WP_DEBUG === true ){
    if( is_array( $message ) || is_object( $message ) ){
      error_log( print_r( $message, true ) );
    } else {
      error_log( $message );
    }
  }
}

/**
* Returns current plugin version.
*
* @return string Plugin version
*/
function plugin_get_version() {
  $plugin_data = get_plugin_data( __FILE__ );
  $plugin_version = $plugin_data['Version'];

  return $plugin_version;
}

class Dropstream {
  private static $instance = NULL;
  private function __construct() {
    add_filter( 'xmlrpc_methods', array($this, 'dropstream_add_xmlrpc_methods') );
  }
  
  public static function get_instance() {
    if(!isset(self::$instance)) {
      self::$instance = new Dropstream();
    }
    return self::$instance;
  }
  
  public function dropstream_ping($args) {
    global $wp_xmlrpc_server;
    $wp_xmlrpc_server->escape( $args );

    $username = $args['credentials']['username'];
    $password = $args['credentials']['password'];

    if ( ! $wp_xmlrpc_server->login( $username, $password ) ) {
      return $wp_xmlrpc_server->error;
    }
    
    $ping_data = array(
      'wp-dropstream-version' => plugin_get_version(),
      'phpversion' => phpversion(),
    );

    if(is_plugin_active('wp-e-commerce/wp-shopping-cart.php')) {
      $plugin_data = get_plugin_data( ABSPATH . 'wp-content/plugins/wp-e-commerce/wp-shopping-cart.php' );
      $ping_data['wp-e-commerce-version'] = $plugin_data['Version'];
    } elseif(is_plugin_active('woocommerce/woocommerce.php')) {
      $plugin_data = get_plugin_data( ABSPATH . 'wp-content/plugins/woocommerce/woocommerce.php' );
      $ping_data['woocommerce-version'] = $plugin_data['Version'];
    }
    return $ping_data;
  }
  
  public function dropstream_getOrders($args) {
    global $wp_xmlrpc_server;
    $wp_xmlrpc_server->escape( $args );

    $username = $args['credentials']['username'];
    $password = $args['credentials']['password'];

    if ( ! $wp_xmlrpc_server->login( $username, $password ) ) {
      return $wp_xmlrpc_server->error;
    }

    $status = $args['params']['status'];
    
    $created_after = NULL;
    if(array_key_exists('created_after', $args['params'])) {
      $created_after = $args['params']['created_after'];
    }
    
    _log_ds('Requesting orders. status='.$status.',created_after='.$created_after);

    return $this->get_adapter_instance()->getOrders($status, $created_after);
  }

  public function dropstream_updateOrderStatus($args) {
    global $wp_xmlrpc_server;
    $wp_xmlrpc_server->escape( $args );

    $username = $args['credentials']['username'];
    $password = $args['credentials']['password'];

    if ( ! $wp_xmlrpc_server->login( $username, $password ) ) {
      return $wp_xmlrpc_server->error;
    }
    
    $id = $args['params']['id'];
    $status = $args['params']['status'];

    return $this->get_adapter_instance()->updateOrderStatus($id, $status);
  }

  public function dropstream_createOrderTracking($args) {
    global $wp_xmlrpc_server;
    $wp_xmlrpc_server->escape( $args );

    $username = $args['credentials']['username'];
    $password = $args['credentials']['password'];

    if ( ! $wp_xmlrpc_server->login( $username, $password ) ) {
      return $wp_xmlrpc_server->error;
    }
    
    $id = $args['params']['id'];
    $tracking = $args['params']['tracking'];
    $carrier = $args['params']['carrier'];
    $service_level = array_key_exists('service_level', $args['params']) ? $args['params']['service_level'] : "";
    return $this->get_adapter_instance()->createOrderTracking($id, $tracking, $carrier, $service_level);
  }

  public function dropstream_updateProductInventory($args) {
    global $wp_xmlrpc_server;
    $wp_xmlrpc_server->escape( $args );

    $username = $args['credentials']['username'];
    $password = $args['credentials']['password'];

    if ( ! $wp_xmlrpc_server->login( $username, $password ) ) {
      return $wp_xmlrpc_server->error;
    }
    
    $product_id = $args['params']['product_id'];
    $sku = $args['params']['sku'];
    $quantity = $args['params']['quantity'];
    return $this->get_adapter_instance()->updateProductInventory($product_id, $sku, $quantity);
  }
  
  public function dropstream_updateProductInventoryBatch($args) {
    global $wp_xmlrpc_server;
    $wp_xmlrpc_server->escape( $args );

    $username = $args['credentials']['username'];
    $password = $args['credentials']['password'];

    if ( ! $wp_xmlrpc_server->login( $username, $password ) ) {
      return $wp_xmlrpc_server->error;
    }
    
    $items = $args['params']['items'];
    return $this->get_adapter_instance()->updateProductInventoryBatch($items);
  }

  public function dropstream_add_xmlrpc_methods( $methods ) {
    $methods['dropstream.ping'] = array($this, 'dropstream_ping');
    $methods['dropstream.getOrders'] = array($this, 'dropstream_getOrders');
    $methods['dropstream.updateOrderStatus'] = array($this, 'dropstream_updateOrderStatus');
    $methods['dropstream.createOrderTracking'] = array($this, 'dropstream_createOrderTracking');
    $methods['dropstream.updateProductInventory'] = array($this, 'dropstream_updateProductInventory');
    $methods['dropstream.updateProductInventoryBatch'] = array($this, 'dropstream_updateProductInventoryBatch');
    return $methods;
  }

  private function get_adapter_instance() {
    global $wp_xmlrpc_server;
    
    if(is_plugin_active('wp-e-commerce/wp-shopping-cart.php')) {
      
      require_once('inc/adapters/wp_e_commerce.php');
      return new WPECommerceAdapter();
    } elseif(is_plugin_active('woocommerce/woocommerce.php')) {
      
      require_once('inc/adapters/woocommerce.php');
      return new WoocommerceAdapter();
    }
    return $wp_xmlrpc_server->error; // TODO return a helpful message
  }

}

function register_woocommerce_pangolin_order_statuses() {
  # is_plugin_active is not available until after init
  # we use this check to avoid causing an error when not woocommerce_pangolin
  if(function_exists('register_post_status')) {
    register_post_status( 'wc-awaiting-shipment', array(
        'label'                     => _x( 'Awaiting fulfillment', 'Order status', 'woocommerce' ),
        'public'                    => true,
        'exclude_from_search'       => false,
        'show_in_admin_all_list'    => true,
        'show_in_admin_status_list' => true,
        'label_count'               => _n_noop( 'Awaiting fulfillment <span class="count">(%s)</span>', 'Awaiting fulfillment <span class="count">(%s)</span>', 'woocommerce' )
      ) );

    add_filter('wc_order_statuses', 'add_custom_woocommerce_order_statuses');
  }
}
add_action( 'init', 'register_woocommerce_pangolin_order_statuses' );

function install_dropstream() {
  # add custom acknowledgement status
  if(is_plugin_active('woocommerce/woocommerce.php')) {
    add_filter('woocommerce_reports_order_statuses', 'add_customs_order_statuses_to_woocommerce_reports');
    add_filter('wc_order_statuses', 'add_custom_woocommerce_order_statuses');
  }  
}
add_action( 'admin_init' , 'install_dropstream');

function load_dropstream() {
  $dropstream =  Dropstream::get_instance();
}
add_action( 'plugins_loaded' , 'load_dropstream');


function add_customs_order_statuses_to_woocommerce_reports($statuses) {
  if(is_array($statuses)) {
    array_push($statuses, 'awaiting-shipment');
  }
  return $statuses;
}

function add_custom_woocommerce_order_statuses($statuses) {
  $statuses['wc-awaiting-shipment']  = _x('Awaiting fulfillment', 'Order status', 'woocommerce');

  return $statuses;
}

?>
