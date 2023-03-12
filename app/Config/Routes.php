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
$routes->get('/', 'Home::index');

$routes->group("api", static function ($routes) {

    $routes->get("authenticate", "UserController::authenticate");

    $routes->group("v1", static function ($routes) {
        $routes->group("user", static function ($routes) {
            $routes->get("", "UserController::getAll"); // Tous les utilisateurs
            $routes->get("(:num)", "UserController::getById/$1"); // Un utilisateur par son id
            $routes->get("foyer/(:num)", "UserController::getByIdFoyer/$1"); // Tous les utilisateurs d'un foyer
            $routes->get("role/(:num)", "UserController::getByIdRole/$1"); // Tous les utilisateurs d'un rôle
            $routes->get("ref/(:num)", "UserController::getByIdRef/$1"); // Tous les utilisateurs d'un référent

            // Les paramètres sont dans le body
            $routes->post("add", "UserController::add"); // Ajoute un utilisateur
            $routes->put("data", "UserController::updateData"); // Met à jour un utilisateur
            $routes->put("login", "UserController::updateLastLogin"); // Met à jour la date de dernière connexion d'un utilisateur
            $routes->put("logout", "UserController::updateLastLogout"); // Met à jour la date de dernière déconnexion d'un utilisateur
            $routes->put("password", "UserController::updatePassword"); // Met à jour le mot de passe d'un utilisateur
        });

        $routes->group("foyer", static function ($routes) {
            $routes->get("", "FoyerController::getAll"); // Tous les foyers
            $routes->get("(:num)", "FoyerController::getById/$1"); // Un foyer par son id

            // Les paramètres sont dans le body
            $routes->post("add", "FoyerController::add"); // Ajoute un foyer
            $routes->put("update", "FoyerController::update"); // Met à jour un foyer
        });

        $routes->group("role", static function ($routes) {
            $routes->get("", "RoleController::getAll"); // Tous les rôles
            $routes->get("(:num)", "RoleController::getById/$1"); // Un rôle par son id
            $routes->get("(:alpha)", "RoleController::getByLibelle/$1"); // Un rôle par son libellé
        });

        $routes->group("event", static function ($routes) {
            $routes->get("", "EventController::getAll"); // Tous les événements
            $routes->get("(:num)", "EventController::getById/$1"); // Un événement par son id
            $routes->get("zip/(:alphanum)", "EventController::getByZip/$1"); // Tous les événements par code postal

            // Les paramètres sont dans le body
            $routes->post("add", "EventController::add"); // Ajoute un événement
            $routes->post("(:num)/image", "EventController::addImage/$1"); // Ajoute une image à un événement
            $routes->put("updateData", "EventController::updateData"); // Met à jour un événement
            $routes->put("cancel", "EventController::cancel"); // Annule un événement par son id
        });

        $routes->group("participant", static function ($routes) {
            $routes->get("(:num)", "ParticipantController::getAll/$1"); // Tous les participants d'un événement
            $routes->get("isParticipant/(:num)/(:num)", "ParticipantController::isParticipant/$1/$2"); // Vérifie si un utilisateur participe à un événement

            // Les paramètres sont dans le body
            $routes->post("add", "ParticipantController::add"); // Ajoute un participant à un événement
        });

        $routes->group("code", static function ($routes) {
            $routes->get("", "CodeController::getAll"); // Tous les codes
            $routes->get("(:alphanum)", "CodeController::checkExists/$1"); // Un code
            
            // Les paramètres sont dans le body
            $routes->post("generate", "CodeController::generate"); // Génère un code
        });

        $routes->group("friend", static function ($routes) {
            $routes->get("(:num)", "FriendController::getAll/$1"); // Tous les amis d'un utilisateur
            $routes->get("isFriend/(:num)/(:num)", "FriendController::isFriend/$1/$2"); // Vérifie si deux utilisateurs sont amis

            // Les paramètres sont dans le body
            $routes->post("add", "FriendController::add"); // Ajoute un ami
            $routes->delete("remove", "FriendController::remove"); // Supprime un ami
        });

        $routes->group("blocked", static function ($routes) {
            $routes->get("(:num)", "BlockedController::getAll/$1"); // Tous les utilisateurs bloqués par un utilisateur
            $routes->get("isBlocked/(:num)/(:num)", "BlockedController::isBlocked/$1/$2"); // Vérifie si un utilisateur est bloqué par un autre

            // Les paramètres sont dans le body
            $routes->post("add", "BlockedController::add"); // Ajoute un utilisateur bloqué
            $routes->delete("remove", "BlockedController::remove"); // Supprime un utilisateur bloqué
        });

        $routes->group("registration", static function ($routes) {
            $routes->post("register", "RegistrationController::register"); // Enregistre un utilisateur
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
