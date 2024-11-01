<?php

require('adapter.php');

class WoocommerceAdapter implements iAdapter {
  
  public function getOrders($status, $updated_after) {

    $orders = array('orders' => array());

    $order_query = new WP_Query(array('post_type' => 'shop_order', 
                                      'post_status' => array('wc-'.$status), 'posts_per_page' => '-1',
    'date_query' => array(
        array(
          'column'    => 'post_modified_gmt',
          'after'     => $updated_after,
          'inclusive' => true,
        ),
      )));
    _log_ds('message="Executing query",query="'.$order_query->request.'"');

    if( $order_query->have_posts() ) {

      while ($order_query->have_posts()) : $order_query->the_post(); 

      $wc_order = self::format_order(WC_Order_Factory::get_order(get_the_ID()));

      $orders['orders'][] = $wc_order;
      endwhile;
    }

    _log_ds('message="Completed getOrders method"');
    return $orders;
   }
  
  public function updateOrderStatus($order_id, $status) {

    if(!($wc_order = WC_Order_Factory::get_order($order_id))) {
      return new IXR_Error( 404, __( 'Selected order could not be found' ) );
    }

    if($status == 'awaiting-fulfillment') {
      $status = 'wc-awaiting-shipment';
    }
    $result = $wc_order->update_status($status);

    _log_ds('message="updated order status",order_id='.$order_id.',status="'.$status.'"');

    return $result;
  }
  
  public function createOrderTracking($order_id, $tracking_number, $carrier, $service_level, $send_email = true) {
    if(!($wc_order = WC_Order_Factory::get_order($order_id))) {
      return new IXR_Error( 404, __( 'Selected order ['.$order_id.'] could not be found' ) );
    }
    
    if(!isset($tracking_number)) {
      return new IXR_Error( 500, __( 'Invalid Tracking Number' ) );
    }

    if(function_exists('wc_st_add_tracking_number')) {
      wc_st_delete_tracking_number($order_id, $tracking_number);
      wc_st_add_tracking_number($order_id, $tracking_number, strtolower($carrier), strtotime(date('Y-m-d')));  
    } else { 
      update_post_meta( $order_id, '_tracking_provider', strtolower($carrier) );
  		update_post_meta( $order_id, '_tracking_number', $tracking_number );
  		update_post_meta( $order_id, '_date_shipped', strtotime(date('Y-m-d')) ); // YYYY-MM-DD
    }
    update_post_meta( $order_id, '_tracking_provider_service_level', strtolower($service_level) );
    _log_ds('message="added tracking number",order_id='.$order_id.',tracking_number="'.$tracking_number.'"');

  }

  public function updateProductInventory($product_id, $sku, $quantity) {
    $product_id = self::get_product_id_by_sku($sku);

    if(!isset($product_id)) {
      return new IXR_Error( 404, __( 'Product SKU ['.$sku.'] not found.' ) );
    }

    wc_update_product_stock($product_id, $quantity);
    
    _log_ds('message="updated product stock",sku='.$sku.',quantity="'.$quantity.'"');

    return true;
  }
  
  public function updateProductInventoryBatch($items) {
    $results = array();

    foreach ($items as $item) {
      $sku = $item['sku'];
      $quantity = $item['quantity'];

      $product_id = self::get_product_id_by_sku($sku);
      if(isset($product_id)) {
        $results[] = array($sku => wc_update_product_stock($product_id, $quantity));
      } else {
        $results[] = array($sku => __( 'Product SKU ['.$sku.'] not found.' ));
      }
    }
    
    return $results;
  }  

  private static function get_product_id_by_sku( $sku ) {
    global $wpdb;

    return $wpdb->get_var( $wpdb->prepare( "SELECT post_id FROM $wpdb->postmeta JOIN $wpdb->posts ON $wpdb->posts.id = $wpdb->postmeta.post_id WHERE meta_key='_sku' AND meta_value='%s' AND post_status = 'publish' LIMIT 1", $sku ) );    
  }
  private static function get_product_by_sku( $sku ) {

    $product_id = self::get_product_id_by_sku($sku);

    if ( $product_id ) return wc_get_product( $product_id );

    return null;
  }
  
  private static function get_product_by_id( $id ) {
    return wc_get_product( $id );
  }
  
