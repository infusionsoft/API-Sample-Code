################################################################################
###                                                                          ###
### Sample updated by Jeremiah Marks on 17 July 2014                         ###
###                                                                          ###
###                                                                          ###
### Sample currently works in Python2.7                                      ###
### Sample currently does not work in Python 3.4                             ###
###                                                                          ###
### This sample connects to the remote Infusionsoft server, demonstrates the ###
### ability to add a new contact, add a tag to a contact, and find all       ###
### contacts with the same tag.                                              ###
###                                                                          ###
################################################################################

################################################################################
########    Import the xml-rpc library                                  ########
################################################################################
import xmlrpclib

################################################################################
########    Configure variables for your account.                       ########
########      Replace "appName" with your appname/subdomain and insert  ########
########      your Encrypted Key from Admin > Settings > Application    ########
################################################################################
server = xmlrpclib.ServerProxy("https://appName.infusionsoft.com:443/api/xmlrpc")
key = "4a9a88ca89ab126b5fdc368eea0abd2a"

################################################################################
########    ADD CONTACT                                                 ########
########      Set up the contact data we want to add                    ########
################################################################################
contact = {}
contact["FirstName"] = "John"
contact["LastName"] = "Doe"
contact["Email"] = "john@doe.com"
contact["Company"] = "ACME"

################################################################################
########      Make the call to "DataService.add" method.  The ID for the######## 
########      newly added record will be returned as the variable "id"  ########
################################################################################
id = server.DataService.add(key, "Contact", contact)
print "Added contact with id ", id, "\n"

################################################################################
########    ADD TAG TO CONTACT                                          ########
########      Note: if you do not have a tag with the id of 269, this   ########
########      will fail.                                                ########
################################################################################
tagId = 269
added = server.ContactService.addToGroup(key, id, tagId)
print "Tag added to contact: ", added, "\n"

################################################################################
########    FIND ALL CONTACTS WITH A TAG                                ########
########        This search illustrates how to find all contacts that   ########
########        have a particular tag as well as when those tags were   ########
########        applied.                                                ########
################################################################################

fields = ["ContactId", "ContactGroup", "DateCreated"]

limit = 50;     #Limit the number of rows that will be returned to 50
page = 0;       #Start with the first page


results = server.DataService.findByField(key, "ContactGroupAssign", limit, \
    page, "GroupId", tagId, fields)

for result in results: 
    print "Found: Contact #%(ID)3d had tag \"%(tagName)s\" applied on %(date)s."\
     %{"ID":result["ContactId"], "tagName":result["ContactGroup"], "date":result["DateCreated"]}