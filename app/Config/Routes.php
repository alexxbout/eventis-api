<?php

namespace Config;

use App\Controllers\SearchController;

// Create a new instance of our RouteCollection class.
$routes = Services::routes();

/*
 * --------------------------------------------------------------------
 * Router Setup
 * --------------------------------------------------------------------
 */
$routes->setDefaultNamespace('App\Controllers');
$routes->setDefaultController('Home');
$routes->setDefaultMethod('index');
$routes->setTranslateURIDashes(false);
$routes->set404Override();
// The Auto Routing (Legacy) is very dangerous. It is easy to create vulnerable apps
// where controller filters or CSRF protection are bypassed.
// If you don't want to define all routes, please use the Auto Routing (Improved).
// Set `$autoRoutesImproved` to true in `app/Config/Feature.php` and set the following to true.
// $routes->setAutoRoute(false);

/*
 * --------------------------------------------------------------------
 * Route Definitions
 * --------------------------------------------------------------------
 */

// GET : Used to retrieve a resource
// POST : Used to create a new resource
// PUT : Used to update a resource
// DELETE : Used to delete a resource

// We get a performance increase by specifying the default
// route since we don't have to scan directories.
$routes->get("/", "Home::index");

$routes->get("unauthorized", "Home::unauthorized");

$routes->get("image/(:alphanum)/(:segment)", "ImageController::getImage/$1/$2");

$routes->group("api", static function ($routes) {

    $routes->group("auth", static function ($routes) {
        $routes->post("login",    "AuthController::login");
        $routes->post("register", "RegistrationController::register");
    
        $routes->get("code/(:alphanum)",  "CodeController::getByCode/$1");
    });

    $routes->group("v1", static function ($routes) {

        $routes->group("emoji", static function ($routes) {
            $routes->get("", "EmojiController::getAll");
        });

        $routes->group("user", static function ($routes) {
            $routes->get("",                  "UserController::getAll");
            $routes->get("(:num)",            "UserController::getById/$1");
            $routes->post("",                 "UserController::add");
            $routes->put("(:num)",            "UserController::update/$1");
            $routes->post("image/(:num)",     "UserController::addImage/$1");
            $routes->delete("image/(:num)",   "UserController::removeImage/$1");
            $routes->put("deactivate/(:num)", "UserController::deactiveAccount/$1");
            $routes->put("reactivate/(:num)", "UserController::reactivateAccount/$1");
            $routes->put("password/(:num)",   "UserController::updatePassword/$1");
            $routes->get("foyer/(:num)",      "UserController::getByIdFoyer/$1");
            $routes->get("zip/(:num)",        "UserController::getByZip/$1");
            
            // Interests
            $routes->get("(:num)/interest",           "InterestController::getInterestsByUser/$1");
            $routes->post("(:num)/interest/(:num)",   "InterestController::add/$1/$2");
            $routes->delete("(:num)/interest/(:num)", "InterestController::remove/$1/$2");

            // Friends
            $routes->get("(:num)/friend",                  "FriendController::getAll/$1");
            $routes->get("(:num)/friend/(:num)",           "FriendController::isFriend/$1/$2");
            $routes->delete("(:num)/friend/(:num)",        "FriendController::remove/$1/$2");
            $routes->post("(:num)/friend/ask/(:num)",      "FriendController::askFriend/$1/$2");
            $routes->delete("(:num)/friend/reject/(:num)", "FriendController::rejectRequest/$1/$2");
            $routes->post("(:num)/friend/accept/(:num)",   "FriendController::add/$1/$2");
            $routes->get("(:num)/friend/pending/(:num)",   "FriendController::isPending/$1/$2");

            // Affinities
            $routes->get("(:num)/affinities",               "UserController::getAffinities/$1");

            $routes->group("blocked", static function ($routes) {
                $routes->get("(:num)",           "BlockedController::getAll/$1");
                $routes->get("(:num)/(:num)",    "BlockedController::isBlocked/$1/$2");
                $routes->post("(:num)/(:num)",   "BlockedController::add/$1/$2");
                $routes->delete("(:num)/(:num)", "BlockedController::remove/$1/$2");
            });
        });

        // Interests
        $routes->group("interest", static function ($routes) {
            $routes->get("", "InterestController::getAll");
        });

        $routes->group("notifications", static function ($routes) {
            $routes->get("(:num)",           "NotificationController::getNotifications/$1");
        }); 

        $routes->group("role", static function ($routes) {
            $routes->get("", "RoleController::getAll");
        });

        $routes->group("code", static function ($routes) {
            $routes->get("",             "CodeController::getAll");
            $routes->get("foyer/(:num)", "CodeController::getAllByFoyer/$1");
            $routes->post("",            "CodeController::add");
        });

        $routes->group("registration", static function ($routes) {
            $routes->get("", "RegistrationController::getAll");
        });

        $routes->group("foyer", static function ($routes) {
            $routes->get("",                "FoyerController::getAll");
            $routes->get("zip/(:alphanum)", "FoyerController::getAllByZip/$1");
            $routes->get("(:num)",          "FoyerController::getById/$1");
            $routes->post("",               "FoyerController::add");
        });

        $routes->group("event", static function ($routes) {
            $routes->get("",                       "EventController::getAll");
            $routes->get("zip/(:alphanum)",        "EventController::getByZip/$1");
            $routes->get("(:num)",                 "EventController::getById/$1");
            $routes->get("categories",             "EventController::getAllTypes");
            $routes->get("cal/(:num)/(:alphanum)", "EventController::getByDayAndZip/$1/$2");
            $routes->get("cal/(:num)",             "EventController::getByTime/$1");
            $routes->post("",                      "EventController::add");
            $routes->put("(:num)",                 "EventController::updateData/$1");
            $routes->put("cancel/(:num)",          "EventController::cancel/$1");
            $routes->put("uncancel/(:num)",        "EventController::uncancel/$1");
            $routes->post("image/(:num)",          "EventController::addImage/$1");

            // Participants
            $routes->post("(:num)/participant/(:num)",   "ParticipantController::add/$1/$2");
            $routes->delete("(:num)/participant/(:num)", "ParticipantController::remove/$1/$2");
            $routes->get("(:num)/participant",           "ParticipantController::getAll/$1");
            $routes->get("(:num)/participant/(:num)",    "ParticipantController::isParticipating/$1/$2");
        });

        $routes->get("search/(:any)", 'SearchController::getSearch/$1');
    });
});

/*
 * --------------------------------------------------------------------
 * Additional Routing
 * --------------------------------------------------------------------
 *
 * There will often be times that you need additional routing and you
 * need it to be able to override any defaults in this file. Environment
 * based routes is one such time. require() additional route files here
 * to make that happen.
 *
 * You will have access to the $routes object within that file without
 * needing to reload it.
 */
if (is_file(APPPATH . 'Config/' . ENVIRONMENT . '/Routes.php')) {
    require APPPATH . 'Config/' . ENVIRONMENT . '/Routes.php';
}
