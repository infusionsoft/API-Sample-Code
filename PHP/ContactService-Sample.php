<?php
###########################################################################################################
###Sample Created by Justin Morris on 7/8/08                                                            ###
###In this sample we create a script that will allow forms to post to it and then                       ###
###take the posted data and create a contact in Infusionsoft and add it to a group and/or campaign.     ###
###The forms.html file included with this sample is the page that posts to this script.                 ###
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

###########################################
###Our Function to add people to a group###
###########################################
function addGrp($CID, $GID) {
###Set up global variables###
	global $client, $key;
###Set up the call to add to the group###
	$call = new xmlrpcmsg("ContactService.addToGroup", array(
		php_xmlrpc_encode($key), 		#The encrypted API key
		php_xmlrpc_encode($CID),		#The contact ID
		php_xmlrpc_encode($GID),		#The Group ID
	));
###Send the call###
	$result = $client->send($call);

	if(!$result->faultCode()) {
		print "Contact added to group " . $GID;
		print "<BR>";
	} else {
		print $result->faultCode() . "<BR>";
		print $result->faultString() . "<BR>";
	}
}

##############################################
###Our Function to add people to a campaign###
##############################################
function addCamp($CID, $CMP) {
###Set up global variables###
	global $client, $key;
	
###Set up the call to add to the campaign###
	$call = new xmlrpcmsg("ContactService.addToCampaign", array(
		php_xmlrpc_encode($key), 		#The encrypted API key
		php_xmlrpc_encode($CID),		#The contact ID
		php_xmlrpc_encode($CMP),		#The Campaign ID
	));
###Send the call###
	$result = $client->send($call);

	if(!$result->faultCode()) {
		print "Contact added to Campaign " . $CMP;
		print "<BR>";
	} else {
		print $result->faultCode() . "<BR>";
		print $result->faultString() . "<BR>";
	}
}

################################################################
###We only want to run the API script if there is posted data###
################################################################
if (isset($_POST['fName'],$_POST['lName'],$_POST['email'])) {
	if ($_POST['fName'] == "" || $_POST['lName'] == "" || $_POST['email'] == "") {
		//ABORT
		echo "<script>alert('You must fill out the required fields!');</script>";
	} else {
		
###Build a Key-Value Array to store a contact###
$contact = array(
		"FirstName" => 	$_POST['fName'],
		"LastName" => 	$_POST['lName'],
		"Email" => 		$_POST['email'],
	);

###Set up the call###
$call = new xmlrpcmsg("ContactService.add", array(
		php_xmlrpc_encode($key), 		#The encrypted API key
		php_xmlrpc_encode($contact)		#The contact array
	));

###Send the call###
	$result = $client->send($call);

###Check the returned value to see if it was successful and set it to a variable/display the results###
	if(!$result->faultCode()) {
                $conID = $result->value();
		print "Contact added was " . $conID;
		print "<BR>";
	} else {
		print $result->faultCode() . "<BR>";
		print $result->faultString() . "<BR>";
	}

##########################################################
###Check to see what newsgroups/campaigns were selected###
##########################################################
	if(isset($_POST['news1'])) {
		addGrp($conID,91);
	}
	if(isset($_POST['news2'])) {
		addGrp($conID,92);
	}
	if(isset($_POST['camp1'])) {
		addCamp($conID,21);
	}
	if(isset($_POST['camp2'])) {
		addCamp($conID,23);
	}
}
###Finally, lets alert them if they didnt post the required fields###
} else {
echo "<script>alert('Be sure to fill out all required fields.');</script>"; 
}
?>