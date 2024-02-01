<?php

/**
 * @package  Travel request
 * @author   Said Omar <saidmbugua@gmail.com>
 * @version  1.0.0
 */


namespace Module\Travel_request;

class Config extends \DB\Jig\Mapper
{

	public function __construct()
	{
		$db = new \DB\Jig('modules/travel_request/');
		parent::__construct($db, 'config.json');
		$this->load();
	}

	public function expose()
	{
		$arr = $this->cast();
		\Base::instance()->mset($arr);
	}

	static public function instance()
	{
		if (\Registry::exists('Travel_request_config'))
			$travel_request_config = \Registry::get('Travel_request_config');
		else {
			$travel_request_config = new self;
			\Registry::set('Travel_request_config', $travel_request_config);
		}
		return $travel_request_config;
	}
}