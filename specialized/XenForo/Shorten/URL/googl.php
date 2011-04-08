<?php

class Shorten_URL_googl
{
	public static function shorten($url, $timeout = 10)
	{
		if(self::checkSettings())
		{
			$post_vars = array(
				"user" => XenForo_Application::get('options')->suMethod_googl_user,
				"url" => $url,
				"auth_token" => self::googlToken($url)
			);

		$fullurl = "http://goo.gl/api/url";
			try
			{
				$client = new Zend_Http_Client($fullurl, array(
					'timeout' => $timeout
				));

				$client->setParameterPost($post_vars);
				$client->setMethod(Zend_Http_Client::POST);

				$request = $client->request();
				if ($request->isSuccessful())
				{
					$body = json_decode($request->getBody(),true);
					return $body['short_url'];
				}
			}
			catch (Zend_Http_Client_Exception $e)
			{
				return false;
			}
		}
		return array('error' => 1, 'text' => 'Google Username is missing');
	}
	
	public static function options()
	{
		$options = array(
			'user' => array(
				'type' => 'textbox', 
				'default' => '', //optional
				'parameters' => '', //optional
				'label' => 'Google email address (Required)',
				'description' => 'Enter your Google email address' //optional
			)
		);
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
		$details['shortName'] = 'googl';
		$details['longName'] = 'goo.gl Shortener';
		$details['decription'] = 'Uses goo.gl\'s API to shorten URL\'s this requires a googl username';
		$details['available'] = self::checkSettings();
		$details['author'] = 'Cezz';
		$details['installed'] = self::checkInstalled();
		$details['advanced'] = true;
		
		return $details;
	}

	public static function checkSettings()
	{
		return (XenForo_Application::get('options')->suMethod_googl_user != '') ? true : false;
	}
	
	public static function checkInstalled()
	{
		return (isset(XenForo_Application::get('options')->suMethod_googl_user)) ? true : false;
	}

	//token code
	private static function  googlToken($b){
		$i = self::tke($b);
		$i = $i >> 2 & 1073741823;
		$i = $i >> 4 & 67108800 | $i & 63;
		$i = $i >> 4 & 4193280 | $i & 1023;
		$i = $i >> 4 & 245760 | $i & 16383;
		$j = "7";
		$h = self::tkf($b);
		$k = ($i >> 2 & 15) << 4 | $h & 15;
		$k |= ($i >> 6 & 15) << 12 | ($h >> 8 & 15) << 8;
		$k |= ($i >> 10 & 15) << 20 | ($h >> 16 & 15) << 16;
		$k |= ($i >> 14 & 15) << 28 | ($h >> 24 & 15) << 24;
		$j .= self::tkd($k);
		return $j;
	}

	private static function  tkc(){
		$l = 0;
		foreach(func_get_args() as $val){
			$val &= 4294967295;
			$val += $val > 2147483647 ? -4294967296 : ($val < -2147483647 ? 4294967296 : 0);
			$l   += $val;
			$l   += $l > 2147483647 ? -4294967296 : ($l < -2147483647 ? 4294967296 : 0);
		}
		return $l;
	}

	private static function  tkd($l){
		$l = $l > 0 ? $l : $l + 4294967296;
		$m = "$l";  //deve ser uma string
		$o = 0;
		$n = false;
		for($p = strlen($m) - 1; $p >= 0; --$p){
			$q = $m[$p];
			if($n){
				$q *= 2;
				$o += floor($q / 10) + $q % 10;
			} else {
				$o += $q;
			}
			$n = !$n;
		}
		$m = $o % 10;
		$o = 0;
		if($m != 0){
			$o = 10 - $m;
			if(strlen($l) % 2 == 1){
				if ($o % 2 == 1){
					$o += 9;
				}
				$o /= 2;
			}
		}
		return "$o$l";
	}

	private static function tke($l){
		$m = 5381;
		for($o = 0; $o < strlen($l); $o++){
			$m = self::tkc($m << 5, $m, ord($l[$o]));
		}
		return $m;
	}

	private static function  tkf($l){
		$m = 0;
		for($o = 0; $o < strlen($l); $o++){
			$m = self::tkc(ord($l[$o]), $m << 6, $m << 16, -$m);
		}
		return $m;
	}
}
