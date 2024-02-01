<?php

/**
 * @package  Travel request
 * @author   Said Omar <saidmbugua@gmail.com>
 * @version  1.0.0
 */

namespace Module\Travel_request;

class Base extends \Controller\Modules
{
    protected $_navLinks = array();
    protected $moduleAssets = array();

    public function __construct()
    {

        $f3 = \Base::instance();
        $LoggedInUser = \Module\User\Controller\Auth::isLoggedIn();
        $f3->set('LoggedInUser', $LoggedInUser);
        //Setup injectible html
        $this->_setModuleTemplateSection('travel_request', 'search', 'search.html');
    }

    //run the setup to install the required if any dependancies
    public function _setup()
    {
        $cfg = \Module\Travel_request\Config::instance();
        if (!$cfg->exists('menu'))
            $cfg->set('menu', array('menu_name' =>  ucwords((str_replace("_", " ", 'Travel_request'))), 'menu_icon' => 'fa-solid fa-angle-right'));
        $cfg->save();

        /* $default_module_item = array('default');
        $d = new \Module\Travel_request\Model\Travel_request();

        foreach ($default_module_item as $i) {
            $d->load(array('name = ?', $i));
            if ($d->dry()) {
                $d->name = $i;
                $d->save();
            }
        } */
    }

    public function _addRoutes()
    {
        $f3 = \Base::instance();
        $f3->route(
            [
                'GET /travel_request/list',
                'GET /travel_request/list/page/@page',
                'GET /api/travel_request/list',
                'GET /api/travel_request/list/page/@page',

            ],
            'Module\Travel_request\Controller\Travel_request->_getList'
        );
        $f3->route(
            [
                'GET /travel_request/details/@uid',
                'GET /api/travel_request/details',
                'GET /api/travel_request/details/@uid',
            ],
            'Module\Travel_request\Controller\Travel_request->_getDetails'
        );
        $f3->route(
            [
                'GET /api/travel_request/search',
                'GET /travel_request/search'
            ],
            'Module\Travel_request\Controller\Travel_request->_search'
        );
        $f3->route(
            [
                'GET /api/travel_request/delete',
                'GET /api/travel_request/delete/@uid'
            ],
            'Module\Travel_request\Controller\Travel_request->_Delete'
        );

        $f3->route(
            [
                'POST /api/travel_request/new',
                'POST /api/travel_request/update/@uid'
            ],
            'Module\Travel_request\Controller\Travel_request->_Save'
        );


        if ($f3->get('LoggedInUser') && \Module\User\Model\User::canAccessBackend($f3->get('LoggedInUser'))) {
            $f3->route('GET /' . \Config::instance()->admin_url . '/travel_request', 'Module\Travel_request\Controller\Travel_request->_getDashboard');
            $f3->route(
                [
                    'GET /' . \Config::instance()->admin_url . '/travel_request/list',
                    'GET /' . \Config::instance()->admin_url . '/travel_request/list/page/@page'
                ],
                'Module\Travel_request\Controller\Travel_request->_getList'
            );
            $f3->route('GET /' . \Config::instance()->admin_url . '/travel_request/search', 'Module\Travel_request\Controller\Travel_request->_search');
            $f3->route('GET /' . \Config::instance()->admin_url . '/travel_request/details/@uid', 'Module\Travel_request\Controller\Travel_request->_getDetails');
            $f3->route(
                [
                    'GET /' . \Config::instance()->admin_url . '/travel_request/new',
                    'GET /' . \Config::instance()->admin_url . '/travel_request/edit/@uid'
                ],
                'Module\Travel_request\Controller\Travel_request->_Edit'
            );
            $f3->route('GET /' . \Config::instance()->admin_url . '/travel_request/delete/@uid', 'Module\Travel_request\Controller\Travel_request->_Delete');
            $f3->route(
                [
                    'POST /' . \Config::instance()->admin_url . '/travel_request/new',
                    'POST /' . \Config::instance()->admin_url . '/travel_request/update/@uid'
                ],
                'Module\Travel_request\Controller\Travel_request->_Save'
            );

            //settings
            if (\Module\User\Model\User::isAdmin($f3->get('LoggedInUser')))
                $f3->route('GET|POST /' . \Config::instance()->admin_url . '/travel_request/settings', 'Module\Travel_request\Controller\Settings->_EditSettings');
        }
    }

    public function _addNavLinks()
    {
        $f3 = \Base::instance();
        $this->_navLinks['backend'] = array(
            array(
                'name' => 'Dashboard',
                'url' => \Config::instance()->admin_url . '/travel_request',
                'icon' => 'fa-solid fa-chart-line',
                'access' => true,
                'is_active' => ($f3->get('PATH') == '/' . \Config::instance()->admin_url . '/travel_request'),
            ),
            array(
                'name' => 'Travel_request',
                'url' => \Config::instance()->admin_url . '/travel_request/list',
                'count' =>  0,
                'is_active' => (str_contains($f3->get('PATH'), \Config::instance()->admin_url . '/travel_request') &&
                    !str_contains($f3->get('PATH'), \Config::instance()->admin_url . '/travel_request/settings') &&
                    $f3->get('PATH') != '/' . \Config::instance()->admin_url . '/travel_request'),
                'submenu' => array(
                    [
                        'name' => 'list',
                        'url' => \Config::instance()->admin_url . '/travel_request/list',
                        'icon' => 'fa-solid fa-list-check',
                        'count' =>  0,
                        'access' => true,
                        'is_active' => (str_contains($f3->get('PATH'), \Config::instance()->admin_url . '/travel_request/list')),
                    ],
                    [
                        'name' => 'Add travel_request',
                        'url' => \Config::instance()->admin_url . '/travel_request/new',
                        'icon' => 'fa-solid fa-plus',
                        'access' => true,
                        'is_active' => (str_contains($f3->get('PATH'), \Config::instance()->admin_url . '/travel_request/new') || str_contains($f3->get('PATH'), \Config::instance()->admin_url . '/travel_request/update')),
                    ],
                ),
                'icon' => 'fa-solid fa-minus',
                'access' => true
            ),
            array(
                'name' => 'Settings',
                'url' => \Config::instance()->admin_url . '/travel_request/settings',
                'icon' => 'fa-solid fa-screwdriver-wrench',
                'access' => \Module\User\Model\User::isAdmin($f3->get('LoggedInUser')),
                'is_active' => (str_contains($f3->get('PATH'), \Config::instance()->admin_url . '/travel_request/settings')),
            ),
        );
        $this->_navLinks['frontend'] = array(
            array(
                'name' => 'Travel_request',
                'url' => 'travel_request/list',
                'count' =>  0,
                'icon' => 'fa-solid fa-minus',
                'access' => true,
                'is_active' => (str_contains($f3->get('PATH'), \Config::instance()->admin_url . '/travel_request')),
            )
        );

        $cfg = \Module\Travel_request\Config::instance();
        $this->_navLinks['icon'] = $cfg->exists('menu') ? $cfg->menu['menu_icon'] : null;
        $this->_navLinks['menu_name'] = $cfg->exists('menu') ? $cfg->menu['menu_name'] : null;
        $this->_navLinks['count'] = 0;
        $this->_navLinks['is_active'] = $this->checkActiveUrl($this->_navLinks);
        $this->_navLinks['access'] = true;


        return $this->_navLinks;
    }
    public function _moduleAssets()
    {
        $this->moduleAssets['css'] = array('travel_request.css');
        $this->moduleAssets['js'] = array('travel_request.js');
        return $this->moduleAssets;
    }
}
