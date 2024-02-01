<?php

/**
 * @package  Travel request
 * @author   Said Omar <saidmbugua@gmail.com>
 * @version  1.0.0
 */


namespace Module\Travel_request\Controller;

class Travel_request extends \Module\Travel_request\Base
{

	private $removeKeys = [];


	public function _getDashboard(\Base $f3, $params)
	{
		$is_backend = $this->is_backend($params);
		if (!$is_backend) $this->response = new \View\Frontend();

		$this->_setLayout('travel_request', "main.html");

		$this->response->data['pagetitle'] = 'Travel request';
		$this->response->data['search'] = array('url' => $is_backend ? \Config::instance()->admin_url . '/travel_request/search' : 'travel_request/search', 'module' => str_replace('_', ' ', 'Travel_request'));
	}


	public function _getList(\Base $f3, $params)
	{
		$is_api = $this->is_api($params);
		$is_backend = $this->is_backend($params);
		if ($is_api) {
			$this->response = new \View\JSON();
		} else {
			if (!$is_backend) $this->response = new \View\Frontend();
			$this->_setLayout('travel_request',  $is_backend ? "backend/travel_request/list.html" : "frontend/travel_request/list.html");
			$this->response->data['pagetitle'] = 'Travel request';
		}

		$item = new \Module\Travel_request\Model\Travel_request();
		$page = \Pagination::findCurrentPage();

		$pg = false;
		if (@$params['page'] || @$f3->get('GET.page'))
			$pg = true;

		if ($is_api && !$pg) {

			$records = $item->find(
				null,
				array('order' => 'created_at DESC')
			);
		} else {
			$records = $item->paginate(
				$page - 1,
				15,
				null,
				array('order' => 'created_at DESC')
			);
		}


		if ($is_api) {
			$this->response->data = $pg ? $this->JsonPaginateData($records, '\Module\Travel_request\Model\Travel_request', 1, $this->removeKeys) : $this->JsonFindData($records, '\Module\Travel_request\Model\Travel_request', 1, $this->removeKeys);
		} else {
			$this->response->data['content'] = $records;
			$this->response->data['search'] = array('url' => $is_backend ? \Config::instance()->admin_url . '/travel_request/search' : 'travel_request/search', 'module' => str_replace('_', ' ', 'Travel_request'));
		}
	}

	public function _search(\Base $f3, $params)
	{
		$is_api = $this->is_api($params);
		$is_backend = $this->is_backend($params);
		if ($is_api) {
			$this->response = new \View\JSON();
		} else {
			if (!$is_backend) $this->response = new \View\Frontend();
			$this->_setLayout('travel_request',  $is_backend ? "backend/travel_request/list.html" : "frontend/travel_request/list.html");
			$this->response->data['pagetitle'] = 'Search results for: ' . ucfirst($f3->get('GET.term'));
		}

		$item = new \Module\Travel_request\Model\Travel_request();
		$page = \Pagination::findCurrentPage();

		$records = $item->paginate(
			$page - 1,
			15,
			array('name like ?', '%' . $f3->get('GET.term') . '%'),
			array('order' => 'created_at DESC')
		);

		if ($is_api) {
			$this->response->data =   $this->JsonPaginateData($records, '\Module\Travel_request\Model\Travel_request', 1, $this->removeKeys);
		} else {
			$this->response->data['content'] = $records;
			$this->response->data['search'] = array('url' => $is_backend ? \Config::instance()->admin_url . '/travel_request/search' : 'travel_request/search', 'module' => str_replace('_', ' ', 'Travel_request'));
		}
	}
	public function _getDetails(\Base $f3, $params)
	{
		$is_api = $this->is_api($params);
		$is_backend = $this->is_backend($params);
		if ($is_api) {
			$this->response = new \View\JSON();
		} else {
			if (!$is_backend) $this->response = new \View\Frontend();
			$this->_setLayout('travel_request',  $is_backend ? "backend/travel_request/details.html" : "frontend/travel_request/details.html");
		}

		$item = new \Module\Travel_request\Model\Travel_request();
		if (@$params['uid'] || @$f3->get('GET.uid')) {
			$uid = $params['uid'] ?? $f3->get('GET.uid');

			$item->load(array('uid = ?', $uid));
			$url = $is_backend ? "/" . \Config::instance()->admin_url . "/travel_request/list" : "/travel_request/list";
			if ($item->dry()) {
				$this->returnResponse($is_api, 'Record not found', $url, 'danger', 404);
			}
			if ($is_api) {
				$item_data = $this->cleanUserData($item->cast());
				$item_data = $this->CleanModuleDetails($item_data, $this->removeKeys);
				$this->response->data = $item_data;
			} else {
				$this->response->data['item'] = $item;
				$this->response->data['pagetitle'] = $item->name;
			}
		}
	}

