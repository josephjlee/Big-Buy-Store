<?php

// Load classes
$file = preg_replace('/\.php$/', '', basename(__FILE__));
include ("inc/classload.inc.php");

// Access check
$acc = $user->account_type();

if ($acc < 2) {
	gotoPage("dashboard.php");
} else {
	include ("inc/class_seller.inc.php");
	$seller = new seller($db);
	$orders = $seller->getOrders();
}

// Process
if (isset($_POST["ship_order"])) {
	$error = array();
	if ($seller->shiporder($_POST["oid"], $_POST["pid"], $_POST["trackno_".$_POST["oid"].$_POST["pid"]])) {
		$orders = $seller->getOrders();
	}
}

// Include header template
include ("inc/header.inc.php");

?>

<div id = "maincontent">
	
	<div id = "submenu"> <?php dashnav($acc); ?> </div>
	
	<br/>
	
	<div id = "displaywindow">
	
	<?php
			
			if ($orders) {
				
				errorhandler();
				
			?>
				<div id = "divlabel">Pending orders</div>
				
				<table id="listings">
						<thead>
							<tr>
								<th>Invoice</th>
								<th>Placed</th>
								<th>Name</th>
								<th>Qty</th>
								<th>Total</th>
							</tr>
						</thead>
						<tbody>			
			
			<?php
				
				foreach ($orders as $order) {
					
					$p = product::getProductData($order["contains"]);
					
					echo "<tr>";
					
					echo 	"<td id = \"invoice\" data-title=\"Invoice\"><a href = \"orderdetails.php?id=".$order["oid"]."\">".$order["invoiceid"]."</a></td>";
					echo 	"<td id = \"orderplaced\" data-title=\"Placed\">".toDate($order["orderDate"], true)."</td>";
					echo 	"<td id = \"pname\" data-title=\"Name\">".$p["pname"]."</td>";
					echo 	"<td id = \"items\" data-title=\"Items\">".$order["units"]."</td>";
					echo	"<td id = \"total\" data-title=\"Total\">$".$order["totalunitprice"]."</td>";

					echo "</tr>";
					
					echo "<tr>";
					
					echo "<td id =\"\" colspan = 5>";		
			?>			
						<form action="pendingorders.php" method="post" id = "contentform">

							<input type="hidden" name="token" id = "csrftoken" value="<?php echo generateToken(30); ?>" />
							<input type="hidden" name="form" id = "csrfform" value="shiporder" />
							<input type="hidden" name="oid" id = "" value="<?php echo $order["oid"]; ?>" />
							<input type="hidden" name="pid" id = "" value="<?php echo $order["contains"]; ?>" />
							
							<div id ="label">Shipping Label</div><div id ="fieldreq"><input type="text" name="<?php echo "trackno_".$order["oid"].$order["contains"]; ?>" style = "<?php echo (isset($error["trackno_".$order["oid"].$order["contains"]]) ? "border:2px solid red;" : null); ?>" value = "<?php echo (isset($_POST["trackno_".$order["oid"].$order["contains"]]) ? cleanDisplay($_POST["trackno_".$order["oid"].$order["contains"]]) : null); ?>" /><span class = "detail"></span><?php echo (isset($error["trackno_".$order["oid"].$order["contains"]]) ? "<p class = \"inputerror\">".$error["trackno_".$order["oid"].$order["contains"]]."</p>" : null); ?><input type="submit" id="ship" name="ship_order" value="Ship" /></div>					
						
						</form>
			
			<?php
					
					echo "</td>";
					
					echo "</tr>";
					
				}
			
			?>
			
					</tbody>
				</table>
			
			<?php
				
			} else {
				
				$_SESSION["caution"] = "You have not placed any orders.";
				errorhandler();
				
			}
			
			?>
	</div>

</div>


<?php

// Include footer template
include ("inc/footer.inc.php");
	
?>
