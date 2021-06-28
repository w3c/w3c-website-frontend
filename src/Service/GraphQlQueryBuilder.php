<?php

declare(strict_types=1);

namespace App\Service;

/**
 * GraphQL Query builder helper
 *
 * Class GraphQlQueryBuilder
 * @package App\Service
 */
class GraphQlQueryBuilder
{

    /**
     * Get the related localisations for a page via a slug string
     *
     * @param string $slug
     * @return string
     */
    public function getLocalisationQueryForSlug(string $slug): string
    {
        $query =
            '{
          entry(slug: "' . $slug . '") {
            id
            language
            url
            localized {
              title
              code: language
              url
            }
          }
        }';

        return $query;
    }

    /**
     * Get the landing page content query for a page via a slug string
     *
     * @param string $slug
     * @return string
     */
    public function getLandingPageContentQueryForSlug(string $slug): string
    {
        $query =
            '{
              entry(slug: "' . $slug . '") {
                id
                ... on pages_landingPage_Entry {
                  title
                  pageLead
                  landingFlexibleComponents(orderBy: "sortOrder") {
                    ... on landingFlexibleComponents_textComponent_BlockType {
                      typeHandle
                      contentField
                      sortOrder
                      enabled
                    }
                    ... on landingFlexibleComponents_blockquoteComponent_BlockType {
                      typeHandle
                      sortOrder
                      citation
                      quoteText
                      enabled
                    }
                    ... on landingFlexibleComponents_fiftyFiftyComponent_BlockType {
                      typeHandle
                      ctaCopy
                      ctaUrl
                      enabled
                    }
                  }
                }
              }
            }';

        return $query;
    }

}