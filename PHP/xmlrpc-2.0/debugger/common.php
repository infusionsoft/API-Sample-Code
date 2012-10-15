<?php
/**
 * @version $Id: common.php,v 1.2 2006/10/11 22:30:55 eric Exp $
 * @copyright G. Giunta 2005
 * @author Gaetano Giunta
 *
 * @todo switch params for http compression from 0,1,2 to values to be used directly
 * @todo do some more sanitization of received parameters
 */

// recover input parameters
  $debug = false;
  $protocol = 0;
  $run = false;
  if (isset($_GET['action']))
  {
    $host = isset($_GET['host']) ? $_GET['host'] : '';
    if (isset($_GET['protocol']) && ($_GET['protocol'] == '0' || $_GET['protocol'] == '1' || $_GET['protocol'] == '2'))
      $protocol = $_GET['protocol'];
    if (strpos($host, 'http://') === 0)
      $host = substr($host, 7);
    else if (strpos($host, 'https://') === 0)
    {
      $host = substr($host, 8);
      $protocol = 2;
    }
    $port = isset($_GET['port']) ? $_GET['port'] : '';
    $path = isset($_GET['path']) ? $_GET['path'] : '';
    // in case user forgot initial '/' in xmlrpc server path, add it back
    if ($path && ($path[0]) != '/')
      $path = '/'.$path;

    if (isset($_GET['debug']) && $_GET['debug'] == '1' || $_GET['debug'] == '2')
      $debug = $_GET['debug'];

    $verifyhost = isset($_GET['verifyhost']) ? $_GET['verifyhost'] : 0;
    if (isset($_GET['verifypeer']) && $_GET['verifypeer'] == '1')
      $verifypeer = true;
    $cainfo= isset($_GET['cainfo']) ? $_GET['cainfo'] : '';
    $proxy = isset($_GET['proxy']) ? $_GET['proxy'] : 0;
    if (strpos($proxy, 'http://') === 0)
      $proxy = substr($proxy, 7);
    $proxyuser= isset($_GET['proxyuser']) ? $_GET['proxyuser'] : '';
    $proxypwd = isset($_GET['proxypwd']) ? $_GET['proxypwd'] : '';
    $timeout = isset($_GET['timeout']) ? $_GET['timeout'] : 0;
    if (!is_numeric($timeout))
      $timeout = 0;
    $action = $_GET['action'];

    $method = isset($_GET['method']) ? $_GET['method'] : '';
    $payload = isset($_GET['methodpayload']) ? $_GET['methodpayload'] : '';

    if (isset($_GET['run']) && $_GET['run'] == 'now')
      $run = true;

    $username = isset($_GET['username']) ? $_GET['username'] : '';
    $password = isset($_GET['password']) ? $_GET['password'] : '';
	$authtype = isset($_GET['authtype']) ? $_GET['authtype'] : 1;

    if (isset($_GET['requestcompression']) && ($_GET['requestcompression'] == '0' || $_GET['requestcompression'] == '1' || $_GET['requestcompression'] == '2'))
      $requestcompression = $_GET['requestcompression'];
    if (isset($_GET['responsecompression']) && ($_GET['responsecompression'] == '0' || $_GET['responsecompression'] == '1' || $_GET['responsecompression'] == '2' || $_GET['responsecompression'] == '3'))
      $responsecompression = $_GET['responsecompression'];

    $clientcookies = isset($_GET['clientcookies']) ? $_GET['clientcookies'] : '';
  }
  else
  {
    $host = '';
    $port = '';
    $path = '';
    $action = '';
    $method = '';
    $payload = '';
    $username = '';
    $password = '';
    $authtype = 1;
    $verifyhost = 0;
    $verifypeer = false;
    $cainfo = '';
    $proxy = '';
    $proxyuser = '';
    $proxypwd = '';
    $timeout = 0;
    $requestcompression = 0;
    $responsecompression = 0;
	$clientcookies = '';
  }

  // check input for known XMLRPC attacks against this or other libs
  function payload_is_safe($input)
  {
      return true;
  }
?>
