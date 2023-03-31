<?php

namespace App\Service;

use Symfony\Component\Routing\RouterInterface;

class FeedHelper
{
    private RouterInterface $router;

    public function __construct(RouterInterface $router)
    {
        $this->router = $router;
    }

    public function buildTaxonomyFeeds(array $page): array
    {
        $feeds = [];
        // only one category max, could be an empty array if no category is set
        if (array_key_exists('category', $page) && count($page['category']) > 0) {
            $feeds[] = [
                'title' => 'W3C - ' . $page['category']['title'],
                'href'  => $this->router->generate('app_feed_category', ['slug' => $page['category']['slug']])
            ];
        }

        if (array_key_exists('ecosystems', $page)) {
            foreach ($page['ecosystems'] as $ecosystem) {
                $feeds[] = [
                    'title' => 'W3C - ' . $ecosystem['title'] . ' Ecosystem',
                    'href'  => $this->router->generate('app_feed_ecosystem', ['slug' => $ecosystem['slug']])
                ];
            }
        }

        if (array_key_exists('groups', $page)) {
            foreach ($page['groups'] as $group) {
                [$type, $shortname] = explode('-', $group['slug'], 2);
                $feeds[] = [
                    'title' => 'W3C - ' . $group['title'],
                    'href'  => $this->router->generate('app_feed_group', ['type' => $type, 'shortname' => $shortname])
                ];
            }
        }

        return $feeds;
    }
}
