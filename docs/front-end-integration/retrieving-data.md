# Retrieving data

## Accessing data via Strata

When accessing data via Strata we use _data providers_, these represent a connection to a HTTP-based API. Data providers 
support things like caching, logging, concurrent requests and can deal with GraphQL errors automatically. 

An example of a data provider is the `CraftCMS` service, which uses the GraphQL data provider to connect to the Craft's API.

When sending HTTP requests you have two options: you can either run manual queries directly in the data provider. For example, 
for a REST-based data provider you can send GET and POST requests. GraphQL-based data providers have a `query()` method 
which allows you to send GraphQL queries.

Example usage:

```php
$response = $w3c->get('healthcheck');
$data = $w3c->decode($response);
```

Alternatively, you can construct _queries_ which are an object-orientated way to construct queries. For REST-based data 
providers you can construct enture queries via the OO interface. For GraphQL queries simple queries can be built, or you 
can load more complex queries via files (this is what we do for the W3C site).

By using queries we can setup common queries as classes (see `src/Query/`) which make them easier to re-use. We can also 
setup additional methods to make it easier to work with data (see `isHealthy()` example below).

Example usage:

```php
use App\Query\W3C\Healthcheck;

$query = new Healthcheck();

// Return whether W3C API is healthy (true if OK, false if not OK)
$healthy = $query->isHealthy();
```

We use a _Query manager_ to help run multiple queries. We add data providers to the query manager. Queries are then added 
to the query manager, with the correct data provider automatically applied. We can then retrieve data from the query manager.

Example usage:

```php
use App\Query\CraftCMS\Page;

// Return global navigation
$navigation = $manager->getCollection('navigation');

// Return current page content based on site ID and URI
$manager->add('page', new Page($siteId, $uri));
$page = $manager->get('page');
```

### Useful QueryManager methods

#### add()
Add a query to the Query Manager. You must pass the query name as the 1st argument and the query object as the 2nd argument.

#### get()
Run the query and return the data. This automatically decodes the data and can retrieve data from a specified root element 
(e.g. a `Page` query returns data from the root element `entry`). You must pass the query name as the 1st argument.

#### getCollection()
Run the query and return the data as a collection. This is an iterable collection and contains pagination information.
You must pass the query name as the 1st argument.

You can access pagination via: `getPagination()`

#### getResponse()
Return the HTTP response object for a query. You must pass the query name as the 1st argument.

#### getQuery()
Return the query object. You must pass the query name as the 1st argument.

