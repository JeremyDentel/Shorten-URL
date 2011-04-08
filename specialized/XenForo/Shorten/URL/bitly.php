<?php

class Shorten_URL_bitly
{	
	/* shorten($url, $timeout = 10) must be the name of your shortener
		as it is called dynamically via Shorten_URL. It must have exactly
		these two options.
	*/
	public static function shorten($url, $timeout = 10)
	{
		$_api = XenForo_Application::get('options')->suMethod_bitly_API;
		$_user = XenForo_Application::get('options')->suMethod_bitly_user;
		
		if(self::checkSettings())
		{
			$fullurl = "http://api.bit.ly/v3/shorten?login=".$_user."&apiKey=".$_api."&longUrl=".$url."&format=json";
			try
			{
				$client = new Zend_Http_Client($fullurl, array(
					'timeout' => $timeout
				));

				$request = $client->request();

				if ($request->isSuccessful())
				{
					$body = json_decode($request->getBody(),true);
					return $body['status_code'] == 200 ? $body['data']['url'] : array('error' => 1, 'text' => $body['status_code']);
				}
			}
			catch (Zend_Http_Client_Exception $e)
			{
				return false;
			}
		}
		return array('error' => 1, 'text' => 'API Key Or Username are missing');
	}
	
	/* options() is called by Shorten_URL dynamically and
		is expected to return an array of options with
		optionKey => optionValues as the setup. This allows
		Shorten_URL::install() to properly add options
	*/
	public static function options()
	{
		$options = array(
			'API' => array(
				'type' => 'textbox', 
				'default' => '', //optional
				'parameters' => '', //optional
				'label' => 'Bitly API Key (Required)',
				'description' => 'Enter your Bit.ly API key here' //optional
			),
			'user' => array(
				'type' => 'textbox', 
				'default' => '', //optional
				'parameters' => '', //optional
				'label' => 'Bitly username (Required)',
				'description' => 'Enter your Bit.ly username here' //optional
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
	
	/* about() is expected to return an array with the info
		shown below.
	*/
	public static function about()
	{
		$details = array();
		$details['version'] = '1.0.0';
		$details['versionDecimal'] = '1';
		$details['shortName'] = 'bitly';
		$details['longName'] = 'bit.ly Shortener';
		$details['decription'] = 'Uses bit.ly\'s API to shorten URL\'s more info at http://bit.ly/, this requires a bit.ly API key and username';
		$details['available'] = self::checkSettings();
		$details['author'] = 'Cezz';
		$details['installed'] = self::checkInstalled();
		$details['advanced'] = true;
		
		return $details;
	}

	/* checkSettings() is only required on advanced shorteners
		and verifies the settings are set before use. Should
		be called by about().
	*/
	public static function checkSettings()
	{
		return (XenForo_Application::get('options')->suMethod_bitly_API != '' && XenForo_Application::get('options')->suMethod_bitly_user != '') ? true : false;
	}

	/* checkInstalled() is only required on advanced shorteners
		and verifies the settings are in the options. Should
		be called by about().
	*/	
	public static function checkInstalled()
	{
		if(isset(XenForo_Application::get('options')->suMethod_bitly_API) && isset(XenForo_Application::get('options')->suMethod_bitly_user))
		{
			return true;
		}
		
		return false;
	}	
}
