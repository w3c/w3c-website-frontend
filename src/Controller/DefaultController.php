<?php

declare(strict_types=1);

namespace App\Controller;

use App\Service\CraftCmsApi;
use App\Service\W3CApi;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Cache\Adapter\FilesystemTagAwareAdapter;
use Symfony\Component\HttpFoundation\Response;

class DefaultController extends AbstractController
{

    public function index(W3CApi $w3CApi, CraftCmsApi $craftApi): Response
    {
        // @todo Set cache (set in service)
        // Please note ping() does not use cache, all other requests will
        $cache = new FilesystemTagAwareAdapter('cache', 0, __DIR__ . '/../../var/cache/');
        $w3CApi->setCache($cache);

        $response = $w3CApi->getSpecifications();
        $specifications = $w3CApi->getProvider()->decode($response);

        return $this->render('index/index.html.twig', [
            'w3c_available'   => $w3CApi->ping(),
            'craft_available' => $craftApi->ping(),
            'specifications'  => $specifications,
            'specifications_cache_hit' => $response->isHit(),
        ]);
    }
}
