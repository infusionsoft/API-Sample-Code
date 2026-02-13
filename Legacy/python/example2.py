import xmlrpclib

#Python has built-in support for xml-rpc.  All you need to do is add the
#line above.

#Set up the API server variable
server = xmlrpclib.ServerProxy("https://appName.infusionsoft.com:443/api/xmlrpc");

#The encrypted API key
key = "dcbe038fbdc9c3f0d538b545a1705006";

var = server.DataService.findByField(key,"Contact",10,0,"email","%testmail.misc",["LastName","Id"] );
for result in var:
      server.DataService. update(key,"Contact",result["Id"],{"LastName":"Walker"});




print "Done";
