<?php

 $this->db->select("*");
            $this->db->join("ip_quotes", "ip_quotes.quote_id = ip_quote_items.quote_id");
            $this->db->join("ip_clients", "ip_clients.client_id = ip_quotes.client_id");
            $this->db->where("ip_quote_items.quote_id",37165);
            $product_data = $this->db->get("ip_quote_items")->result_array();
            
            
            
            foreach($product_data as $product_data){
           $post = array(
    'kp' => '/1=?6|[\zb+QQG&v>ZxS9n#r27 \p."UtpJr?!P-AOo%HW[}_m]T{\.}a?ZsVr~k]#wEgk6ry+R|9-!SDr*[R>I>ku23h9f[Pl?k)Rb+qx4O?ZOv-3O_(B&-e$o9b.jEk}xD_x:GU8T/hZvO0 `gLQaM/2aY%W#7MyHS`z2}6wH+j"gK-D$rA9KG3GhB;aBIW,lM@PQ$SL, rx:5t;3]{q;:8Ub>]w{&wX_a!H."(/zUeyY)6"{{**,j,',
    'un' => '365inpl_' . mktime(),
    'buytype' => $this->input->post('store'),
    'orderid' => '37165',
    'buyer' => $product_data['client_id'],
    'shipping' => $shipping,
    'bcn' => $product_data['item_product_id'],
    'product_qtd' => $product_data['item_quantity'],           
    'warehouse' => serialize(array(array('warehouseid' => 15535, 'pricesold' => $product_data['item_price']), array('warehouseid' => 15535, 'pricesold' => $product_data['item_price']), array('warehouseid' => 15535, 'pricesold' => $product_data['item_price'])))
);
           
            
            }
            


$ch = curl_init('http://www.dev.la-tronics.com/Mywarehouse/Comm');
curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
curl_setopt($ch, CURLOPT_POSTFIELDS, $post);


// execute!
$response = curl_exec($ch);

// close the connection, release resources used
curl_close($ch);

// do anything you want with your response
var_dump($response);

