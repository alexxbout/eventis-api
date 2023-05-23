<?php

namespace App\Utils;

class HTTPCodes {

    /**
     * La requête a réussi.
     */
    const OK = 200;

    /**
     * La requête a été traitée avec succès et une nouvelle ressource a été créée.
     */
    const CREATED = 201;

    /**
     * La requête a été traitée avec succès, mais que le contenu retourné dépend du résultat d'une action sur le serveur.
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

    /**
     * Le client n'est pas autorisé à utiliser la méthode demandée.
     */
    //const NOT_ALLOWED = 405;

    /**
     * Le serveur a rencontré une situation qu'il ne sait pas traiter.
     */
    const INTERNAL_SERVER_ERROR = 500;
}
