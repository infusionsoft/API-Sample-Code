<?php
/**
 * Common parameter parsing for benchmarks and tests scripts
 *
 * @param boolean DEBUG
 * @param string  LOCALSERVER
 * @param string  URI
 * @param string  HTTPSSERVER
 * @param string  HTTPSSURI
 **/

	require_once('xmlrpc.inc');
	require_once('xmlrpcs.inc');

	// play nice to older PHP versions that miss superglobals
	if(!isset($_SERVER))
	{
		$_SERVER = $HTTP_SERVER_VARS;
	}

	// check for command line vs web page input params
	if(!isset($_SERVER['REQUEST_METHOD']))
	{
		if(isset($argv))
		{
			foreach($argv as $param)
			{
				$param = explode('=', $param);
				if(count($param) > 1)
				{
					$$param[0]=$param[1];
				}
			}
		}
	}
	elseif(!ini_get('register_globals') && function_exists('import_request_variables'))
	{
		// play nice to modern PHP installations with register globals OFF
		// NB: we might as well consider using $_GET stuff later on...
		@import_request_variables('GP');
	}

	if(!isset($DEBUG))
	{
		$DEBUG = 0;
	}

	if(!isset($LOCALSERVER))
	{
		if(isset($HTTP_HOST))
		{
			$LOCALSERVER = $HTTP_HOST;
		}
		elseif(isset($_SERVER['HTTP_HOST']))
		{
			$LOCALSERVER = $_SERVER['HTTP_HOST'];
		}
		else
		{
			$LOCALSERVER = 'localhost';
		}
	}
	if(!isset($HTTPSSERVER))
	{
		$HTTPSSERVER = 'xmlrpc.usefulinc.com';
	}
	if(!isset($HTTPSURI))
	{
		$HTTPSURI = '/server.php';
	}
	if(!isset($URI))
	{
		// GUESTIMATE the url of local demo server
		// play nice to php 3 and 4-5 in retrieving URL of server.php
		if(isset($REQUEST_URI))
		{
			$URI = str_replace('/test/testsuite.php', '/demo/server/server.php', $REQUEST_URI);
			$URI = str_replace('/testsuite.php', '/server.php', $URI);
			$URI = str_replace('/test/benchmark.php', '/server.php', $URI);
			$URI = str_replace('/benchmark.php', '/server.php', $URI);
		}
		elseif(isset($_SERVER['PHP_SELF']) && isset($_SERVER['REQUEST_METHOD']))
		{
			$URI = str_replace('/test/testsuite.php', '/demo/server/server.php', $_SERVER['PHP_SELF']);
			$URI = str_replace('/testsuite.php', '/server.php', $URI);
			$URI = str_replace('/test/benchmark.php', '/server.php', $URI);
			$URI = str_replace('/benchmark.php', '/server.php', $URI);
		}
		else
		{
			$URI = '/demo/server/server.php';
		}
	}
	if($URI[0] != '/')
	{
		$URI = '/'.$URI;
	}
	if(!isset($LOCALPATH))
	{
		$LOCALPATH = dirname(__FILE__);
	}

?>