For more information see Strata Data docs:
* [Query Manager](https://docs.strata.dev/data/v/release%2F0.8.0/retrieving-data/query-manager)
* [Queries](https://docs.strata.dev/data/v/release%2F0.8.0/retrieving-data/query)

## Services

The following services are available in the Symfony app. Simply type-hint your controller to use them.

See [config/services.yaml](../../config/services.yaml) for configuration to setup these services.

### QueryManager

```php
use Strata\Data\Query\QueryManager;

// Type hint to use service
QueryManager $manager
```

* Query manager to help setup and send queries to data providers
* Configured by [App\Service\QueryManagerConfigurator](../../src/Service/QueryManagerConfigurator.php), this does the following:
  * Add the CraftCMS and W3C data providers
  * Setup caching
  * Setup global query for navigation 
    
You can retrieve global navigation via:

```php
$navigation = $manager->getCollection('navigation');
```

### CraftCMS

```php
use App\Service\CraftCMS;

// Type hint to use service
CraftCMS $craftCmsApi
```

* Data provider for CraftCMS GraphQL API
* Connects with the reading schema authentication token (`CRAFTCMS_API_READ_TOKEN` in your `.env` file)

### W3C

```php
use App\Service\W3C;

// Type hint to use service
W3C $w3cApi
```

* Data provider for W3C Rest API
* Connects with the W3C API key (`W3C_API_KEY` in your `.env` file)

## Retrieving CraftCMS content via GraphQL API

For content retrieved from CraftCMS we need to do this using GraphQL queries. We also need to use an authentication token
to use the _Reading schema_.

We need a few different types of GraphQL queries to do the following:
* Retrieve all content for an individual page
* Retrieve all global content (currently global navigation)
* Retrieve lists of content for listing pages (e.g. news)

Find out more about writing [GraphQL queries for CraftCMS](craftcms-graphql.md).

## Writing GraphQL queries

Once we've confirmed our GraphQL query these are saved at `src/Query/CraftCMS/graphql` and have the file extension `.graphql`

These are loaded to a query via: `$query->setGraphQLFromFile()`

Any fragments (complex GraphQL query fragments that can be included in other queries) are stored in `src/Query/CraftCMS/graphql/fragments`.

These are loaded to a query via: `$query->addFragmentFromFile()`

## Writing custom Query objects

For W3C, create a class that extends `Strata\Data\Query\Query`, override the method `getRequiredDataProviderClass()` and 
return `App\Service\W3C::class`.

For CraftCMS, create a class that extends `Strata\Data\Query\GraphQLQuery`, override the method `getRequiredDataProviderClass()` and
return `App\Service\CraftCMS::class`.

You can then setup your query in the `__construct()` method. It's recommended to setup any parameters (Rest queries) or 
variables (GraphQL queries) as constructor arguments. Set defaults where these are optional. 

You can see an example below.

```php
use App\Service\CraftCMS;
use Strata\Data\Cache\CacheLifetime;
use Strata\Data\Query\GraphQLQuery;

class Page extends GraphQLQuery {

    public function getRequiredDataProviderClass(): string
    {
        return CraftCMS::class;
    }
    
    /**
     * Set up query
     *
     * @param int $siteId Site ID of page content
     * @param string $uri Page URI to return
     * @param int $cacheLifetime Cache lifetime to store HTTP response for, defaults to 30 minutes
     */
    public function __construct(int $siteId, string $uri, int $cacheLifetime = CacheLifetime::MINUTE * 30)
    {
        $this->setGraphQLFromFile(__DIR__ . '/graphql/page.graphql')
            ->addFragmentFromFile(__DIR__ . '/graphql/fragments/defaultFlexibleComponents.graphql')
            ->setRootPropertyPath('[entry]')

            // Set page URI to retrieve navigation for
            ->addVariable('uri', $uri)

            // Set site ID to retrieve navigation for
            ->addVariable('siteId', $siteId)

            // Cache page response
            ->enableCache($cacheLifetime)
        ;
    }
}
```

For more information see:
* [Queries](https://docs.strata.dev/data/v/release%2F0.8.0/retrieving-data/query)
* [Custom query classes](https://docs.strata.dev/data/v/release%2F0.8.0/retrieving-data/custom-query-classes)

## Sending mutate requests to CraftCMS to change data

If you need to send [mutate queries](https://graphql.org/learn/queries/#mutations) to change data, for example saving a
blog comment, you need to use an authentication token for the the _Publishing schema_. To retrieve this in a controller us:

```graphql
$publishingToken = $this->getParameter('app.craftcms_api_publish_token');
```

To use in a service, you need to inject the config parameter as arguments of their constructors. See [Accessing Configuration Parameters](https://symfony.com/doc/current/configuration.html#configuration-accessing-parameters).

Please ensure only use the publishing schema when you need to save data. By default, the reading schema is always used on the frontend app.

## Running queries

Once you have a query object to use, you can add this to the Query Manager and retrieve data via the `get()` or 
`getCollection()` method. If the data is considered global you can also add it to all instances of the Query Manager 
in the [`QueryManagerConfigurator`](../../src/Service/QueryManagerConfigurator.php)

```php
// Example controller method
public function page(Request $request, QueryManager $manager): Response
{
  // Add query to query manager
  $siteId = 1; 
  $uri = ltrim($request->getRequestUri(), '/')
  $manager->add('page', new Page($siteId, $uri));
  
  // Pass retrieved data to view (query runs on access)
  return $this->render('debug/page.html.twig', [
      'navigation'        => $manager->getCollection('navigation'),
      'page'              => $manager->get('page'),
      'page_cached'       => $manager->isHit('page'),
  ]);
}
```

Please note GraphQL only returns fields that match in your query, so it's possible some fields will not be set at all in
your response data.

