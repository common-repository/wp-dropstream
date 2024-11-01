<?php
interface iAdapter {
  public function getOrders($status, $created_after);
  public function updateOrderStatus($id, $status);
  public function createOrderTracking($id, $tracking_number, $carrier, $service_level, $send_email = true);
  public function updateProductInventory($product_id, $sku, $quantity);
  public function updateProductInventoryBatch($items);
}

?>