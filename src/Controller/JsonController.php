<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpKernel\Exception\HttpException;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Translation\TranslatorBagInterface;
use Symfony\Contracts\Translation\TranslatorInterface;

class JsonController extends AbstractController
{
    /**
     * Returns a JSON response of all translated messages corresponding to the request language
     *
     * @Route("/translated-messages")
     */
    public function translatedMessages(Request $request, TranslatorInterface $translator): JsonResponse
    {
        if ($translator instanceof TranslatorBagInterface) {
            $locale = $request->getLocale();

            foreach (['messages', 'w3c_website_templates_bundle'] as $domain) {
                $catalogues[$domain] = $translator->getCatalogue($locale)->all($domain);
            }

            return $this->json($catalogues);
        }

        throw new HttpException(Response::HTTP_INTERNAL_SERVER_ERROR);
    }
}
