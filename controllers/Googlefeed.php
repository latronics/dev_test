<?php

if (!defined('BASEPATH'))
    exit('No direct script access allowed');

class Googlefeed extends Controller {

    function index() {
        $this->load->helper('file');

        @unlink('feeds/gfeed.xml');
        //SET SHOP VARIABLES
        $shop_name = "365LaptopRepair";
        $shop_link = "https://www.365laptoprepair.com";


//GET PRODUCTS FROM DATABASE
        $this->db->join("warehouse", "warehouse.listingid = ebay.e_id");
        $this->db->where("ebay.price_ch1 <>", "");
        $this->db->where("ebay.e_title <>", "");
        $this->db->where("ebay.quantity >", 0);
        $this->db->where("ebay.price_ch1 <>", "500.00");
        $this->db->where("ebay.price_ch1 <>", "1000.00");
        $this->db->where("ebay.price_ch1 <>", "1500.00");
        $this->db->where("ebay.price_ch1 <>", "2000.00");
        $this->db->where("ebay.price_ch1 <>", "2500.00");
        $this->db->where("ebay.price_ch1 <>", "3000.00");
        $this->db->group_by("ebay.e_id");
        $products = $this->db->get("ebay")->result_object();

        $feed_products = [];

//LOOP THROUGH PRODUCTS
        foreach ($products as $products) {



            //CREATE EMPTY ARRAY FOR GOOGLE-FRIENDLY INFO 
            $gf_product = [];

            //FLAGS FOR LATER
//  //set True or False, depending on whether product is clothing3
//  $gf_product['is_clothing'] = False; 
//  
//  //set True or False depending on whether product is on sale
//  if(($products->p_visibility == 1) &&($products->p_quantity > 0) && ($products->p_price > 0))
//  {
//  $gf_product['is_on_sale'] = True; 
//  }
//  else
//  {
//    $gf_product['is_on_sale'] = False;  
//  }
            //feed attributes
            $gf_product['g:id'] = $products->e_id;
            $gf_product['g:sku'] = $products->e_id;
            $gf_product['g:title'] = $products->e_title;
            $gf_product['g:description'] = $products->e_desc;
            $gf_product['g:link'] = base_url() . "storeitem/" . $products->e_id;
            $gf_product['g:image_link'] = base_url() . "google_images/" . $products->idpath . "/" . $products->e_img1;
            $gf_product['g:shipping_weight'] = 0;

//CHECK IF THE FOLDER EXISTS
//$foldername = "google_images/" . $products->idpath;
//
//if (!file_exists($foldername)) {
//    echo "The folder $foldername has been created<br>";
//    mkdir($foldername);
//    //RESIZE IMAGES TO GOOGLESHOPPING
//      $this->resizer($products->e_img1, 250, 250, 250, 250, "ebay_images/" . $products->idpath, "google_images/".$products->idpath, 80); 
//}else
//{
//   //RESIZE IMAGES TO GOOGLESHOPPING
//      $this->resizer($products->e_img1, 250, 250, 250, 250, "ebay_images/" . $products->idpath, "google_images/".$products->idpath, 80); 
//}







            if (($products->quantity > 0) && ($products->price_ch1 > 0)) {
                $gf_product['g:availability'] = "in stock";
            } else if (($products->quantity <= 0) && ($products->price_ch1 > 0)) {
                $gf_product['g:availability'] = "out of stock";
            } else {
                $gf_product['g:availability'] = "out of stock";
            }

            $gf_product['g:price'] = $products->price_ch1;

            $gf_product['g:google_product_category'] = $products->gtaxonomy;
            $gf_product['g:brand'] = $products->e_manuf;

            $gf_product['g:gtin'] = $products->upc;


            $gf_product['g:mpn'] = $products->e_compat;
            if (($gf_product['g:gtin'] == "") && ($gf_product['g:mpn'] == "")) {
                $gf_product['g:identifier_exists'] = "no";
            };
            if ($products->Condition == 1000) {
                $gf_product['g:condition'] = 'NEW'; //must be NEW or USED
            } else {
                $gf_product['g:condition'] = 'USED'; //must be NEW or USED    
            }
            //remove this IF block if you don't sell any clothing
//  if ($gf_product['is_clothing']) {
//    $gf_product['g:age_group'] = $product['age_group']; //newborn/infant/toddle/kids/adult
//    $gf_product['g:color'] = $product['color'];
//    $gf_product['g:gender'] = $product['gender'];
//    $gf_product['g:size'] = $product['size'];
//  }
//  if ($gf_product['is_on_sale']) {
            $gf_product['g:sale_price'] = $products->price_ch1;
            $gf_product['g:sale_price_effective_date'] = date('Y-m-d') . " " . date('Y-m-d', strtotime("+365 days"));
//  }

            $feed_products[] = $gf_product;
        }





        $doc = new DOMDocument('1.0', 'UTF-8');

        $xmlRoot = $doc->createElement("rss");
        $xmlRoot = $doc->appendChild($xmlRoot);
        $xmlRoot->setAttribute('version', '2.0');
        $xmlRoot->setAttributeNS('http://www.w3.org/2000/xmlns/', 'xmlns:g', "http://base.google.com/ns/1.0");

        $channelNode = $xmlRoot->appendChild($doc->createElement('channel'));
        $channelNode->appendChild($doc->createElement('title', $shop_name));
        $channelNode->appendChild($doc->createElement('link', $shop_link));

        foreach ($feed_products as $product) {
            $itemNode = $channelNode->appendChild($doc->createElement('item'));
            foreach ($product as $key => $value) {
                if ($value != "") {
                    if (is_array($product[$key])) {
                        $subItemNode = $itemNode->appendChild($doc->createElement($key));
                        foreach ($product[$key] as $key2 => $value2) {
                            $subItemNode->appendChild($doc->createElement($key2))->appendChild($doc->createTextNode($value2));
                        }
                    } else {
                        $itemNode->appendChild($doc->createElement($key))->appendChild($doc->createTextNode($value));
                    }
                } else {

                    $itemNode->appendChild($doc->createElement($key));
                }
            }
        }

        $doc->formatOutput = true;
        $gfeed = $doc->saveXML(); // put string in gfeed
//$handle = fopen(base_url()."feeds/gfeed.xml", "w");

        if (write_file("feeds/gfeed.xml", $gfeed, 'a+')) {
            echo "Writed";
        } else {
            echo "failed";
        }


        //GET PRODUCTS FROM DATABASE
         $this->db->join("warehouse", "warehouse.listingid = ebay.e_id");
        $this->db->where("ebay.price_ch1 <>", "");
        $this->db->where("ebay.e_title <>", "");
        $this->db->where("ebay.quantity >", 0);
        $this->db->where("ebay.price_ch1 <>", "500.00");
        $this->db->where("ebay.price_ch1 <>", "1000.00");
        $this->db->where("ebay.price_ch1 <>", "1500.00");
        $this->db->where("ebay.price_ch1 <>", "2000.00");
        $this->db->where("ebay.price_ch1 <>", "2500.00");
        $this->db->where("ebay.price_ch1 <>", "3000.00");
        $this->db->group_by("ebay.e_id");
        $products_2 = $this->db->get("ebay");
        $products_2array = array(
            "products" => $products_2->result_object(),
            "rows" => $products_2->num_rows()
        );

        $this->load->view("google/gfeed", $products_2array);
        //$this->load->view("google/Resizeimages", $produtcts_2array);
    }

