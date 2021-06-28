<?php

declare(strict_types=1);

namespace App\Controller;

use App\Service\CraftCmsApi;
use App\Service\GraphQlDataFormatter;
use App\Service\GraphQlQueryBuilder;
use App\Service\W3CApi;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Cache\Adapter\FilesystemTagAwareAdapter;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class DefaultController extends AbstractController
{

    /**
     * @Route("/debug", requirements={"route"=".+"}, defaults={"route"=""}, priority=-1)
     * @todo route priority is temporarily set to -1 as it's extremely greedy because of the {route} parameter.
     */
    public function debug(string $route, W3CApi $w3CApi, CraftCmsApi $craftApi): Response
    {
        // @todo Set cache (set in service)
        // Please note ping() does not use cache, all other requests will
        $cache = new FilesystemTagAwareAdapter('cache', 0, __DIR__ . '/../../var/cache/');
        $w3CApi->setCache($cache);

        $response = $w3CApi->getSpecifications();
        $specifications = $w3CApi->getProvider()->decode($response);

        return $this->render('debug/index.html.twig', [
            'w3c_available'   => $w3CApi->ping(),
            'craft_available' => $craftApi->ping(),
            'specifications'  => $specifications,
            'specifications_cache_hit' => $response->isHit(),
            'route' => '/' . $route
        ]);
    }

    /**
     * @Route("/{route}", requirements={"route"=".+"}, defaults={"route"=""}, priority=-1)
     * @todo route priority is temporarily set to -1 as it's extremely greedy because of the {route} parameter.
     */
    public function index(string $route, W3CApi $w3CApi, CraftCmsApi $craftApi, GraphQlQueryBuilder $queryBuilder): Response
    {
        // @todo Set cache (set in service)
        // Please note ping() does not use cache, all other requests will
        $cache = new FilesystemTagAwareAdapter('cache', 0, __DIR__ . '/../../var/cache/');
        $w3CApi->setCache($cache);

        $pageContent = $craftApi->query($queryBuilder->getLandingPageContentQueryForSlug($route));
        $pageLocalisations = $craftApi->query($queryBuilder->getLocalisationQueryForSlug($route));

//        dd($pageContent->toArray());

        return $this->render('debug/page.html.twig', [
            'page_translations'   => GraphQlDataFormatter::formatLocalisationDataForView($pageLocalisations),
            'page_content_components'   => GraphQlDataFormatter::formatLandingPageContentDataForView($pageContent),
            'route' => '/' . $route
        ]);
    }
}
