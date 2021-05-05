<?php

declare(strict_types=1);

namespace App\Controller;

use App\Service\CraftCmsApi;
use App\Service\W3CApi;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;

class DefaultController extends AbstractController
{

    public function index(W3CApi $w3CApi, CraftCmsApi $craftApi): Response
    {
        //$w3c->setCache();
        //$w3c->setCacheTags(['tagName']);

        $response = $w3CApi->getSpecifications();
        $specifications = $w3CApi->getProvider()->decode($response);

        return $this->render('index/index.html.twig', [
            'w3c_available'   => $w3CApi->ping(),
            'craft_available' => $craftApi->ping(),
            'specifications'  => $specifications
        ]);
    }
}