  private static function format_order($_order) {
  	$order = new stdClass;
    $id = method_exists( $_order, 'get_id' ) ? $_order->get_id() : $_order->id;
  	$order->id = $id;
  	if(is_plugin_active('woocommerce-sequential-order-numbers-pro/woocommerce-sequential-order-numbers-pro.php') ||
        is_plugin_active('wt-woocommerce-sequential-order-numbers-pro/wt-advanced-order-number-pro.php')) {
  	  $order->display_id = ltrim( $_order->get_order_number(), _x( '#', 'hash before order number', 'woocommerce' ) );
	  } else if(is_plugin_active('woocommerce-sequential-order-numbers/woocommerce-sequential-order-numbers.php') ||
                is_plugin_active('wt-woocommerce-sequential-order-numbers/wt-advanced-order-number.php')) {
      $order->display_id = ltrim( $_order->get_order_number(), _x( '#', 'hash before order number', 'woocommerce' ) );
    }
    _log_ds('message="Preparing to format order",order_id='.$id);

  	$order->status = $_order->get_status();
  	$order->email = $_order->get_billing_email();
  	$order->date = $_order->get_date_created() ? $_order->get_date_created()->__toString() : null;

  	$order->notes = $_order->get_customer_note();
  	$order->shipping_method = $_order->get_shipping_method();
    $order->shipping_amount = $_order->get_total_shipping();
    $order->shipping_tax = $_order->get_shipping_tax();
    $order->tax_amount = $_order->get_total_tax();
    $order->discount_amount = $_order->get_total_discount();
    $order->coupons = $_order->get_used_coupons();
    $order->payment_method = $_order->get_payment_method();
    $order->payment_date = $_order->get_date_paid() ? $_order->get_date_paid()->__toString() : null;
  	$order->currency_code = $_order->get_currency();
    
  	// billing address
  	$billing_address = new stdClass;
  	$billing_address->first_name = $_order->get_billing_first_name();
  	$billing_address->last_name = $_order->get_billing_last_name();
  	$billing_address->phone = $_order->get_billing_phone();
  	$billing_address->company = $_order->get_billing_company();
  	$billing_address->street = $_order->get_billing_address_1()."\r\n".$_order->get_billing_address_2();
    $billing_address->address1 = $_order->get_billing_address_1();
    $billing_address->address2 = $_order->get_billing_address_2();
  	$billing_address->city = $_order->get_billing_city();
  	$billing_address->state = $_order->get_billing_state();
  	$billing_address->zip = $_order->get_billing_postcode();
  	$billing_address->country = $_order->get_billing_country();
  	$order->billing_address = $billing_address;

  	// shipping address
  	$shipping_address = new stdClass;
    $shipping_address->first_name = $_order->get_shipping_first_name();
    $shipping_address->last_name =$_order->get_shipping_last_name();
    $shipping_address->phone = $_order->get_billing_phone();
    $shipping_address->company = $_order->get_shipping_company();
    $shipping_address->street = $_order->get_shipping_address_1()."\r\n".$_order->get_shipping_address_2();
    $shipping_address->address1 = $_order->get_shipping_address_1();
    $shipping_address->address2 = $_order->get_shipping_address_2();
    $shipping_address->city = $_order->get_shipping_city();
    $shipping_address->state = $_order->get_shipping_state();
    $shipping_address->zip = $_order->get_shipping_postcode();
    $shipping_address->country = $_order->get_shipping_country();
  	$order->shipping_address = $shipping_address;

  	$order->order_items = array();
  	foreach($_order->get_items() as $_item) {
  	  $product = self::get_product_by_id($_item['product_id']);
      
      if(is_null($product) || empty($product)) {
        _log_ds('message="WARNING: Could not find product",order_id='.$id.',product_id='.$_item['product_id']);
        continue;
      }

  		$item = new stdClass;
      $id = method_exists( $_item, 'get_id' ) ? $_item->get_id() : $_item->id;
      $item->order_item_id = $id;
  		$item->order_product_id = $_item['product_id'];
  		$item->name = $_item['name'];
  		$item->sku = self::format_order_item_sku($_item);
  		$item->quantity = $_item['qty'];
  		$item->price = $_order->get_item_total($_item, false, false);
  		$order->order_items[] = $item;

      _log_ds('message="Found order item",order_id='.$id.',sku='.$item->sku);
  	}

    _log_ds('message="Formatted order",order_id='.$id);
  	return apply_filters( 'wc_dropstream_order', $order);
  }
  
  private static function format_order_item_sku($item) {
    $product = self::get_product_by_id($item['product_id']);
    if($product->get_type() == 'simple' || $product->get_type() == 'subscription' || $product->get_type() == 'bundle') {
      return $product->get_sku();
    } else if($product->get_type() == 'variable' || $product->get_type() == 'variable-subscription') {
      $variation = new WC_Product_variation($item['variation_id']);
      
      return $product->get_sku()."".$variation->get_sku();
    }
    // group products can only contain simple products and will be caught above
    return NULL;
  }
  
}
