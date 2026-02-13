<?php
/**
 * @version $Id: action.php,v 1.2 2006/10/11 22:30:55 eric Exp $
 * @copyright G. Giunta 2005
 * @author Gaetano Giunta
 *
 * @todo switch params for http compression from 0,1,2 to values to be used directly
 * @todo use ob_start to catch debug info and print it AFTER method call results?
 **/
?>
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN"
    "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
  <title>XMLRPC Debugger</title>
  <meta name="robots" content="index,nofollow">
<style type="text/css">
<!--
body {border-top: 1px solid gray; padding: 1em; font-family: Verdana, Arial, Helvetica; font-size: 8pt;}
.dbginfo {padding: 1em; background-color: #EEEEEE; border: 1px dashed silver; font-family: monospace;}
#response {padding: 1em; margin-top: 1em; background-color: #DDDDDD; border: 1px solid gray; white-space: pre; font-family: monospace;}
table {padding: 2px; margin-top: 1em;}
th {background-color: navy; color: white; padding: 0.5em;}
td {padding: 0.5em; font-family: monospace;}
.oddrow {background-color: #EEEEEE;}
-->
</style>
</head>
<body>
<?php

  include(getcwd().'/common.php');
  if ($action)
  {
    include('xmlrpc.inc');

    if ($port != "")
    {
      $client =& new xmlrpc_client($path, $host, $port);
      $server = "$host:$port$path";
    } else {
      $client =& new xmlrpc_client($path, $host);
      $server = "$host$path";
    }
    if ($protocol == 2)
	{
      $server = 'https://'.$server;
    }
    else
	{
      $server = 'http://'.$server;
    }
    if ($proxy != '') {
      $pproxy = split(':', $proxy);
      if (count($pproxy) > 1)
        $pport = $pproxy[1];
      else
        $pport = 8080;
      $client->setProxy($pproxy[0], $pport, $proxyuser, $proxypwd);
    }

    if ($protocol == 2)
    {
      $client->setSSLVerifyPeer($verifypeer);
      $client->setSSLVerifyHost($verifyhost);
      if ($cainfo)
      {
        $client->setCaCertificate($cainfo);
      }
      $httpprotocol = 'https';
    }
    else if ($protocol == 1)
      $httpprotocol = 'http11';
    else
      $httpprotocol = 'http';

    if ($username)
      $client->setCredentials($username, $password, $authtype);

    $client->setDebug($debug);

    switch ($requestcompression) {
      case 0:
        $client->request_compression = '';
        break;
      case 1:
        $client->request_compression = 'gzip';
        break;
      case 2:
        $client->request_compression = 'deflate';
        break;
    }

    switch ($responsecompression) {
      case 0:
        $client->accepted_compression = '';
        break;
      case 1:
        $client->accepted_compression = array('gzip');
        break;
      case 2:
        $client->accepted_compression = array('deflate');
        break;
      case 3:
        $client->accepted_compression = array('gzip', 'deflate');
        break;
    }

    $cookies = explode(',', $clientcookies);
    foreach ($cookies as $cookie)
    {
      if (strpos($cookie, '='))
      {
        $cookie = explode('=', $cookie);
        $client->setCookie(trim($cookie[0]), trim(@$cookie[1]));
      }
    }

    $msg = array();
    switch ($action) {
      case 'describe':
        $msg[0] =& new xmlrpcmsg('system.methodHelp');
        $msg[0]->addparam(new xmlrpcval($method));
        $msg[1] =& new xmlrpcmsg('system.methodSignature');
        $msg[1]->addparam(new xmlrpcval($method));
        $actionname = 'Description of method "'.$method.'"';
        break;
      case 'list':
        $msg[0] =& new xmlrpcmsg('system.listMethods');
        $actionname = 'List of available methods';
        break;
      case 'execute':
        if (!payload_is_safe($payload))
          die("Tsk tsk tsk, please stop it or I will have to call in the cops!");
        $msg[0] =& new xmlrpcmsg($method);
        // hack! build xml payload by hand
        $msg[0]->payload = $msg[0]->xml_header() .
            '<methodName>' . $method . "</methodName>\n<params>" .
            $payload .
            "</params>\n" . $msg[0]->xml_footer();
        $actionname = 'Execution of method '.$method;
        break;
      default: // give a warning
    }

    // Before calling execute, print out brief description of action taken + date and time ???
    /// @todo

    // execute method(s)
    if ($debug)
      echo '<div class="dbginfo"><h2>Debug info:</h2>';  /// @todo use ob_start instead
    $resp = array();
    $mtime = explode(' ',microtime());
    $time = (float)$mtime[0] + (float)$mtime[1];
    foreach ($msg as $message)
    {
      // catch errors: for older xmlrpc libs, send does not return by ref
      @$response =& $client->send($message, $timeout, $httpprotocol);
      $resp[] = $response;
      if (!$response || $response->faultCode())
        break;
    }
    $mtime = explode(' ',microtime());
    $time = (float)$mtime[0] + (float)$mtime[1] - $time;
    if ($debug)
      echo '</div>';

    if ($response->faultCode())
    {
      // call failed! print out error msg!
      echo '<h2>'.htmlspecialchars($actionname).' on server '.htmlspecialchars($server).'</h2>';
      echo '<h3>XMLRPC call FAILED!</h3>';
      print "<p>Fault code: [" . htmlspecialchars($response->faultCode()) .
        "] Reason: '" . htmlspecialchars($response->faultString()) . "'</p>";
      echo(strftime("%d/%b/%Y:%H:%M:%S"));
    }
    else
    {
      // call succeeded: parse results
      echo '<h2>'.htmlspecialchars($actionname).' on server '.htmlspecialchars($server).'</h2>';
      printf ("<h3>XMLRPC call(s) OK (%.2f secs.)</h3>", $time);
      echo(strftime("%d/%b/%Y:%H:%M:%S"));

      switch ($action)
      {
        case 'list':

        $v = $response->value();
        $max = $v->arraysize();
        echo("<table border=\"0\" cellspacing=\"0\" cellpadding=\"0\">\n");
        print ("<thead>\n<tr><th>Method</th><th>Description</th><th>&nbsp;</th></tr>\n</thead>\n<tbody>\n");
        for($i=0; $i < $max; $i++)
        {
          $rec = $v->arraymem($i);
          if ($i%2) $class=' class="oddrow"'; else $class = ' class="evenrow"';
          print ("<tr><td$class>".$rec->scalarval()."</td><td$class><form action=\"controller.php\" method=\"get\" target=\"frmcontroller\">".
            "<input type=\"hidden\" name=\"host\" value=\"$host\" />".
            "<input type=\"hidden\" name=\"port\" value=\"$port\" />".
            "<input type=\"hidden\" name=\"path\" value=\"$path\" />".
            "<input type=\"hidden\" name=\"debug\" value=\"$debug\" />".
            "<input type=\"hidden\" name=\"username\" value=\"$username\" />".
            "<input type=\"hidden\" name=\"password\" value=\"$password\" />".
            "<input type=\"hidden\" name=\"verifyhost\" value=\"$verifyhost\" />".
            "<input type=\"hidden\" name=\"verifypeer\" value=\"$verifypeer\" />".
            "<input type=\"hidden\" name=\"proxy\" value=\"".htmlspecialchars($proxy)."\" />".
            "<input type=\"hidden\" name=\"proxyuser\" value=\"".htmlspecialchars($proxyuser)."\" />".
            "<input type=\"hidden\" name=\"proxypwd\" value=\"".htmlspecialchars($proxypwd)."\" />".
            "<input type=\"hidden\" name=\"responsecompression\" value=\"$responsecompression\" />".
            "<input type=\"hidden\" name=\"requestcompression\" value=\"$requestcompression\" />".
            "<input type=\"hidden\" name=\"clientcookies\" value=\"".htmlspecialchars($clientcookies)."\" />".
            "<input type=\"hidden\" name=\"protocol\" value=\"$protocol\" />".
            "<input type=\"hidden\" name=\"timeout\" value=\"$timeout\" />".
            "<input type=\"hidden\" name=\"method\" value=\"".$rec->scalarval()."\" />".
            "<input type=\"hidden\" name=\"action\" value=\"describe\" />".
            "<input type=\"hidden\" name=\"run\" value=\"now\" />".
            "<input type=\"submit\" value=\"Describe\" /></form></td>");
          //print("</tr>\n");

          // generate lo scheletro per il method payload per eventuali test
          //$methodpayload="<methodCall>\n<methodName>".$rec->scalarval()."</methodName>\n<params>\n<param><value></value></param>\n</params>\n</methodCall>";

          /*print ("<form action=\"{$_SERVER['PHP_SELF']}\" method=\"get\"><td>".
            "<input type=\"hidden\" name=\"host\" value=\"$host\" />".
            "<input type=\"hidden\" name=\"port\" value=\"$port\" />".
            "<input type=\"hidden\" name=\"path\" value=\"$path\" />".
            "<input type=\"hidden\" name=\"method\" value=\"".$rec->scalarval()."\" />".
            "<input type=\"hidden\" name=\"methodpayload\" value=\"$payload\" />".
            "<input type=\"hidden\" name=\"action\" value=\"execute\" />".
            "<input type=\"submit\" value=\"Test\" /></td></form>");*/
          print("</tr>\n");
        }
        echo("</tbody>\n</table>");

          break;

        case 'describe':

        $r1 = $resp[0]->value();
        $r2 = $resp[1]->value();

        echo("<table border=\"0\" cellspacing=\"0\" cellpadding=\"0\">");
        print ("<thead><tr><th>Method</th><th>$method</th><th>&nbsp;</th></tr></thead><tbody>");
        $desc = htmlentities($r1->scalarval());
        if ($desc == "")
          $desc = "-";
        print ("<tr><td class=\"evenrow\">Description</td><td class=\"evenrow\">$desc</td><td class=\"evenrow\">&nbsp;</td></tr>\n");
        $payload="";
        if ($r2->kindOf()!="array")
          print "<tr><td class=\"oddrow\">Signature</td><td class=\"oddrow\">Unknown</td><td class=\"oddrow\">&nbsp;</td></tr>\n";
        else
        {
          for($i=0; $i < $r2->arraysize(); $i++)
          {
          if ($i+1%2) $class=' class="oddrow"'; else $class = ' class="evenrow"';
            print "<tr><td$class>Signature&nbsp;".($i+1)."</td><td$class>";
            $x = $r2->arraymem($i);
              $ret = $x->arraymem(0);
              print "<code>OUT:&nbsp;" . $ret->scalarval() . "<br />IN: (";
              if ($x->arraysize() > 1)
              {
                for($k = 1; $k < $x->arraysize(); $k++)
                {
                  $y = $x->arraymem($k);
                  print $y->scalarval();
                  $payload = $payload . '<param><value><'.$y->scalarval().'></'.$y->scalarval()."></value></param>\n";
                  if ($k < $x->arraysize()-1)
                    print ", ";
                }
            }
            print ")</code></td>";
            //bottone per testare questo metodo
            //$payload="<methodCall>\n<methodName>$method</methodName>\n<params>\n$payload</params>\n</methodCall>";
            echo ("<td$class><form action=\"controller.php\" target=\"frmcontroller\" method=\"get\">".
            "<input type=\"hidden\" name=\"host\" value=\"$host\" />".
            "<input type=\"hidden\" name=\"port\" value=\"$port\" />".
            "<input type=\"hidden\" name=\"path\" value=\"$path\" />".
            "<input type=\"hidden\" name=\"debug\" value=\"$debug\" />".
            "<input type=\"hidden\" name=\"username\" value=\"$username\" />".
            "<input type=\"hidden\" name=\"password\" value=\"$password\" />".
            "<input type=\"hidden\" name=\"authtype\" value=\"$authtype\" />".
            "<input type=\"hidden\" name=\"verifyhost\" value=\"$verifyhost\" />".
            "<input type=\"hidden\" name=\"verifypeer\" value=\"$verifypeer\" />".
            "<input type=\"hidden\" name=\"proxy\" value=\"".htmlspecialchars($proxy)."\" />".
            "<input type=\"hidden\" name=\"proxyuser\" value=\"".htmlspecialchars($proxyuser)."\" />".
            "<input type=\"hidden\" name=\"proxypwd\" value=\"".htmlspecialchars($proxypwd)."\" />".
            "<input type=\"hidden\" name=\"responsecompression\" value=\"$responsecompression\" />".
            "<input type=\"hidden\" name=\"requestcompression\" value=\"$requestcompression\" />".
            "<input type=\"hidden\" name=\"clientcookies\" value=\"".htmlspecialchars($clientcookies)."\" />".
            "<input type=\"hidden\" name=\"protocol\" value=\"$protocol\" />".
            "<input type=\"hidden\" name=\"timeout\" value=\"$timeout\" />".
            "<input type=\"hidden\" name=\"method\" value=\"$method\" />".
            "<input type=\"hidden\" name=\"methodpayload\" value=\"".htmlspecialchars($payload)."\" />".
            "<input type=\"hidden\" name=\"action\" value=\"execute\" />".
            "<input type=\"submit\" value=\"Load method synopsis\" /></form></td></tr>\n");
          }
        }
        echo("</tbody>\n</table>");

          break;

        case 'execute':
          echo '<div id="response"><h2>Response:</h2>'.htmlspecialchars($response->serialize()).'</div>';
          break;

        default: // give a warning
      }
    }
  }
  else
  {
    // no action taken yet: give some instructions on debugger usage
?>

<h3>Instructions on usage of the debugger:</h3>
<ol>
<li>Run a 'list available methods' action against desired server</li>
<li>If list of methods appears, click on 'describe method' for desired method</li>
<li>To run method: click on 'load method synopsis' for desired method. This will load a skeleton for method call parameters in the form above. Complete all xmlrpc values with appropriate data and click 'Execute'</li>
</ol>

<h3>Example:</h3>
<p>
Server Address: phpxmlrpc.sourceforge.net<br/>
Path: /server.php
</p>

<h3>Notice:</h3>
<p>all usernames and passwords entered on the above form will be written to the web server logs of this server. Use with care.</p>

<h3>Changelog</h3>
<ul>
<li>2006-04-22: added option for setting custom CA certs to verify peer with in SSLmode</li>
<li>2006-03-05: added option for setting Basic/Digest/NTLM auth type</li>
<li>2006-01-18: added option echoing to screen xmlrpc request before sending it ('More' debug)</li>
<li>2005-10-01: added option for setting cookies to be sent to server</li>
<li>2005-08-07: added switches for compression of requests and responses and http 1.1</li>
<li>2005-06-27: fixed possible security breach in parsing malformed xml</li>
<li>2005-06-24: fixed error with calling methods having parameters...</li>
</ul>
<?php
  }
?>
</body>
</html>
