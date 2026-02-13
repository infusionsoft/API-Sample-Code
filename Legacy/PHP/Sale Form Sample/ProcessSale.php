<?php
##########################################################################################################
###                            Sample Created by Justin Morris on 7/10/08                               ###
###        In this Sample we will use the InvoiceService to add an order to a contact record.          ###
##########################################################################################################

###Include our XMLRPC Library###
include("xmlrpc-2.0/lib/xmlrpc.inc");
###Set our Infusionsoft application as the client###
$client = new xmlrpc_client("https://mach2.infusionsoft.com/api/xmlrpc");
###Return Raw PHP Types###
$client->return_type = "phpvals";
###Dont bother with certificate verification###
$client->setSSLVerifyPeer(FALSE);
###Our API Key###
$key = "13643d3c8910057ce07623ed01bb22b2";

############################################
###   Function to create a new contact   ###
############################################
function addCon()
{
	global $client, $key;
	###Create our contact array###
	$contact = array(
		"FirstName"		=> $_POST['fName'],
		"LastName" 		=> $_POST['lName'],
		"Email" 		=> $_POST['eMail'],
		"StreetAddress1" 	=> $_POST['Street'],
		"City"		 	=> $_POST['City'],
		"State"		 	=> $_POST['State'],
		"PostalCode" 		=> $_POST['zip'],
	);
	
	###Build the Call###
	$call = new xmlrpcmsg("ContactService.add",array(
		php_xmlrpc_encode($key),
		php_xmlrpc_encode($contact),
	));
	
	###Send the call###
	$result=$client->send($call);
	
	if(!$result->faultCode()) {
		return $result->value();
	} else {
		echo "<script>alert('" . $result->faultString() . "');</script>";
		return "ERROR";
	}
	
}
###########################################
###   Function to add the Credit Card   ###
###########################################
function addCC($CID) {
	global $client,$key;
	
	###Create our card array###
	$card = array(
		"ContactId" 		=> $CID,
		"NameOnCard" 		=> $_POST['ccName'],
		"CardNumber"		=> $_POST['ccNum'],
		"CardType"		=> $_POST['ccType'],
		"CVV2"			=> $_POST['ccCode'],
		"ExpirationMonth"	=> $_POST['ccMonth'],
		"ExpirationYear"	=> $_POST['ccYear'],
	);
	
	###Set up the call###
	$call = new xmlrpcmsg("DataService.add",array(
		php_xmlrpc_encode($key),		#Our API KEy
		php_xmlrpc_encode("CreditCard"),	#The table we are adding to
		php_xmlrpc_encode($card),		#The data we are adding
		));
	
	###Send the call###
	$result = $client->send($call);
	
	###Check returned data###
	if (!$result->faultCode) {
		return $result->value();
	} else {
		echo "<script>alert('" . $result->faultString() . "');</script>";
		return "ERROR";
	}
}

############################################
###   Function to create a blank order   ###
############################################
function buildOrder($cID, $desc, $oDate, $leadAff, $saleAff) {
	global $client, $key;
###Set up the call to Create a blank order###
	$call = new xmlrpcmsg("InvoiceService.createBlankOrder", array(
		php_xmlrpc_encode($key), 				#The encrypted API key
		php_xmlrpc_encode($cID),				#The Contact ID
		php_xmlrpc_encode($desc),				#The Order Description
		php_xmlrpc_encode($oDate, array('auto_dates')),		#The Order Date
		php_xmlrpc_encode($leadAff),				#The Lead Affiliate ID
		php_xmlrpc_encode($saleAff),				#The Sale Affiliate ID
	));
###Send the call###
	$result = $client->send($call);

	if(!$result->faultCode()) {
		$ordID = $result->value();
		print "Blank order built - ID: " . $ordID;
		print "<BR>";
		return $ordID;
	} else {
		print $result->faultCode() . "<BR>";
		print $result->faultString() . "<BR>";
		return "ERROR";
	}
}
##############################################
###     Function to add an order item      ###
##############################################
function addOrderItem($oID, $pID, $Type, $Price, $qty, $desc, $notes) {
	global $client, $key;
###   Set up the call to add an item   ###
	$call = new xmlrpcmsg("InvoiceService.addOrderItem", array(
		php_xmlrpc_encode($key), 		#The encrypted API key
		php_xmlrpc_encode($oID),		#The Order ID
		php_xmlrpc_encode($pID),		#The Product ID
		php_xmlrpc_encode($Type),		#The Type of Item
		/*	Item Types: 	FINANCECHARGE = 6; PRODUCT = 4; SERVICE = 3;
					SHIPPING = 1; SPECIAL = 7; TAX = 2; UNKNOWN = 0; UPSELL = 5;
		*/
		php_xmlrpc_encode($Price),		#The price per item to sell.
		php_xmlrpc_encode($qty),		#Amount of the item to add.
		php_xmlrpc_encode($desc),		#Item Description
		php_xmlrpc_encode($notes),		#Item Notes
	));
###Send the call###
	$result = $client->send($call);

	if(!$result->faultCode()) {
		$itemID = $result->value();
		print "Order Item added: " . $desc;
		print "<BR>";
		return $itemID;
	} else {
		print $result->faultCode() . "<BR>";
		print $result->faultString() . "<BR>";
	}
}

########################
###   Let 'er Rip!   ###
########################

###   Here we build our posted contact data and collect the ID   ###
$conID = addCon();

if ($conID!="ERROR") {
###   Assuming everything worked lets add the credit card and collect the ID   ###
	$creditID = addCC($conID);
	if ($creditID!="ERROR") {
	###   Now if nothing errored, lets create a date object for today in the correct format   ###
		$dateObj = date('Ymd\TH:i:s');
	###   Then build the order and collect the ID   ###
		$order = buildOrder($conID,"Sample Order",$dateObj,0,0);
		if ($order!="ERROR") {
		###   If the order build was successful lets add our posted products.   ###
			if (isset($_POST['hotDog'])) {
				addOrderItem($order,41,4,2.00,1,"HOT DOG!","An All-Beef frank!");
				}
			if (isset($_POST['Burger'])) {
				addOrderItem($order,42,4,3.50,1,"Hamburger!","2 Buns and a patty!");
				}
			if (isset($_POST['Fries'])) {
				addOrderItem($order,43,4,1.75,1,"French Fries!","Potatoes Fried to perfection!");
				}
			if (isset($_POST['Drink'])) {
				addOrderItem($order,44,4,1.00,1,"Cold Drink!","Drink with NO free refills!");
				}
		}
	}
}





?>