    function resizer($fileName, $maxWidth, $maxHeight, $fixedWidth, $fixedHeight, $oldDir, $newDir, $quality) {
        $file = $oldDir . '/' . $fileName;
        $fileDest = $newDir . '/' . $fileName;
        list($width, $height) = getimagesize($file);
        if ($fixedWidth) {
            $newWidth = $fixedWidth;
            @$newHeight = ($newWidth / $width) * $height;
        } elseif ($fixedHeight) {
            $newHeight = $fixedHeight;
            $newWidth = ($newHeight / $height) * $width;
        } elseif ($width < $height) {   // image is portrait
            $newHeight = $maxHeight;
            $newWidth = ($newHeight / $height) * $width;
        } elseif ($width > $height) {   // image is landscape
            $newWidth = $maxWidth;
            $newHeight = ($newWidth / $width) * $height;
        } else {         // image is square
            $newWidth = $maxHeight;
            $newHeight = $maxHeight;
        }
        $extn = strtolower(pathinfo($file, PATHINFO_EXTENSION));
        @$imageDest = imagecreatetruecolor($newWidth, $newHeight);
        // jpeg
        if ($extn == 'jpg' or $extn == 'jpeg') {
            $imageSrc = imagecreatefromjpeg($file);
            if (@imagecopyresampled($imageDest, $imageSrc, 0, 0, 0, 0, $newWidth, $newHeight, $width, $height)) {
                imagejpeg($imageDest, $fileDest, $quality);
                imagedestroy($imageSrc);
                imagedestroy($imageDest);
                return true;
            }
            return false;
        }
        // png
        if ($extn == 'png') {
            imagealphablending($imageDest, false);
            imagesavealpha($imageDest, true);
            $imageSrc = imagecreatefrompng($file);
            if (imagecopyresampled($imageDest, $imageSrc, 0, 0, 0, 0, $newWidth, $newHeight, $width, $height)) {
                imagepng($imageDest, $fileDest, ($quality / 10) - 1);
                imagedestroy($imageSrc);
                imagedestroy($imageDest);
                return true;
            }
            return false;
        }
    }

}
