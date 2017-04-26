<?php
session_start();
include_once("config.php");
?>
<!DOCTYPE html>
<html>
<head>
<meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
<title>View shopping cart</title>
<link href="style/style.css" rel="stylesheet" type="text/css"></head>
<script src="https://ajax.googleapis.com/ajax/libs/jquery/3.2.0/jquery.min.js"></script>
<body>
<h1 align="center">View Cart</h1>
<div class="cart-view-table-back">
<form method="post" action="cart_update.php" id="FormID">
<table width="100%"  cellpadding="6" cellspacing="0"><thead><tr><th>Quantity</th><th>Name</th><th>Price</th><th>Total</th><th>Remove</th></tr></thead>
  <tbody>
 	<?php
	if(isset($_SESSION["cart_products"])) //check session var
    {
		$total = 0; //set initial total value
		$b = 0; //var for zebra stripe table 
		foreach ($_SESSION["cart_products"] as $cart_itm)
        {
			//set variables to use in content below
			$product_name = $cart_itm["product_name"];
			$product_qty = $cart_itm["product_qty"];
			$product_price = $cart_itm["product_price"];
			$product_code = $cart_itm["product_code"];

			$subtotal = ($product_price * $product_qty); //calculate Price x Qty
			
		   	$bg_color = ($b++%2==1) ? 'odd' : 'even'; //class for zebra stripe 
		    echo '<tr class="'.$bg_color.'">';
			echo '<td><input type="text" size="2" maxlength="2" name="product_qty['.$product_code.']" value="'.$product_qty.'" /></td>';
			echo '<td>'.$product_name.'</td>';
			echo '<td>'.$currency.$product_price.'</td>';
			echo '<td>'.$currency.$subtotal.'</td>';
			echo '<td><input type="checkbox" name="remove_code[]" value="'.$product_code.'" /></td>';
            echo '</tr>';
			$total = ($total + $subtotal); //add subtotal to total var
        }
		
//		$grand_total = $total + $shipping_cost; //grand total including shipping cost
//		foreach($taxes as $key => $value){ //list and calculate all taxes in array
//				$tax_amount     = round($total * ($value / 100));
//				$tax_item[$key] = $tax_amount;
//				$grand_total    = $grand_total + $tax_amount;  //add tax val to grand total
//		}
//
//		$list_tax       = '';
//		foreach($tax_item as $key => $value){ //List all taxes
//			$list_tax .= $key. ' : '. $currency. sprintf("%01.2f", $value).'<br />';
//		}
//		$shipping_cost = ($shipping_cost)?'Shipping Cost : '.$currency. sprintf("%01.2f", $shipping_cost).'<br />':'';
	}
    ?>
    <tr>
    	<td colspan="5">
    		<span>Select Transport: </span>
    		<select name="transport" id="transport">
    			<option value="-1">Choose Transport</option>
    			<option value="0">Pick up 0$</option>
    			<option value="5">UPS 5$</option>
    		</select>
    	</td>
    </tr>

    <tr>
    	<td colspan="5">your cart total price : <span id="cart-total" style="float:right;text-align: right;"><?php echo $total; ?></span></td>
    </tr>
    <tr>
    	<td colspan="5">transport value : <span id="transport-value" style="float:right;text-align: right;"></span></td>
    </tr>
    <tr>
        <td colspan="5">your total balance is : <span style="float:right;text-align: right;"><?php echo "100$"; ?></span></td>
    </tr>
    <tr>
        <td colspan="5">your remaining balance is : <span id="remaining" style="float:right;text-align: right;"><?php echo 100-$total; ?></span>
        <span id="hiddenRemaining" style="display: none"><?php echo 100-$total; ?></span>
        </td>
    </tr>
    <tr>
    	<td colspan="5">
    		<a href="index.php" class="button">Add More Items</a>
    		<button type="submit" id="btnClick">Update</button>
    		<input type="submit" class="button" name="submit" value="Finish" id="finish">
    	</td>
    </tr>
  </tbody>
</table>
<input type="hidden" name="return_url" value="<?php 
$current_url = urlencode($url="http://".$_SERVER['HTTP_HOST'].$_SERVER['REQUEST_URI']);
echo $current_url; ?>" />
</form>
</div>

<script type="text/javascript">

jQuery(document).ready(function($){
    $("#transport").change(function () {
        var e = document.getElementById("transport");
        var remaining = document.getElementById("hiddenRemaining").innerHTML;
//        alert(remaining);
        var strUser = e.options[e.selectedIndex].value;
        // alert(strUser);
        if(strUser<=0){
            $('#transport-value').html('0$');
        }else{
            $('#transport-value').html('5$');
            remaining=remaining-5;
            document.getElementById("remaining").innerHTML=remaining;
        }
    }).trigger('change');

	$( "#FormID" ).submit(function( event ) {
		var e = document.getElementById("transport");
		var strUser = e.options[e.selectedIndex].value;
		// alert(strUser);
		if(strUser<0){ 
		    alert("Please Select Transport.");    
		}
	  // event.preventDefault();
	});

});
</script>
</body>
</html>
