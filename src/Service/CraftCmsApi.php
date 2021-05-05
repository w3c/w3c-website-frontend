<?php

declare(strict_types=1);

namespace App\Service;

use Strata\Frontend\Repository\CraftCms\CraftCms;
use Strata\Frontend\Schema\Schema;
use Symfony\Component\DependencyInjection\ParameterBag\ParameterBagInterface;

class CraftCmsApi extends CraftCms
{
    /**
     * Constructor
     *
     * Auto-populates data provider from parameters stored in config/services.yaml
     *
     * @param ParameterBagInterface $parameters
     */
    public function __construct(ParameterBagInterface $parameters)
    {
        $baseUrl = $parameters->get('app.craftcms_api_url');

        // @todo
        $contentSchema = null;

        parent::__construct($baseUrl, $contentSchema);
    }

}