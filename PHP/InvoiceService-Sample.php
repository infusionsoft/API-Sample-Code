<?php
##########################################################################################################
###                            Sample Created by Justin Morris on 7/9/08                               ###
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

##############################################
###   Our Function to add an order item    ###
##############################################
function addOrderItem($oID, $pID, $Type, $Price, $qty, $desc, $notes) {
	global $client, $key;
###Set up the call to add an item to our order###
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
		print "Order Item added";
		print "<BR>";
		return $itemID;
	} else {
		print $result->faultCode() . "<BR>";
		print $result->faultString() . "<BR>";
	}
}
############################################
###   Function to create a blank order   ###
############################################
function buildOrder($cID, $desc, $oDate, $leadAff, $saleAff) {
	global $client, $key;
###Set up the call to add an order to a contact###
	$call = new xmlrpcmsg("InvoiceService.createBlankOrder", array(
		php_xmlrpc_encode($key), 				#The encrypted API key
		php_xmlrpc_encode($cID),				#The Order ID
		php_xmlrpc_encode($desc),				#The Product ID
		php_xmlrpc_encode($oDate, array('auto_dates')),		#The Type of Item
		php_xmlrpc_encode($leadAff),				#The price per item to sell.
		php_xmlrpc_encode($saleAff),				#Amount of the item to add.
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
	}
}

#############################################
###   Build the order and add the items   ###
#############################################
$dateObj = date('Ymd\TH:i:s');
$order = buildOrder(1300,"Sample Order",$dateObj,0,0);
addOrderItem($order,40,4,100.35,2,"Sample Item","Relax, its just an example!");
?>