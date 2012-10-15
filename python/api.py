import xmlrpclib

#Python has built-in support for xml-rpc.  All you need to do is add the 
#line above. 

#Set up the API server variable
server = xmlrpclib.ServerProxy("https://appName.infusionsoft.com:443/api/xmlrpc");

#The encrypted API key
key = "dcbe038fbdc9c3f0d538b545a1705006";

########    PING & ECHO          #############
#This is the easiest api call
print server.DataService.echo("Hello, World");


########    ADD CONTACT    #############
#Set up the contact data we want to add
contact = {}; #blank dictionary
contact["FirstName"] = "John";
contact["LastName"] = "Doe";
contact["Email"] = "john@doe.com";
contact["Company"] = "ACME";

#Make the call to "DataService.add" method.  The ID for the newly added record will be returned as the variable "id"
id = server.DataService.add(key, "Contact", contact);
print "Added contact with id ", id;
print;

########    ADD TO GROUP   #############
groupId = 97;
bool = server.ContactService.addToGroup(key, id, groupId);
print "Contact added to group: ", bool;
print;

########    FIND ALL CONTACTS IN GROUP   #######
#Fields I want to select
fields = ["ContactId", "ContactGroup"];

limit = 50; #Limit the number of rows that will be returned to 10
page = 1; #Start with the first page

#Make the API call
results = server.DataService.findByField(key, "ContactGroupAssign", limit, page, "GroupId", groupId, fields); 

#Results will be a python list containg dictionaries.  Let's loop through each item in the list and examine it
for result in results: 
	print "Found ", result["ContactId"], " was in ", result["ContactGroup"]
