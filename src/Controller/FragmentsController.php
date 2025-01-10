<?php

declare(strict_types=1);

namespace App\Controller;

use DateTimeImmutable;
use Strata\Data\Exception\QueryManagerException;
use Strata\Data\Query\QueryManager;
use Strata\Frontend\Site;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpKernel\Attribute\Cache;
use Symfony\Component\Routing\Attribute\Route;

#[Route(path: '/_fragments')]
class FragmentsController extends AbstractController
{
    /**
     * @throws QueryManagerException
     */
    #[Cache(expires: 'tomorrow', public: true)]
    #[Route(path: '/global-nav/')]
    public function globalNav(QueryManager $manager): Response
    {
        $navigation = $manager->getCollection('navigation');

        return $this->render(
            '@W3CWebsiteTemplates/components/styles/global_nav.html.twig',
            ['navigation' => $navigation]
        );
    }

    #[Cache(expires: 'tomorrow', public: true)]
    #[Route(path: '/lang-nav/')]
    public function langNav(Site $site): Response
    {
        return $this->render(
            '@W3CWebsiteTemplates/components/styles/lang_nav.html.twig',
            ['site' => $site]
        );
    }

    #[Cache(expires: 'tomorrow', public: true)]
    #[Route(path: '/footer/')]
    public function footer(): Response
    {
        return $this->render('@W3CWebsiteTemplates/components/styles/footer.html.twig');
    }

    #[Cache(expires: 'tomorrow', public: true)]
    #[Route(path: '/common-head/')]
    public function commonHead(): Response
    {
        return $this->render('@W3CWebsiteTemplates/_common-head.html.twig');
    }
}
