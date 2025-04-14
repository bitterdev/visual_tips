<?php

namespace Bitter\VisualTips\Routing;

use Concrete\Core\Entity\File\Version;
use Concrete\Core\File\File;
use Concrete\Core\Http\Middleware\FractalNegotiatorMiddleware;
use Concrete\Core\Http\Middleware\OAuthErrorMiddleware;
use Concrete\Core\Routing\RouteListInterface;
use Concrete\Core\Routing\Router;
use Symfony\Component\HttpFoundation\JsonResponse;

class RouteList implements RouteListInterface
{
    public function loadRoutes(Router $router)
    {
        $router
            ->buildGroup()
            ->setNamespace('Concrete\Package\VisualTips\Controller\Dialog\Support')
            ->setPrefix('/ccm/system/dialogs/visual_tips')
            ->routes('dialogs/support.php', 'visual_tips');


        $router->buildGroup()
            ->setPrefix('/visual_tips/api/1.0')
            ->addMiddleware(OAuthErrorMiddleware::class)
            ->addMiddleware(FractalNegotiatorMiddleware::class)
            ->routes(function ($groupRouter) {
                $groupRouter->get('/get_file_info/{fID}', function($fID) {
                    $fileUrl = null;

                    $f = File::getByID($fID);

                    if ($f instanceof \Concrete\Core\Entity\File\File) {
                        $fv = $f->getApprovedVersion();

                        if ($fv instanceof Version) {
                            $fileUrl = $fv->getURL();
                        }
                    }

                    return new JsonResponse([
                        "url" => $fileUrl
                    ]);
                });
            });


    }
}