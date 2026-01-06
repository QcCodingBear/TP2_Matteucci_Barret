<?php

namespace App\Http\Controllers;

use Illuminate\Foundation\Auth\Access\AuthorizesRequests;
use Illuminate\Foundation\Bus\DispatchesJobs;
use Illuminate\Foundation\Validation\ValidatesRequests;
use Illuminate\Routing\Controller as BaseController;

// Source pour le SecurityScheme: https://swagger.io/docs/specification/v3_0/authentication/
// (Et compliqué à trouver, bien que généré par l'intellicode initialement).
// De plus sans l'aide de Copilot pour utiliser le bouton Authorize dans Swagger UI, je n'y serais pas arrivé.
// (Il m'a indiqué comment saisir Bearer + mon token dans le header Authorization).
// Je n'ai trouvé cette information dans aucune doc!

/** * @OA\Info(title="TP2 Web API", version="0.1") */
/**
     * @OA\Info(
     *      version="1.0.0",
     *      title="TP2 Web API",
     *      description="Tp2 Web de la session 4",
     *      @OA\Contact(
     *          email="thierry.matteucci@gmail.com"
     *      ),
     *      @OA\License(
     *          name="Apache 2.0",
     *          url="http://www.apache.org/licenses/LICENSE-2.0.html"
     *      )
     * )
     *
     * @OA\Server(
     *      url=L5_SWAGGER_CONST_HOST,
     *      description="Films API Server"
     * )

     *
     * @OA\Tag(
     *     name="Projects",
     *     description="API Endpoints of Projects"
     * )
     *
     * @OA\SecurityScheme(
     *     type="http",
     *     securityScheme="bearerAuth",
     *     description="Use a bearer token to access these endpoints",
     *     name="Token",
     *     in="header",
     *     scheme="bearer",
     *    bearerFormat="Token"
     * )
     */

//HTTP Codes (rajouter ceux qui manque)
define('OK', 200);
define('CREATED', 201);
define('NO_CONTENT', 204);
define('UNAUTHORIZED', 401);
define('FORBIDDEN', 403);
define('NOT_FOUND', 404);
define('INVALID_DATA', 422);
define('TOO_MANY_REQUESTS', 429);
define('SERVER_ERROR', 500);

//Pagination
define('SEARCH_PAGINATION', 20);

//Roles
define('USER', 2);
define('ADMIN', 1);

class Controller extends BaseController
{
    use AuthorizesRequests, DispatchesJobs, ValidatesRequests;
}
