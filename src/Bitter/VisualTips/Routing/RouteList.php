<?php

namespace Bitter\VisualTips\Routing;

use Bitter\VisualTips\API\V1\Middleware\FractalNegotiatorMiddleware;
use Bitter\VisualTips\API\V1\Configurator;
use Concrete\Core\Routing\RouteListInterface;
use Concrete\Core\Routing\Router;

class RouteList implements RouteListInterface
{
    public function loadRoutes(Router $router)
    {
        $router
            ->buildGroup()
            ->setNamespace('Concrete\Package\VisualTips\Controller\Dialog\Support')
            ->setPrefix('/ccm/system/dialogs/visual_tips')
            ->routes('dialogs/support.php', 'visual_tips');
    }
}