<?php

class Shorten_URL_isgd
{
	public static function shorten($url,$timeout = 10)
	{
		$fullurl = "http://is.gd/api.php?longurl=".$url;
			try
			{
				$client = new Zend_Http_Client($fullurl, array(
					'timeout' => $timeout
				));

				$request = $client->request();

				if ($request->isSuccessful())
				{
					return $request->getBody();
				}
			}
			catch (Zend_Http_Client_Exception $e)
			{
				return FALSE;
			}
	}

	public static function options()
	{
		$options = array();
		$phases = array();
		$templates = array();
		return array(
			'options' => $options, 
			'phases' => $phases, 
			'templates', $templates
		);
	}
	
	public static function about()
	{
		$details = array();
		$details['version'] = '1.0.0';
		$details['versionDecimal'] = '1';
		$details['shortName'] = 'isgd';
		$details['longName'] = 'is.gd';
		$details['decription'] = 'Shortens the URL via is.gd\'s API.';
		$details['available'] = true;
		$details['author'] = 'Cezz';
		$details['installed'] = true;
		$details['advanced'] = false;
		
		return $details;
	}
}
