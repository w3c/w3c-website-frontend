<?php

namespace App\Controller;

use Sensio\Bundle\FrameworkExtraBundle\Configuration\Cache;
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
     * Returns a JSON response of JS-side messages corresponding to the request language
     *
     * @Route("/translated-messages")
     * @Cache(expires="+1 month", public=true)
     */
    public function translatedMessages(Request $request, TranslatorInterface $translator): JsonResponse
    {
        if ($translator instanceof TranslatorBagInterface) {
            return $this->json($translator->getCatalogue($request->getLocale())->all('js'));
        }

        throw new HttpException(Response::HTTP_INTERNAL_SERVER_ERROR);
    }
}
