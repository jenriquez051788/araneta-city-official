<?php
/**
 * Routes configuration.
 *
 * In this file, you set up routes to your controllers and their actions.
 * Routes are very important mechanism that allows you to freely connect
 * different URLs to chosen controllers and their actions (functions).
 *
 * It's loaded within the context of `Application::routes()` method which
 * receives a `RouteBuilder` instance `$routes` as method argument.
 *
 * CakePHP(tm) : Rapid Development Framework (https://cakephp.org)
 * Copyright (c) Cake Software Foundation, Inc. (https://cakefoundation.org)
 *
 * Licensed under The MIT License
 * For full copyright and license information, please see the LICENSE.txt
 * Redistributions of files must retain the above copyright notice.
 *
 * @copyright     Copyright (c) Cake Software Foundation, Inc. (https://cakefoundation.org)
 * @link          https://cakephp.org CakePHP(tm) Project
 * @license       https://opensource.org/licenses/mit-license.php MIT License
 */

use Cake\Routing\Route\DashedRoute;
use Cake\Routing\RouteBuilder;

/*
 * This file is loaded in the context of the `Application` class.
 * So you can use `$this` to reference the application class instance
 * if required.
 */
return function (RouteBuilder $routes): void {
    /*
     * The default class to use for all routes
     *
     * The following route classes are supplied with CakePHP and are appropriate
     * to set as the default:
     *
     * - Route
     * - InflectedRoute
     * - DashedRoute
     *
     * If no call is made to `Router::defaultRouteClass()`, the class used is
     * `Route` (`Cake\Routing\Route\Route`)
     *
     * Note that `Route` does not do any inflections on URLs which will result in
     * inconsistently cased URLs when used with `{plugin}`, `{controller}` and
     * `{action}` markers.
     */
    $routes->setRouteClass(DashedRoute::class);

    $routes->scope('/', function (RouteBuilder $builder): void {
        /*
         * Connect '/' (base path) to the Mainpages controller's 'home' action,
         * which renders the public Araneta City homepage (templates/Mainpages/home.php).
         */
        $builder->connect('/', ['controller' => 'Mainpages', 'action' => 'home']);

        /*
         * News and Happenings routes
         */
        $builder->connect('/news', ['controller' => 'Mainpages', 'action' => 'news']);
        $builder->connect('/news-article/*', ['controller' => 'Mainpages', 'action' => 'newsArticle']);
        $builder->connect('/happenings', ['controller' => 'Mainpages', 'action' => 'happenings']);
        $builder->connect('/happening/*', ['controller' => 'Mainpages', 'action' => 'happeningDetail']);
        $builder->connect('/shopping', ['controller' => 'Mainpages', 'action' => 'shopping']);
        $builder->connect('/dining', ['controller' => 'Mainpages', 'action' => 'dining']);
        $builder->connect('/services', ['controller' => 'Mainpages', 'action' => 'services']);
        $builder->connect('/transport', ['controller' => 'Mainpages', 'action' => 'transport']);
        $builder->connect('/transport/{slug}', ['controller' => 'Mainpages', 'action' => 'transportDetail']);
        $builder->connect('/events', ['controller' => 'Mainpages', 'action' => 'events']);
        $builder->connect('/event/*', ['controller' => 'Mainpages', 'action' => 'eventDetail']);
        $builder->connect('/shows', ['controller' => 'Mainpages', 'action' => 'shows']);
        $builder->connect('/cinema', ['controller' => 'Mainpages', 'action' => 'cinema']);
       $builder->connect('/residences', ['controller' => 'Mainpages', 'action' => 'residences']);
        $builder->connect('/residence/{slug}', ['controller' => 'Mainpages', 'action' => 'residenceDetail'])
            ->setPass(['slug'])->setPatterns(['slug' => '[a-zA-Z0-9\-]+']);
        $builder->connect('/malls', ['controller' => 'Mainpages', 'action' => 'malls']);
        $builder->connect('/about', ['controller' => 'Mainpages', 'action' => 'about']);
        $builder->connect('/about/management', ['controller' => 'Mainpages', 'action' => 'aboutManagement']);
        $builder->connect('/privacy-policy', ['controller' => 'Mainpages', 'action' => 'policy']);
        $builder->connect('/faqs', ['controller' => 'Mainpages', 'action' => 'faqs']);
        $builder->connect('/contact', ['controller' => 'Mainpages', 'action' => 'contact']);
        $builder->connect('/listing', ['controller' => 'Mainpages', 'action' => 'listing']);
        $builder->connect('/parking', ['controller' => 'Mainpages', 'action' => 'parkinglist']);
        $builder->connect('/offices', ['controller' => 'Mainpages', 'action' => 'offices']);
        $builder->connect('/news', ['controller' => 'Mainpages', 'action' => 'news']);
        $builder->connect('/attractions', ['controller' => 'Mainpages', 'action' => 'attractions']);
        $builder->connect('/attraction/{slug}', ['controller' => 'Mainpages', 'action' => 'attractionDetail']);
        $builder->connect('/offices', ['controller' => 'Mainpages', 'action' => 'offices']);
        $builder->connect('/office/{slug}', ['controller' => 'Mainpages', 'action' => 'officeDetail']);
        $builder->connect('/hotels', ['controller' => 'Mainpages', 'action' => 'hotels']);
        $builder->connect('/hotel/{id}', ['controller' => 'Mainpages', 'action' => 'hotelDetail']);
        $builder->connect('/parking', ['controller' => 'Mainpages', 'action' => 'parkinglist']);
        $builder->connect('/establishment/{id}', ['controller' => 'Mainpages', 'action' => 'establishmentDetail']);

        /*
         * ...and connect the rest of 'Pages' controller's URLs.
         */
        $builder->connect('/pages/*', 'Pages::display');

        /*
         * Authentication routes (CMS login / logout)
         */
        $builder->connect('/login', ['controller' => 'Users', 'action' => 'login']);
        $builder->connect('/logout', ['controller' => 'Users', 'action' => 'logout']);
        $builder->connect('/forgot-password', ['controller' => 'Users', 'action' => 'forgotPassword']);
        $builder->connect('/reset-password/{token}', ['controller' => 'Users', 'action' => 'resetPassword'])
            ->setPass(['token'])->setPatterns(['token' => '[a-f0-9]+']);

        /*
         * Admin routes - User management
         */
        $builder->connect('/users', ['controller' => 'Users', 'action' => 'index']);
        $builder->connect('/users/add', ['controller' => 'Users', 'action' => 'add']);
        $builder->connect('/users/view/{id}', ['controller' => 'Users', 'action' => 'view'])
            ->setPass(['id'])->setPatterns(['id' => '\d+']);
        $builder->connect('/users/edit/{id}', ['controller' => 'Users', 'action' => 'edit'])
            ->setPass(['id'])->setPatterns(['id' => '\d+']);
        $builder->connect('/users/delete/{id}', ['controller' => 'Users', 'action' => 'delete'])
            ->setPass(['id'])->setPatterns(['id' => '\d+']);

        /*
         * Admin routes - Role management (superadmin only)
         */
        $builder->connect('/roles', ['controller' => 'Roles', 'action' => 'index']);
        $builder->connect('/roles/add', ['controller' => 'Roles', 'action' => 'add']);
        $builder->connect('/roles/view/{id}', ['controller' => 'Roles', 'action' => 'view'])
            ->setPass(['id'])->setPatterns(['id' => '\d+']);
        $builder->connect('/roles/edit/{id}', ['controller' => 'Roles', 'action' => 'edit'])
            ->setPass(['id'])->setPatterns(['id' => '\d+']);
        $builder->connect('/roles/delete/{id}', ['controller' => 'Roles', 'action' => 'delete'])
            ->setPass(['id'])->setPatterns(['id' => '\d+']);

        /*
         * Admin routes - Mainpages CRUD
         */
        $builder->connect('/mainpages', 'Mainpages::index');
        $builder->connect('/mainpages/add', 'Mainpages::add');
        $builder->connect('/mainpages/edit/:id', 'Mainpages::edit');
        $builder->connect('/mainpages/view/:id', 'Mainpages::view');

        /*
         * Admin routes - Contact submissions
         */
        $builder->connect('/contacts', ['controller' => 'Contacts', 'action' => 'index']);
        $builder->connect('/contacts/view/{id}', ['controller' => 'Contacts', 'action' => 'view'])
            ->setPass(['id'])->setPatterns(['id' => '\d+']);
        $builder->connect('/contacts/edit/{id}', ['controller' => 'Contacts', 'action' => 'edit'])
            ->setPass(['id'])->setPatterns(['id' => '\d+']);
        $builder->connect('/contacts/delete/{id}', ['controller' => 'Contacts', 'action' => 'delete'])
            ->setPass(['id'])->setPatterns(['id' => '\d+']);

        /*
         * Admin routes - Team Members (CMS)
         */
        $builder->connect('/team-members', ['controller' => 'TeamMembers', 'action' => 'index']);
        $builder->connect('/team-members/add', ['controller' => 'TeamMembers', 'action' => 'add']);
        $builder->connect('/team-members/view/{id}', ['controller' => 'TeamMembers', 'action' => 'view'])
            ->setPass(['id'])->setPatterns(['id' => '\d+']);
        $builder->connect('/team-members/edit/{id}', ['controller' => 'TeamMembers', 'action' => 'edit'])
            ->setPass(['id'])->setPatterns(['id' => '\d+']);
        $builder->connect('/team-members/delete/{id}', ['controller' => 'TeamMembers', 'action' => 'delete'])
            ->setPass(['id'])->setPatterns(['id' => '\d+']);

        /*
         * Admin routes - Residences (CMS)
         * Uses /manage-residences to avoid conflict with the public /residences page.
         */
        $builder->connect('/manage-residences', ['controller' => 'Residences', 'action' => 'index']);
        $builder->connect('/manage-residences/add', ['controller' => 'Residences', 'action' => 'add']);
        $builder->connect('/manage-residences/view/{id}', ['controller' => 'Residences', 'action' => 'view'])
            ->setPass(['id'])->setPatterns(['id' => '\d+']);
        $builder->connect('/manage-residences/edit/{id}', ['controller' => 'Residences', 'action' => 'edit'])
            ->setPass(['id'])->setPatterns(['id' => '\d+']);
        $builder->connect('/manage-residences/delete/{id}', ['controller' => 'Residences', 'action' => 'delete'])
            ->setPass(['id'])->setPatterns(['id' => '\d+']);
        $builder->connect('/manage-residences/add-phase/{id}', ['controller' => 'Residences', 'action' => 'addPhase'])
            ->setPass(['id'])->setPatterns(['id' => '\d+']);
        $builder->connect('/manage-residences/delete-phase/{id}', ['controller' => 'Residences', 'action' => 'deletePhase'])
            ->setPass(['id'])->setPatterns(['id' => '\d+']);
        $builder->connect('/manage-residences/add-gallery/{id}', ['controller' => 'Residences', 'action' => 'addGallery'])
            ->setPass(['id'])->setPatterns(['id' => '\d+']);
        $builder->connect('/manage-residences/delete-gallery/{id}', ['controller' => 'Residences', 'action' => 'deleteGallery'])
            ->setPass(['id'])->setPatterns(['id' => '\d+']);

        /*
         * Connect catchall routes for all controllers.
         * NOTE: Enable fallbacks for standard RESTful routing
         */
        $builder->fallbacks();
    });

    /*
     * If you need a different set of middleware or none at all,
     * open new scope and define routes there.
     *
     * ```
     * $routes->scope('/api', function (RouteBuilder $builder): void {
     *     // No $builder->applyMiddleware() here.
     *
     *     // Parse specified extensions from URLs
     *     // $builder->setExtensions(['json', 'xml']);
     *
     *     // Connect API actions here.
     * });
     * ```
     */
};
