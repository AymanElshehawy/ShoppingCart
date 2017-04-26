<?php

session_start();
include_once("config.php");

if (isset($_POST["submit"]) && $_POST["submit"] == 'Finish') {

    if (!empty($_SESSION["cart_products"])) {

        $sql = "INSERT INTO orders VALUES (NULL)";
        if ($mysqli->query($sql) === TRUE) {
            $order_id = $mysqli->insert_id;
            $subtotal=0;
            $order_total=0;
            foreach ($_SESSION["cart_products"] as $cart_itm) {
                if (count($cart_itm) > 3) {
                    //set variables to use in content below
                    $product_name = $cart_itm["product_name"];
                    $product_qty = $cart_itm["product_qty"];
                    $product_price = $cart_itm["product_price"];
                    $product_code = $cart_itm["product_code"];
                    $subtotal += ($product_price * $product_qty);
                    $order_items[] = "('$order_id','$product_code','$product_price','$product_qty',$subtotal)";
                }
            }
            $order_total=$subtotal;
            if($_POST['transport']>0){
                $order_total=$subtotal+$_POST['transport'];
                $sql = "UPDATE  orders set total=$order_total WHERE id='$order_total'";
//            exit($sql);
                $results = $mysqli->query($sql);
            }
            $sql = "INSERT INTO order_items (`order_id`, `code`,`price`,`qty`,`total`) VALUES " . implode(',', $order_items);
//            exit($sql);
            $results = $mysqli->query($sql);
            session_destroy();
            $urlfinish = urlencode($url="http://".$_SERVER['HTTP_HOST'].'/finish.php');
            // header('Location:' . "http://localhost:8080/phpCart/finish.php");
            header('Location:' . $urlfinish);
            exit;
        } else {
            echo "Error: " . $sql . "<br>" . $mysqli->error;
        }
    }
}
//add product to session or create new one
if (isset($_POST["type"]) && $_POST["type"] == 'add' && $_POST["product_qty"] > 0) {
    foreach ($_POST as $key => $value) { //add all post vars to new_product array
        $new_product[$key] = filter_var($value, FILTER_SANITIZE_STRING);
    }
    //remove unecessary vars
    unset($new_product['type']);
    unset($new_product['return_url']);

    //we need to get product name and price from database.
    $statement = $mysqli->prepare("SELECT product_name, price FROM products WHERE product_code=? LIMIT 1");
    $statement->bind_param('s', $new_product['product_code']);
    $statement->execute();
    $statement->bind_result($product_name, $price);

    while ($statement->fetch()) {

        //fetch product name, price from db and add to new_product array
        $new_product["product_name"] = $product_name;
        $new_product["product_price"] = $price;

        if (isset($_SESSION["cart_products"])) {  //if session var already exist
            if (isset($_SESSION["cart_products"][$new_product['product_code']])) //check item exist in products array
            {
                unset($_SESSION["cart_products"][$new_product['product_code']]); //unset old array item
            }
        }
        $_SESSION["cart_products"][$new_product['product_code']] = $new_product; //update or create product session with new item  
    }
}


//update or remove items 
if (isset($_POST["product_qty"]) || isset($_POST["remove_code"])) {
    //update item quantity in product session
    if (isset($_POST["product_qty"]) && is_array($_POST["product_qty"])) {
        foreach ($_POST["product_qty"] as $key => $value) {
            if (is_numeric($value)) {
                $_SESSION["cart_products"][$key]["product_qty"] = $value;
            }
        }
    }
    //remove an item from product session
    if (isset($_POST["remove_code"]) && is_array($_POST["remove_code"])) {
        foreach ($_POST["remove_code"] as $key) {
            unset($_SESSION["cart_products"][$key]);
        }
    }
}

//back to return url
$return_url = (isset($_POST["return_url"])) ? urldecode($_POST["return_url"]) : ''; //return url
header('Location:' . $return_url);

?>