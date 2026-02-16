#!/usr/bin/perl

# Use the Frontier::Client Perl module
use Frontier::Client;
use Frontier::RPC2;
use Data::Dumper;

# Your API Access URL
my $server_url ='https://mach2.infusionsoft.com:443/api/xmlrpc';

# Create a new Frontier XML-RPC client instance
my $client = Frontier::Client->new( 
  'url' => $server_url,
  'debug' => 0, 
  'encoding' => 'iso-8859-1'
);

#The encrypted key
$key = "13643d3c8910057ce07623ed01bb22b2";

#------------------------------#     ECHO    #------------------------------------#
#---------------------------------------------------------------------------------#
#This should print out 'Hello, World' to stdout
print $client->call("DataService.echo", "Hello, World") . "\n";

#------------------------------# ADD CONTACT #------------------------------------#
#---------------------------------------------------------------------------------#

#Set up the contact data
$contact = {
	"FirstName"=>"John",
	"LastName"=>"Doe",
	"Email"=>"john@doe.com",
	"Company"=>"ACME"
};

#Make API call
$contactId = $client->call("DataService.add", $key, "Contact", $contact);
print "New contact id is $contactId\n";

#------------------------------# ADD CONTACT TO GROUP #------------------------------------#
#------------------------------------------------------------------------------------------#
#We will use the contactId from the previous example
$groupId=97;

#Call API - should print out true if it worked right
$bool = $client->call("ContactService.addToGroup", $key, $contactId, $groupId) . "\n";


#------------------------------# LIST CONTACTS IN GROUP #------------------------------------#
#--------------------------------------------------------------------------------------------#
#We will use the groupId from the previous example (=97)
$selectFields = ["ContactGroup", "ContactId"];
$limit = 50; #Max number of records to return
$page = 1; #Page to display



#Make API Call
$_results = $client->call("DataService.findByField", $key, "ContactGroupAssign", $limit, $page, "GroupId", $groupId, $selectFields);

# Results are returned as references.  _results will be an array containing hashes
foreach $item (@$_results) {
	print $item->{"ContactId"} . " was added to group " . $item->{"ContactGroup"} . "\n";
}
