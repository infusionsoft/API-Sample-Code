<?php
###########################################################################################################
###                           Sample Created by Justin Morris on 7/9/08                                 ###
###  In this Sample we will use the dataservice to create a credit card in Infusionsoft for a contact   ###
###########################################################################################################

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
###   Our Function to add a credit card    ###
##############################################
function addCard($CC) {
	global $client, $key;
###Set up the call to add the card###
	$call = new xmlrpcmsg("DataService.add", array(
		php_xmlrpc_encode($key), 		#The encrypted API key
		php_xmlrpc_encode("CreditCard"),	#The table to add the record to.
		php_xmlrpc_encode($CC),			#The Credit Card Data
	));
###Send the call###
	$result = $client->send($call);

	if(!$result->faultCode()) {
		$cardID = $result->value();
		print "Credit Card added - ID: " . $cardID;
		print "<BR>";
	} else {
		print $result->faultCode() . "<BR>";
		print $result->faultString() . "<BR>";
	}
	return $cardID;
}

###############################
###   Build the card data   ###
###############################
$card = array(
		"ContactId" => 		1300,
		"NameOnCard" => 	"FirstName LastName",
		"CardType" => 		"Visa", #Options are 'American Express','Discover', 'MasterCard', 'Visa'
		"CardNumber" => 	"4111111111111111",
		"ExpirationMonth" => 	"01", #must be MM
		"ExpirationYear" => 	"2011", #must be YYYY
		"CVV2" =>	 	"123",
		
	);
###   Create the card and store its returned ID   ###
$CCID = addCard($card);
?>