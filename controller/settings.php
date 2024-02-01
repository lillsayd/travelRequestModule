<?php

/**
 * @package  Travel request
 * @author   Said Omar <saidmbugua@gmail.com>
 * @version  1.0.0
 */


namespace Module\Travel_request\Controller;

class Settings extends \Module\Travel_request\Base
{

	public function _EditSettings(\Base $f3, $params)
	{
		if (\Module\User\Model\User::isAdmin($f3->get('LoggedInUser'))) {
			$this->_setLayout('travel_request',  "backend/settings.html");
			$this->response->data['pagetitle'] = str_replace('_', ' ', 'Travel_request') . ' Settings';

			$cfg = \Module\Travel_request\Config::instance();

			if ($f3->get('VERB') == 'POST') {

				$error = false;

				//menu Setup
				if ($f3->exists('POST.menu')) {
					$menu_array = array();
					foreach ($f3->get('POST.menu') as $key => $val) {
						$menu_array[str_replace("'", '', $key)] = $val;
					}
					$f3->set('POST.menu', $menu_array);
				}



				if ($f3->devoid('POST.menu.menu_name')) {
					$error = true;
					\Flash::instance()->addMessage('Menu Name is required', 'warning');
				} else {
					$cfg->menu['menu_name'] = $f3->get('POST.menu.menu_name');
				}
				if ($f3->devoid('POST.menu.menu_icon')) {
					$cfg->menu['menu_icon'] = 'fa fa-angle-double-right';
				} else {
					$cfg->menu['menu_icon'] = $f3->get('POST.menu.menu_icon');
				}

				if (!$error) {


					$cfg->save();
					//check if settup is set on module
					$M = new \Module\Travel_request\Base();
					if (method_exists($M, '_setup')) {
						$M->_setup();
					}

					\Flash::instance()->addMessage(str_replace('_', ' ', 'Travel_request') . ' Settings saved', 'success');

					$f3->reroute('/' . \Config::instance()->admin_url . '/Travel_request/settings');
				}
			}
			$cfg->copyto('POST');
		} else {
			\Flash::instance()->addMessage('Sorry you are not allowed to view this page', 'danger');
			$f3->reroute($f3->get('SESSION.LastPageURL'));
		}
	}
}