	public function _Edit(\Base $f3, $params)
	{

		$is_backend = $this->is_backend($params);
		if (!$is_backend) $this->response = new \View\Frontend();
		$this->_setLayout('travel_request',  $is_backend ? "backend/travel_request/edit.html" : "frontend/travel_request/edit.html");
		$this->response->data['pagetitle'] = 'Create new';

		if (!\Module\User\Model\User::canEdit($f3->get('LoggedInUser'))) {
			$this->returnResponse(false, 'Sorry you are not allowed to view this page', null, 'danger', 403);
		}

		if (@$params['uid'] || @$f3->get('GET.uid')) {
			$uid = $params['uid'] ?? $f3->get('GET.uid');

			$item = new \Module\Travel_request\Model\Travel_request();
			$item->load(array('uid = ?', $uid));
			if ($item->dry()) {
				$url = $is_backend ? "/" . \Config::instance()->admin_url . "/travel_request/list" : "/travel_request/list";
				$this->returnResponse(false, 'Sorry record not found', $url, 'danger', 404);
			}

			$this->response->data['pagetitle'] = 'Edit ' . $item->name;
			$this->response->data['POST'] = $item;
		}
	}


	public function _Save(\Base $f3, $params)
	{


		$is_api = $this->is_api($params);
		$is_backend = $this->is_backend($params);
		if ($is_api) {
			$this->response = new \View\JSON();
			$this->setPostJsonData();
		} else {

			if (!\Module\User\Model\User::canEdit($f3->get('LoggedInUser'))) {
				$this->returnResponse($is_api, 'Sorry you are not allowed to view this page', null, 'danger', 403);
			}
			if (!$is_backend) $this->response = new \View\Frontend();
			$this->_setLayout('travel_request',  $is_backend ? "backend/travel_request/edit.html" : "frontend/travel_request/edit.html");
		}
		$this->response->data['pagetitle'] = 'Create new';

		//check required fields

		if (!$this->CheckRequiredFields('name')) return;

		$item = new \Module\Travel_request\Model\Travel_request();

		if (@$params['uid'] || @$f3->get('GET.uid')) {
			$uid = $params['uid'] ?? $f3->get('GET.uid');

			$item->load(array('uid = ?', $uid));
			if ($item->dry()) {
				$url = $is_backend ? "/" . \Config::instance()->admin_url . "/travel_request/list" : "/travel_request/list";
				$this->returnResponse(false, 'Sorry record not found', $url, 'danger', 404);
			}
			$this->response->data['pagetitle'] = 'Edit ' . $item->name;

			$f3->set('POST.id', $item->_id);


			//delete the attachemet if available
			if (@$_FILES['image']['size'] > 0) {
				if (isset($item->image)) {
					\Controller\General::deleteFolder($item->image);
					\Controller\General::deleteFolder($item->image_thumb);
				}
				$image = $this->uploadFIle('Travel_request', $f3->get('POST.image'), 'travel_request', 1200, null, true, 300);

				$f3->set('POST.image_thumb', $image['thumb']);
				$f3->set('POST.image', $image['image']);
			}
			$this->updateItem('\Module\Travel_request\Model\Travel_request', $f3->get('POST'));


			$item->load(array('_id = ?', $f3->get('POST.id')));
			$url = $is_backend ? "/" . \Config::instance()->admin_url . "/travel_request/details/" . $item->uid : "/travel_request/details/" . $item->uid;

			$this->returnResponse(false, 'Updated successfully!', $url, 'success', 201, ['uid' => $item->uid]);
		} else {

			$fields = $item->getFieldConfiguration();
			$item->copyfrom('POST', array_keys($fields));

			//upload Image
			if (@$_FILES['image']['size'] > 0) {
				$image = $this->uploadFIle('Travel_request', $f3->get('POST.image'), 'travel_request', 1200, null, true, 300);
				$item->image_thumb = $image['thumb'];
				$item->image = $image['image'];
			}
			$item->save();
			$savedItem = $item->get('_id');
			if (!$f3->get('ERROR')) {
				$item->load(array('_id = ?', $savedItem));

				$url = $is_backend ? "/" . \Config::instance()->admin_url . "/travel_request/details/" . $item->uid : "/travel_request/details/" . $item->uid;

				$this->returnResponse(false, 'Created successfully!', $url, 'success', 201, ['uid' => $item->uid]);
			}
		}
	}

	public function _Delete(\Base $f3, $params)
	{
		$is_api = $this->is_api($params);
		$is_backend = $this->is_backend($params);
		if ($is_api) {
			$this->response = new \View\JSON();
		} else {
			if (!\Module\User\Model\User::canDelete($f3->get('LoggedInUser'))) {
				$this->returnResponse(false, 'Sorry you are not allowed to view this page', null, 'danger', 403);
			}
			if (!$is_backend) $this->response = new \View\Frontend();
		}

		$item = new \Module\Travel_request\Model\Travel_request();

		if (@$params['uid'] || @$f3->get('GET.uid'))
			$uid = $params['uid'] ?? $f3->get('GET.uid');

		$item->load(array('uid =?', $uid));
		if ($item->dry()) {
			$this->returnResponse(false, 'Sorry record not found', null, 'danger', 404);
		} else {
			if ($item->image) {
				\Controller\General::deleteFolder($item->image);
				\Controller\General::deleteFolder($item->image_thumb);
			}

			$item->erase();
			//$f3->reroute($f3->get('SESSION.LastPageURL'));
			$url = $is_backend ? "/" . \Config::instance()->admin_url . "/travel_request/list" : "/travel_request/list";
			$this->returnResponse(false, 'Deleted successfully', $url, 'info', 200);
		}
	}
}
