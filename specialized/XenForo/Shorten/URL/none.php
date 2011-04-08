<?php

class Shorten_URL_none
{
	public static function shorten($url, $timeout = 10)
	{
		// Just a wrapper function. It'll return the full $url in case someone class Shorten_URL::shorten($url, 'none');
		return $url;
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
		$details['shortName'] = 'none';
		$details['longName'] = 'No URL Shortening';
		$details['decription'] = 'Doesn\'t shorten the URL.';
		$details['available'] = true;
		$details['author'] = 'King Kovifor';
		$details['installed'] = true;
		$details['advanced'] = false;
		
		return $details;
	}
}