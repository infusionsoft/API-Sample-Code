<!--#include virtual="aspxmlrpc/code/xmlrpc.asp" -->
<%
	
	'ASP examples for using the Infusion API.  The library used above
	'can be found at http://aspxmlrpc.sourceforge.net/
	
	'This example will perform three functions:
	'	1.  Add a new contact record
	'	2.  Add the newly created contact to a group
	'	3.  Look up a list of all contacts that belong to that group
		
	'key is the encrypted API key you got from Infusion Software
	key = "6ae189d497cd486b9db53793ccf98646"
	
	'URL is the API Url you got from Infusion Software
	url = "https://marty.infusionsoft.com:443/api/xmlrpc"
	
	
	'---------------------------     ADD CONTACT    ----------------------------
	'---------------------------------------------------------------------------
	'This function will add a contact to the database
	Dim contact
	'Scripting.Dictionary object is used to represent the XML-RPC struct
	Set contact = Server.createObject("Scripting.Dictionary")
	contact.Add "FirstName", "ASP John"
	contact.Add "LastName", "ASP Doe"
	contact.Add "Email", "eric@infusionsoft.com"
	contact.Add "DOB", Date
	
	'paramList will hold parameters to the service call
	ReDim paramList(3)
	paramList(0) = key
	paramList(1) = "Contact"  'The table we are adding to
	Set paramList(2) = contact

	'Make the API call.  The result is the ID of the new contact
	contactId = xmlRPC (url, "DataService.add", paramList)
	
	'Debugging information
	Response.Write(functionToXML ("DataService.add", paramList))
	
	'Clean up and print results
	Set contact = Nothing
	Response.Write("New Contact ID is  " & contactId & "<BR>")
	
	
	'---------------------------     ADD TO GROUP    ----------------------------
	'----------------------------------------------------------------------------
	'This function will take the contact added above and add it to group 97
	groupId = 97
	
	ReDim paramList(3)
	paramList(0) = key
	paramList(1) = contactId
	paramList(2) = groupId
	
	success = xmlRPC (url, "ContactService.addToGroup", paramList)
	Response.Write("Contact was added to group " & success & "<BR>")
	
	'---------------------------     LIST IN GROUP    ----------------------------
	'-----------------------------------------------------------------------------
	'This will list all contact ids that belong to group 97
	ReDim paramList(7)
	paramList(0) = key
	paramList(1) = "ContactGroupAssign"  	'The table that stores the information we are looking for
	paramList(2) = 50						'The limit to the # of records returned					
	paramList(3) = 1						'What page of results to view
	paramList(4) = "GroupId"				'The field we will be searching on
	paramList(5) = groupId					'The criteria for that field
	Dim fields(2)
	fields(0) = "ContactGroup"				'Fields to return in the query
	fields(1) = "ContactId"
	paramList(6) = fields
	contacts = xmlRPC (url, "DataService.findByField", paramList)
	
	'The contacts variable will be a list that contains struct (or dictionaries)
	For Each assignment in contacts
		'assignment variable is a dictionary
		Response.Write("Contact " & assignment("ContactId") & " was assigned to " & assignment("ContactGroup") & "<BR>")
	Next
%>
