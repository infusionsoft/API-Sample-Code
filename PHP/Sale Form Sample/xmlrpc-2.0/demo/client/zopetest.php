<html>
<head><title>xmlrpc</title></head>
<body>
<h1>Zope test demo</h1>
<h3>The code demonstrates usage of basic authentication to connect to the server</h3>
<?php
  include("xmlrpc.inc");

  $f=new xmlrpcmsg('document_src', array());
  $c=new xmlrpc_client("/index_html", "pingu.heddley.com", 9080);
  $c->setCredentials("username", "password");
  $c->setDebug(2);
  $r=$c->send($f);
  if (!$r->faultCode()) {
    $v=$r->value();
    print "I received:" . htmlspecialchars($v->scalarval()) . "<br/>";
    print "<hr/>I got this value back<br/>pre>" .
      htmlentities($r->serialize()). "</pre>\n";
  } else {
    print "An error occurred: ";
    print "Code: " . htmlspecialchars($r->faultCode()) .
      " Reason: '" . ($r->faultString()) . "'<br/>";
  }
?>
<hr/>
<em>$Id: zopetest.php,v 1.2 2006/10/11 22:30:55 eric Exp $</em>
</body>
</html>
