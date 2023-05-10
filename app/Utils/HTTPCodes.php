<?php
namespace App\Utils;

class HTTPCodes {

    /**
     * La requête a réussi.
     */
    const OK = 200;
    /**
     * Il n'y a pas de contenu à envoyer pour cette requête, mais les en-têtes peuvent être utiles.
     */
    const NO_CONTENT = 204;

    /**
     * Cette réponse indique que le serveur n'a pas pu comprendre la requête à cause d'une syntaxe invalide.
     */
    const BAD_REQUEST = 400;

    /**
     * Bien que le standard HTTP indique « non-autorisé », la sémantique de cette réponse correspond à « non-authentifié » : le client doit s'authentifier afin d'obtenir la réponse demandée.
     */
    const UNAUTHORIZED = 401;

    /**
     * Le client n'a pas les droits d'accès au contenu, donc le serveur refuse de donner la véritable réponse.
     */
    const FORBIDDEN = 403;

    /**
     * Le serveur n'a pas trouvé la ressource demandée. Ce code de réponse est principalement connu pour son apparition fréquente sur le web.
     */
    const NOT_FOUND = 404;
}
