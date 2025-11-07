<?php

use CodeIgniter\Router\RouteCollection;

$routes->group('api', function ($routes) {
    $routes->options('(:any)', function () {
        return $this->response->setStatusCode(200); // You can return more headers if needed  
    });

    $routes->post('login', 'AuthController::login');

    $routes->get('brands', 'ProductController::getBrands');

    $routes->post('media/upload', 'MediaController::uploadFile', ['filter' => 'authFilter']);
    $routes->post('media/delete', 'MediaController::deleteUploadedFile', ['filter' => 'authFilter']);

    $routes->group('templates', ['filter' => 'authFilter'], function ($routes) {
        $routes->get('', 'CmsTemplateController::index');
        $routes->get('all', 'CmsTemplateController::all');
        $routes->get('(:num)', 'CmsTemplateController::show/$1');
        $routes->post('', 'CmsTemplateController::save');
        $routes->put('(:num)', 'CmsTemplateController::save/$1');
        $routes->delete('(:num)', 'CmsTemplateController::delete/$1');
    });

    $routes->group('categories', ['filter' => 'authFilter'], function ($routes) {
        $routes->get('all', 'CategoryController::all');
        $routes->get('', 'CategoryController::index');
        $routes->put('(:num)', 'CategoryController::save/$1');
        $routes->get('check_sequence', 'CategoryController::checkSequence');
        $routes->get('initialize_sequence', 'CategoryController::initializeSequence');
        $routes->post('(:num)/move/(:segment)', 'CategoryController::move/$1/$2');
    });

    $routes->group('cms_items', ['filter' => 'authFilter'], function ($routes) {
        $routes->get('', 'CmsItemController::index');
        $routes->get('(:num)', 'CmsItemController::show/$1');
        $routes->post('', 'CmsItemController::save');
        $routes->put('(:num)', 'CmsItemController::save/$1');
        $routes->put('(:num)/activate', 'CmsItemController::activate/$1');
        $routes->delete('(:num)', 'CmsItemController::delete/$1');
        $routes->get('(:num)/last_sequence', 'CmsItemController::getLastSequence/$1');
        $routes->post('bulkaction', 'CmsItemController::bulkAction');
    });

    $routes->group('cms', ['filter' => 'authFilter'], function ($routes) {
        $routes->get('', 'CmsController::index');
        $routes->get('(:num)', 'CmsController::show/$1');
        $routes->get('expires', 'CmsController::expires');
        $routes->put('(:num)', 'CmsController::save/$1');
        $routes->post('', 'CmsController::save');
        $routes->delete('(:num)', 'CmsController::delete/$1');
        $routes->post('(:num)/activate', 'CmsController::activate/$1');
        $routes->post('(:num)/move/(:segment)', 'CmsController::moveUpDown/$1/$2');
        $routes->post('bulkaction', 'CmsController::bulkAction');
        $routes->post('check_daterange', 'CmsController::checkDateRange');
        $routes->post('copy/(:num)', 'CmsController::copy/$1');
    });

    $routes->group('managers', ['filter' => 'authFilter'], function ($routes) {
        $routes->get('', 'ManagerController::index');
        $routes->post('activate', 'ManagerController::activate');
        $routes->get('show/(:num)', 'ManagerController::show/$1');
        $routes->post('save/(:num)', 'ManagerController::save/$1');
        $routes->post('create', 'ManagerController::save');
    });

    $routes->group('manager_permissions', ['filter' => 'authFilter'], function ($routes) {
        $routes->get('', 'ManagerPermissionController::index');
        $routes->get('all', 'ManagerPermissionController::all');
    });

    $routes->group('branches', ['filter' => 'authFilter'], function ($routes) {
        $routes->get('all', 'BranchController::all');
        $routes->get('', 'BranchController::index');
        $routes->delete('(:num)', 'BranchController::delete/$1');
        $routes->get('show/(:num)', 'BranchController::show/$1');
        $routes->post('save/(:num)', 'BranchController::save/$1');
        $routes->post('create', 'BranchController::save');
    });

    $routes->group('organizations', ['filter' => 'authFilter'], function ($routes) {
        $routes->get('all', 'OrganizationController::all');
        $routes->get('', 'OrganizationController::index');
        $routes->delete('(:num)', 'OrganizationController::delete/$1');
        $routes->get('show/(:num)', 'OrganizationController::show/$1');
        $routes->post('save/(:num)', 'OrganizationController::save/$1');
        $routes->post('create', 'OrganizationController::save');
        $routes->post('bulkaction', 'OrganizationController::bulkAction');
    });

    $routes->group('siteconfig', ['filter' => 'authFilter'], function ($routes) {
        $routes->get('footer', 'SiteConfigController::indexFooterConfig');
        $routes->post('footer', 'SiteConfigController::saveFooterConfig');
        $routes->get('top_ribbon', 'SiteConfigController::indexTopRibbonConfig');
        $routes->post('top_ribbon', 'SiteConfigController::saveTopRibbonConfig');
    });

    $routes->group('activity_logs', ['filter' => 'authFilter'], function ($routes) {
        $routes->get('', 'ActivityLogController::index');
    });

    $routes->get('test/1', 'TestController::test1');
    // $routes->get('test/1', 'TestController::test1', ['filter' => 'authFilter']);
});

$routes->get('(:any)', 'HomeController::index');