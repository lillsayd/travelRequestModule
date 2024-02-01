<?php

/**
 * @package  Travel request
 * @author   Said Omar <saidmbugua@gmail.com>
 * @version  1.0.0
 */


namespace Module\Travel_request\Model;

class Travel_request extends \Model\Base
{

	protected $fieldConf = array(
			'uid' => array(
				'type' => \DB\SQL\Schema::DT_VARCHAR128,
				'required' => true,
				'unique' => true,
			),
			'name' => array(
				'type' => \DB\SQL\Schema::DT_VARCHAR128,
				'required' => true,
			),
			'slug' => array(
				'type' => \DB\SQL\Schema::DT_VARCHAR128,
			),
			'image' => array(
				'type' => \DB\SQL\Schema::DT_VARCHAR256,
			),
			'image_thumb' => array(
				'type' => \DB\SQL\Schema::DT_VARCHAR256,
			),
			'details' => array(
				'type' => \DB\SQL\Schema::DT_TEXT,
			),
			'author' => array(
				'belongs-to-one' => '\Module\User\Model\User',
			),
			'created_at' => array(
				'type' => \DB\SQL\Schema::DT_DATETIME
			),
		),
		$table = 'travel_request',
		$db = 'DB';

	public function set_name($val)
	{
		// auto create slug when setting a blog title
		$slug = \Web::instance()->slug($val);
		$i = 0;
		$newslug = $slug;
		if ($this->dry() || $slug != $this->slug)
			while ($this->findone(array('slug = ?', $newslug))) {
				$i++;
				$newslug = $slug . '-' . $i;
			}
		$this->set('slug', $newslug);
		return $val;
	}


	public function save()
	{
		/** @var Base $f3 */
		$f3 = \Base::instance();
		if (!$this->author)
			$this->author = $f3->get('BACKEND_USER')->_id;
		if (!$this->created_at)
			$this->touch('created_at');
		if (!$this->uid)
			$this->uid = str_replace('.', '', uniqid('', TRUE));
		return parent::save();
	}

	public static function getTravelRequest($id)
	{
		$f3 = \Base::instance();
		$travel_request = new self;
		$travel_request->load(array('uid = ?', $id));
		return $travel_request;
	}

	public static function getTravelRequests()
	{
		$f3 = \Base::instance();
		$travel_request = new self;
		return $travel_request->find(null, array('order' => 'created_at Desc'));
	}

	public static function getTravelRequestArrayList()
	{
		$f3 = \Base::instance();
		$array_list = '';
		$travel_request = new self;
		$ar = $travel_request->find(null, array('order' => 'created_at Desc'));
		if ($ar) {
			foreach ($ar as $a) {
				$array_list .= $array_list == '' ? $a->name : ',' . $a->name;
			}
		}
		return $array_list;
	}

	public static function getTravelRequestStats()
	{
		$f3 = \Base::instance();
		$stats = [];
		$travel_request = new self;
		$all = $travel_request->find(null, array('order' => 'created_at Desc'));

		if ($all) $stats['all'] = count($all);
		else $stats['all'] = 0;

		return $stats;
	}
}
