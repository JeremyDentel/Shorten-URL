<?php

class Shorten_URL_urly
{
	public static function shorten($url,$timeout = 10)
	{
		$fullurl = "http://ur.ly/new.json?href=".$url;
			try
			{
				$client = new Zend_Http_Client($fullurl, array(
					'timeout' => $timeout
				));

				$request = $client->request();

				if ($request->isSuccessful())
				{
					$body = json_decode($request->getBody(),true);
					return 'http://ur.ly/'.$body['code'];
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
		$details['shortName'] = 'urly';
		$details['longName'] = 'ur.ly';
		$details['decription'] = 'Shortens the URL via ur.ly\'s API.';
		$details['available'] = true;
		$details['author'] = 'Cezz';
		$details['installed'] = true;
		$details['advanced'] = false;
		
		return $details;
	}
}
