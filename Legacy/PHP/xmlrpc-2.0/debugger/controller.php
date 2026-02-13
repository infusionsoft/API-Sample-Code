<?php
/**
 * @version $Id: controller.php,v 1.2 2006/10/11 22:30:55 eric Exp $
 * @copyright G. Giunta 2005
 * @author Gaetano Giunta
 *
 * @todo add links to documentation from every option caption
 * @todo switch params for http compression from 0,1,2 to values to be used directly
 * @todo add a little bit more CSS formatting: we broke IE box model getting a width > 100%...
 * @todo add support for more options, such as ntlm auth to proxy, or request charset encoding
 **/

  include(getcwd().'/common.php');
  if ($action == '')
    $action = 'list';
?>
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN"
    "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
<title>XMLRPC Debugger</title>
<meta name="robots" content="index,nofollow">
<style type="text/css">
<!--
html {overflow: -moz-scrollbars-vertical;}
body {padding: 0.5em; background-color: #EEEEEE; font-family: Verdana, Arial, Helvetica; font-size: 8pt;}
h1 {font-size: 12pt; margin: 0.5em;}
h2 {font-size: 10pt; display: inline; vertical-align: top;}
table {border: 1px solid gray; margin-bottom: 0.5em; padding: 0.25em; width: 100%;}
#methodpayload {display: inline;}
td {vertical-align: top; font-family: Verdana, Arial, Helvetica; font-size: 8pt;}
.labelcell {text-align: right;}
-->
</style>
<script language="JavaScript" type="text/javascript">
<!--
  function verifyserver()
  {
    if (document.frmaction.host.value == '')
    {
      alert('Please insert a server name or address');
      return false;
    }
    if (document.frmaction.path.value == '')
      document.frmaction.path.value = '/';
    if (document.frmaction.authtype.value != '1' && document.frmaction.username.value == '')
    {
      alert('No username for authenticating to server: authentication disabled');
    }
    return true;
  }

  function switchaction()
  {
    // reset html layout depending on action to be taken
    var action = '';
    for (counter = 0; counter < document.frmaction.action.length; counter++)
      if (document.frmaction.action[counter].checked)
      {
        action = document.frmaction.action[counter].value;
      }
    if (action == 'execute')
    {
      document.frmaction.methodpayload.disabled = false;
      document.frmaction.method.disabled = false;
      document.frmaction.methodpayload.rows = 10;
    }
    else
    {
      document.frmaction.methodpayload.rows = 1;
      if (action == 'describe')
      {
        document.frmaction.methodpayload.disabled = true;
        document.frmaction.method.disabled = false;
      }
      else // list
      {
        document.frmaction.methodpayload.disabled = true;
        document.frmaction.method.disabled = true;
      }
    }
  }
  function switchssl()
  {
    if (document.frmaction.protocol.value != '2')
    {
      document.frmaction.verifypeer.disabled = true;
      document.frmaction.verifyhost.disabled = true;
      document.frmaction.cainfo.disabled = true;
    }
    else
    {
      document.frmaction.verifypeer.disabled = false;
      document.frmaction.verifyhost.disabled = false;
      document.frmaction.cainfo.disabled = false;
    }
  }
  function switchauth()
  {
    if (document.frmaction.protocol.value != '0')
    {
      document.frmaction.authtype.disabled = false;
    }
    else
    {
      document.frmaction.authtype.disabled = true;
      document.frmaction.authtype.value = 1;
    }
  }
  function swicthcainfo()
  {
    if (document.frmaction.verifypeer.checked == true)
    {
      document.frmaction.cainfo.disabled = false;
    }
    else
    {
      document.frmaction.cainfo.disabled = true;
    }
  }

//-->
</script>
</head>
<body onload="switchaction(); switchssl(); switchauth(); swicthcainfo();<?php if ($run) echo ' document.forms[0].submit();'; ?>">
<h1>XMLRPC Debugger (based on the <a href="http://phpxmlrpc.sourceforge.net">PHP-XMLRPC</a> library)</h1>
<form name="frmaction" method="get" action="action.php" target="frmaction">

<table id="serverblock">
<tr>
<td><h2>Target server</h2></td>
<td class="labelcell">Address:</td><td><input type="text" name="host" value="<?php echo htmlspecialchars($host); ?>" /></td>
<td class="labelcell">Port:</td><td><input type="text" name="port" value="<?php echo htmlspecialchars($port); ?>" size="5" maxlength="5" /></td>
<td class="labelcell">Path:</td><td><input type="text" name="path" value="<?php echo htmlspecialchars($path); ?>" /></td>
</tr>
</table>

<table id="actionblock">
<tr>
<td><h2>Action</h2></td>
<td>List available methods<input type="radio" name="action" value="list"<?php if ($action=='list') echo ' checked="checked"'; ?> onclick="switchaction();" /></td>
<td>Describe method<input type="radio" name="action" value="describe"<?php if ($action=='describe') echo ' checked="checked"'; ?> onclick="switchaction();" /></td>
<td>Execute method<input type="radio" name="action" value="execute"<?php if ($action=='execute') echo ' checked="checked"'; ?> onclick="switchaction();" /></td>
</tr>
</table>

<table id="methodblock">
<tr>
<td><h2>Method</h2></td>
<td class="labelcell">Name:</td><td><input type="text" name="method" value="<?php echo htmlspecialchars($method); ?>" /></td>
<td class="labelcell">Payload:</td><td><textarea id="methodpayload" name="methodpayload" rows="1" cols="40"><?php echo htmlspecialchars($payload); ?></textarea></td>
<td></td><td><input type="submit" value="Execute" onclick="return verifyserver();"/></td>
</tr>
</table>

<table id="optionsblock">
<tr>
<td><h2>Client options</h2></td>
<td class="labelcell">Show debug info:</td><td><select name="debug">
<option value="0"<?php if ($debug == 0) echo ' selected="selected"'; ?>>No</option>
<option value="1"<?php if ($debug == 1) echo ' selected="selected"'; ?>>Yes</option>
<option value="2"<?php if ($debug == 2) echo ' selected="selected"'; ?>>More</option>
</select>
</td>
<td class="labelcell">Timeout:</td><td><input type="text" name="timeout" size="3" value="<?php if ($timeout > 0) echo $timeout; ?>" /></td>
<td class="labelcell">Protocol:</td><td><select name="protocol" onclick="switchssl(); switchauth(); swicthcainfo();">
<option value="0"<?php if ($protocol == 0) echo ' selected="selected"'; ?>>HTTP 1.0</option>
<option value="1"<?php if ($protocol == 1) echo ' selected="selected"'; ?>>HTTP 1.1</option>
<option value="2"<?php if ($protocol == 2) echo ' selected="selected"'; ?>>HTTPS</option>
</select></td>
</tr>
<tr>
<td class="labelcell">AUTH:</td>
<td class="labelcell">Username:</td><td><input type="text" name="username" value="<?php echo htmlspecialchars($username); ?>" /></td>
<td class="labelcell">Pwd:</td><td><input type="password" name="password" value="<?php echo htmlspecialchars($password); ?>" /></td>
<td class="labelcell">Type</td><td><select name="authtype">
<option value="1"<?php if ($authtype == 1) echo ' selected="selected"'; ?>>Basic</option>
<option value="2"<?php if ($authtype == 2) echo ' selected="selected"'; ?>>Digest</option>
<option value="8"<?php if ($authtype == 8) echo ' selected="selected"'; ?>>NTLM</option>
</select></td>
<td></td>
</tr>
<tr>
<td class="labelcell">SSL:</td>
<td class="labelcell">Verify Host's CN:</td><td><select name="verifyhost">
<option value="0"<?php if ($verifyhost == 0) echo ' selected="selected"'; ?>>No</option>
<option value="1"<?php if ($verifyhost == 1) echo ' selected="selected"'; ?>>Check CN existance</option>
<option value="2"<?php if ($verifyhost == 2) echo ' selected="selected"'; ?>>Check CN match</option>
</select></td>
<td class="labelcell">Verify Cert:</td><td><input type="checkbox" value="1" name="verifypeer" onclick="swicthcainfo();"<?php if ($verifypeer) echo ' checked="checked"'; ?> /></td>
<td class="labelcell">CA Cert file:</td><td><input type="text" name="cainfo" value="<?php echo htmlspecialchars($cainfo); ?>" /></td>
</tr>
<tr>
<td class="labelcell">PROXY:</td>
<td class="labelcell">Server:</td><td><input type="text" name="proxy" value="<?php echo htmlspecialchars($proxy); ?>" /></td>
<td class="labelcell">Proxy user:</td><td><input type="text" name="proxyuser" value="<?php echo htmlspecialchars($proxyuser); ?>" /></td>
<td class="labelcell">Proxy pwd:</td><td><input type="password" name="proxypwd" value="<?php echo htmlspecialchars($proxypwd); ?>" /></td>
</tr>
<tr>
<td class="labelcell">COMPRESSION:</td>
<td class="labelcell">Request:</td><td><select name="requestcompression">
<option value="0"<?php if ($requestcompression == 0) echo ' selected="selected"'; ?>>None</option>
<option value="1"<?php if ($requestcompression == 1) echo ' selected="selected"'; ?>>Gzip</option>
<option value="2"<?php if ($requestcompression == 2) echo ' selected="selected"'; ?>>Deflate</option>
</select></td>
<td class="labelcell">Response:</td><td><select name="responsecompression">
<option value="0"<?php if ($responsecompression == 0) echo ' selected="selected"'; ?>>None</option>
<option value="1"<?php if ($responsecompression == 1) echo ' selected="selected"'; ?>>Gzip</option>
<option value="2"<?php if ($responsecompression == 2) echo ' selected="selected"'; ?>>Deflate</option>
<option value="3"<?php if ($responsecompression == 3) echo ' selected="selected"'; ?>>Any</option>
</select></td>
<td></td>
</tr>
<tr>
<td class="labelcell">COOKIES:</td>
<td colspan="4" class="labelcell"><input type="text" name="clientcookies" size="80" value="<?php echo htmlspecialchars($clientcookies); ?>" /></td>
<td colspan="2">Format: 'cookie1=value1, cookie2=value2'</td>
</tr>
</table>

</form>
</body>
</html>