### Ruby example code for Infusionsoft API using xmlrpc ###
### By: Justin Morris ###

#Initialize xmlrpc
require 'xmlrpc/client'
  require 'pp'
  
  #Encrypted API Key
  key="EncryptedAPIKey"
  
  #Define the servers URL
  server = XMLRPC::Client.new2("https://AppName.infusionsoft.com:443/api/xmlrpc")
  
  #Create Contact Hash
  contact={"FirstName"=>"Justin", "LastName"=>"Morris","Email"=>"justinm@infusionsoft.com"}
  
  #Make the server call to add a contact.
  result = server.call("ContactService.add", key, contact)
  pp "Contact added: #{result}"
  
  #Make the server call to add it to the group with ID 94
  result=server.call("ContactService.addToGroup",key,result,94)
  pp "Added to group: #{result}"