<?php

$this->db->select("*");
$test = $this->db->get("test")->result_array();
$x = unserialize($test[0]['field']);

print_r($x);