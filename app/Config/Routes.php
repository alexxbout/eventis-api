<?php

namespace Config;

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

// Unauthorized
$routes->get("unauthorized", "Home::unauthorized");

$routes->group("api", static function ($routes) {

    $routes->group("auth", static function ($routes) {
        $routes->get("login",        "AuthController::login"); // Connexion
        // $routes->post("register", "AuthController::register"); // Inscription
    });

    $routes->group("v1", static function ($routes) {
        $routes->group("user", static function ($routes) {


            //Friends
            $routes->get("(:num)/friend",           "FriendController::getAll/$1"); // Tous les amis d'un utilisateur
            $routes->get("(:num)/friend/(:num)",    "FriendController::isFriend/$1/$2");// Vérifie si deux utilisateurs sont amis

            //$routes->post("(:num)/friend/(:num)",   "FriendController::add/$1/$2"); // Ajoute un ami

            $routes->delete("(:num)/friend/(:num)", "FriendController::remove/$1/$2"); // Supprime un ami
            
            $routes->post("(:num)/friend/ask/(:num)",      "FriendController::askFriend/$1/$2"); // Ajoute une demaande d'ami
            $routes->delete("(:num)/friend/reject/(:num)", "FriendController::rejectRequest/$1/$2");
            $routes->post("(:num)/friend/accept/(:num)",   "FriendController::add/$1/$2");

            //User
            $routes->get("",             "UserController::getAll"); // Tous les utilisateurs
            $routes->get("(:num)",       "UserController::getById/$1"); // Un utilisateur par son id
            $routes->get("foyer/(:num)", "UserController::getByIdFoyer/$1"); // Tous les utilisateurs d'un foyer
            $routes->get("role/(:num)",  "UserController::getByIdRole/$1"); // Tous les utilisateurs d'un rôle
            $routes->get("ref/(:num)",   "UserController::getByIdRef/$1"); // Tous les utilisateurs d'un référent
            $routes->post("add",             "UserController::add"); // Ajoute un utilisateur
            $routes->put("updateData",       "UserController::updateData"); // Met à jour un utilisateur
            $routes->put("updateLastLogin",  "UserController::updateLastLogin"); // Met à jour la date de dernière connexion d'un utilisateur
            $routes->put("updateLastLogout", "UserController::updateLastLogout"); // Met à jour la date de dernière déconnexion d'un utilisateur
        
        $routes->group("blocked", static function ($routes) {
            $routes->get("(:num)/(:num)",    "BlockedController::isBlocked/$1/$2"); // Vérifie si un utilisateur est bloqué par un autre $1=blocker $2  = bloqué
            $routes->get("(:num)",           "BlockedController::getAll/$1"); // Tous les utilisateurs bloqués par un utilisateur
            $routes->post("(:num)/(:num)",     "BlockedController::add/$1/$2"); // Ajoute un utilisateur bloqué
            $routes->delete("(:num)/(:num)", "BlockedController::remove/$1/$2"); // Supprime un utilisateur bloqué
        });
        });

        $routes->group("foyer", static function ($routes) {
            $routes->get("",       "FoyerController::getAll"); // Tous les foyers
            $routes->get("(:num)", "FoyerController::getById/$1"); // Un foyer par son id

            $routes->post("add",   "FoyerController::add"); // Ajoute un foyer
            $routes->put("update", "FoyerController::update"); // Met à jour un foyer
        });

        $routes->group("role", static function ($routes) {
            $routes->get("",         "RoleController::getAll"); // Tous les rôles
        });

        $routes->group("event", static function ($routes) {
            $routes->get("",                "EventController::getAll"); // Tous les événements
            $routes->get("(:num)",          "EventController::getById/$1"); // Un événement par son id
            $routes->get("zip/(:alphanum)", "EventController::getByZip/$1"); // Tous les événements par code postal

            $routes->post("add",       "EventController::add"); // Ajoute un événement
            $routes->put("updateData", "EventController::updateData"); // Met à jour un événement
            $routes->put("cancel",     "EventController::cancel"); // Annule un événement par son id
        });

        $routes->group("participant", static function ($routes) {
            $routes->get("(:num)",                      "ParticipantController::getAll/$1"); // Tous les participants d'un événement
            $routes->get("isParticipant/(:num)/(:num)", "ParticipantController::isParticipant/$1/$2"); // Vérifie si un utilisateur participe à un événement

            $routes->post("add", "ParticipantController::add"); // Ajoute un participant à un événement
        });

        $routes->group("code", static function ($routes) {
            $routes->get("",               "CodeController::getAll"); // Tous les codes
            $routes->get("(:num)",         "CodeController::getById/$1"); // Un code par son id
            $routes->get("check/(:alpha)", "CodeController::checkExist/$1"); // Vérifie si un code existe
            $routes->get("valid/(:alpha)", "CodeController::isValid/$1"); // Vérifie si un code est valide

            $routes->post("add",      "CodeController::add"); // Ajoute un code
            $routes->delete("delete", "CodeController::delete"); // Supprime un code
            $routes->put("use",       "CodeController::use"); // Utilise un code
        });



        $routes->group("blocked", static function ($routes) {
            $routes->get("(:num)",                  "BlockedController::getAll/$1"); // Tous les utilisateurs bloqués par un utilisateur
            $routes->get("isBlocked/(:num)/(:num)", "BlockedController::isBlocked/$1/$2"); // Vérifie si un utilisateur est bloqué par un autre

            $routes->post("add",      "BlockedController::add"); // Ajoute un utilisateur bloqué
            $routes->delete("remove", "BlockedController::remove"); // Supprime un utilisateur bloqué
        });
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
