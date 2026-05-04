<?php

// This file is auto-generated and is for apps only. Bundles SHOULD NOT rely on its content.

namespace Symfony\Component\DependencyInjection\Loader\Configurator;

use Symfony\Component\Config\Loader\ParamConfigurator as Param;

/**
 * This class provides array-shapes for configuring the services and bundles of an application.
 *
 * Services declared with the config() method below are autowired and autoconfigured by default.
 *
 * This is for apps only. Bundles SHOULD NOT use it.
 *
 * Example:
 *
 *     ```php
 *     // config/services.php
 *     namespace Symfony\Component\DependencyInjection\Loader\Configurator;
 *
 *     return App::config([
 *         'services' => [
 *             'App\\' => [
 *                 'resource' => '../src/',
 *             ],
 *         ],
 *     ]);
 *     ```
 *
 * @psalm-type ImportsConfig = list<string|array{
 *     resource: string,
 *     type?: string|null,
 *     ignore_errors?: bool,
 * }>
 * @psalm-type ParametersConfig = array<string, scalar|\UnitEnum|array<scalar|\UnitEnum|array<mixed>|Param|null>|Param|null>
 * @psalm-type ArgumentsType = list<mixed>|array<string, mixed>
 * @psalm-type CallType = array<string, ArgumentsType>|array{0:string, 1?:ArgumentsType, 2?:bool}|array{method:string, arguments?:ArgumentsType, returns_clone?:bool}
 * @psalm-type TagsType = list<string|array<string, array<string, mixed>>> // arrays inside the list must have only one element, with the tag name as the key
 * @psalm-type CallbackType = string|array{0:string|ReferenceConfigurator,1:string}|\Closure|ReferenceConfigurator|ExpressionConfigurator
 * @psalm-type DeprecationType = array{package: string, version: string, message?: string}
 * @psalm-type DefaultsType = array{
 *     public?: bool,
 *     tags?: TagsType,
 *     resource_tags?: TagsType,
 *     autowire?: bool,
 *     autoconfigure?: bool,
 *     bind?: array<string, mixed>,
 * }
 * @psalm-type InstanceofType = array{
 *     shared?: bool,
 *     lazy?: bool|string,
 *     public?: bool,
 *     properties?: array<string, mixed>,
 *     configurator?: CallbackType,
 *     calls?: list<CallType>,
 *     tags?: TagsType,
 *     resource_tags?: TagsType,
 *     autowire?: bool,
 *     bind?: array<string, mixed>,
 *     constructor?: string,
 * }
 * @psalm-type DefinitionType = array{
 *     class?: string,
 *     file?: string,
 *     parent?: string,
 *     shared?: bool,
 *     synthetic?: bool,
 *     lazy?: bool|string,
 *     public?: bool,
 *     abstract?: bool,
 *     deprecated?: DeprecationType,
 *     factory?: CallbackType,
 *     configurator?: CallbackType,
 *     arguments?: ArgumentsType,
 *     properties?: array<string, mixed>,
 *     calls?: list<CallType>,
 *     tags?: TagsType,
 *     resource_tags?: TagsType,
 *     decorates?: string,
 *     decoration_inner_name?: string,
 *     decoration_priority?: int,
 *     decoration_on_invalid?: 'exception'|'ignore'|null,
 *     autowire?: bool,
 *     autoconfigure?: bool,
 *     bind?: array<string, mixed>,
 *     constructor?: string,
 *     from_callable?: CallbackType,
 * }
 * @psalm-type AliasType = string|array{
 *     alias: string,
 *     public?: bool,
 *     deprecated?: DeprecationType,
 * }
 * @psalm-type PrototypeType = array{
 *     resource: string,
 *     namespace?: string,
 *     exclude?: string|list<string>,
 *     parent?: string,
 *     shared?: bool,
 *     lazy?: bool|string,
 *     public?: bool,
 *     abstract?: bool,
 *     deprecated?: DeprecationType,
 *     factory?: CallbackType,
 *     arguments?: ArgumentsType,
 *     properties?: array<string, mixed>,
 *     configurator?: CallbackType,
 *     calls?: list<CallType>,
 *     tags?: TagsType,
 *     resource_tags?: TagsType,
 *     autowire?: bool,
 *     autoconfigure?: bool,
 *     bind?: array<string, mixed>,
 *     constructor?: string,
 * }
 * @psalm-type StackType = array{
 *     stack: list<DefinitionType|AliasType|PrototypeType|array<class-string, ArgumentsType|null>>,
 *     public?: bool,
 *     deprecated?: DeprecationType,
 * }
 * @psalm-type ServicesConfig = array{
 *     _defaults?: DefaultsType,
 *     _instanceof?: InstanceofType,
 *     ...<string, DefinitionType|AliasType|PrototypeType|StackType|ArgumentsType|null>
 * }
 * @psalm-type ExtensionType = array<string, mixed>
 * @psalm-type FrameworkConfig = array{
 *     secret?: scalar|Param|null,
 *     http_method_override?: bool|Param, // Set true to enable support for the '_method' request parameter to determine the intended HTTP method on POST requests. // Default: false
 *     allowed_http_method_override?: null|list<string|Param>,
 *     trust_x_sendfile_type_header?: scalar|Param|null, // Set true to enable support for xsendfile in binary file responses. // Default: "%env(bool:default::SYMFONY_TRUST_X_SENDFILE_TYPE_HEADER)%"
 *     ide?: scalar|Param|null, // Default: "%env(default::SYMFONY_IDE)%"
 *     test?: bool|Param,
 *     default_locale?: scalar|Param|null, // Default: "en"
 *     set_locale_from_accept_language?: bool|Param, // Whether to use the Accept-Language HTTP header to set the Request locale (only when the "_locale" request attribute is not passed). // Default: false
 *     set_content_language_from_locale?: bool|Param, // Whether to set the Content-Language HTTP header on the Response using the Request locale. // Default: false
 *     enabled_locales?: list<scalar|Param|null>,
 *     trusted_hosts?: string|list<scalar|Param|null>,
 *     trusted_proxies?: mixed, // Default: ["%env(default::SYMFONY_TRUSTED_PROXIES)%"]
 *     trusted_headers?: string|list<scalar|Param|null>,
 *     error_controller?: scalar|Param|null, // Default: "error_controller"
 *     handle_all_throwables?: bool|Param, // HttpKernel will handle all kinds of \Throwable. // Default: true
 *     csrf_protection?: bool|array{
 *         enabled?: scalar|Param|null, // Default: null
 *         stateless_token_ids?: list<scalar|Param|null>,
 *         check_header?: scalar|Param|null, // Whether to check the CSRF token in a header in addition to a cookie when using stateless protection. // Default: false
 *         cookie_name?: scalar|Param|null, // The name of the cookie to use when using stateless protection. // Default: "csrf-token"
 *     },
 *     form?: bool|array{ // Form configuration
 *         enabled?: bool|Param, // Default: true
 *         csrf_protection?: bool|array{
 *             enabled?: scalar|Param|null, // Default: null
 *             token_id?: scalar|Param|null, // Default: null
 *             field_name?: scalar|Param|null, // Default: "_token"
 *             field_attr?: array<string, scalar|Param|null>,
 *         },
 *     },
 *     http_cache?: bool|array{ // HTTP cache configuration
 *         enabled?: bool|Param, // Default: false
 *         debug?: bool|Param, // Default: "%kernel.debug%"
 *         trace_level?: "none"|"short"|"full"|Param,
 *         trace_header?: scalar|Param|null,
 *         default_ttl?: int|Param,
 *         private_headers?: list<scalar|Param|null>,
 *         skip_response_headers?: list<scalar|Param|null>,
 *         allow_reload?: bool|Param,
 *         allow_revalidate?: bool|Param,
 *         stale_while_revalidate?: int|Param,
 *         stale_if_error?: int|Param,
 *         terminate_on_cache_hit?: bool|Param,
 *     },
 *     esi?: bool|array{ // ESI configuration
 *         enabled?: bool|Param, // Default: false
 *     },
 *     ssi?: bool|array{ // SSI configuration
 *         enabled?: bool|Param, // Default: false
 *     },
 *     fragments?: bool|array{ // Fragments configuration
 *         enabled?: bool|Param, // Default: false
 *         hinclude_default_template?: scalar|Param|null, // Default: null
 *         path?: scalar|Param|null, // Default: "/_fragment"
 *     },
 *     profiler?: bool|array{ // Profiler configuration
 *         enabled?: bool|Param, // Default: false
 *         collect?: bool|Param, // Default: true
 *         collect_parameter?: scalar|Param|null, // The name of the parameter to use to enable or disable collection on a per request basis. // Default: null
 *         only_exceptions?: bool|Param, // Default: false
 *         only_main_requests?: bool|Param, // Default: false
 *         dsn?: scalar|Param|null, // Default: "file:%kernel.cache_dir%/profiler"
 *         collect_serializer_data?: bool|Param, // Enables the serializer data collector and profiler panel. // Default: false
 *     },
 *     workflows?: bool|array{
 *         enabled?: bool|Param, // Default: false
 *         workflows?: array<string, array{ // Default: []
 *                 audit_trail?: bool|array{
 *                     enabled?: bool|Param, // Default: false
 *                 },
 *                 type?: "workflow"|"state_machine"|Param, // Default: "state_machine"
 *                 marking_store?: array{
 *                     type?: "method"|Param,
 *                     property?: scalar|Param|null,
 *                     service?: scalar|Param|null,
 *                 },
 *                 supports?: string|list<scalar|Param|null>,
 *                 definition_validators?: list<scalar|Param|null>,
 *                 support_strategy?: scalar|Param|null,
 *                 initial_marking?: backed-enum|string|list<scalar|Param|null>,
 *                 events_to_dispatch?: null|list<string|Param>,
 *                 places?: string|list<array{ // Default: []
 *                         name?: scalar|Param|null,
 *                         metadata?: array<string, mixed>,
 *                     }>,
 *                 transitions?: list<array{ // Default: []
 *                         name?: string|Param,
 *                         guard?: string|Param, // An expression to block the transition.
 *                         from?: backed-enum|string|list<array{ // Default: []
 *                                 place?: string|Param,
 *                                 weight?: int|Param, // Default: 1
 *                             }>,
 *                         to?: backed-enum|string|list<array{ // Default: []
 *                                 place?: string|Param,
 *                                 weight?: int|Param, // Default: 1
 *                             }>,
 *                         weight?: int|Param, // Default: 1
 *                         metadata?: array<string, mixed>,
 *                     }>,
 *                 metadata?: array<string, mixed>,
 *             }>,
 *     },
 *     router?: bool|array{ // Router configuration
 *         enabled?: bool|Param, // Default: false
 *         resource?: scalar|Param|null,
 *         type?: scalar|Param|null,
 *         cache_dir?: scalar|Param|null, // Deprecated: Setting the "framework.router.cache_dir.cache_dir" configuration option is deprecated. It will be removed in version 8.0. // Default: "%kernel.build_dir%"
 *         default_uri?: scalar|Param|null, // The default URI used to generate URLs in a non-HTTP context. // Default: null
 *         http_port?: scalar|Param|null, // Default: 80
 *         https_port?: scalar|Param|null, // Default: 443
 *         strict_requirements?: scalar|Param|null, // set to true to throw an exception when a parameter does not match the requirements set to false to disable exceptions when a parameter does not match the requirements (and return null instead) set to null to disable parameter checks against requirements 'true' is the preferred configuration in development mode, while 'false' or 'null' might be preferred in production // Default: true
 *         utf8?: bool|Param, // Default: true
 *     },
 *     session?: bool|array{ // Session configuration
 *         enabled?: bool|Param, // Default: false
 *         storage_factory_id?: scalar|Param|null, // Default: "session.storage.factory.native"
 *         handler_id?: scalar|Param|null, // Defaults to using the native session handler, or to the native *file* session handler if "save_path" is not null.
 *         name?: scalar|Param|null,
 *         cookie_lifetime?: scalar|Param|null,
 *         cookie_path?: scalar|Param|null,
 *         cookie_domain?: scalar|Param|null,
 *         cookie_secure?: true|false|"auto"|Param, // Default: "auto"
 *         cookie_httponly?: bool|Param, // Default: true
 *         cookie_samesite?: null|"lax"|"strict"|"none"|Param, // Default: "lax"
 *         use_cookies?: bool|Param,
 *         gc_divisor?: scalar|Param|null,
 *         gc_probability?: scalar|Param|null,
 *         gc_maxlifetime?: scalar|Param|null,
 *         save_path?: scalar|Param|null, // Defaults to "%kernel.cache_dir%/sessions" if the "handler_id" option is not null.
 *         metadata_update_threshold?: int|Param, // Seconds to wait between 2 session metadata updates. // Default: 0
 *         sid_length?: int|Param, // Deprecated: Setting the "framework.session.sid_length.sid_length" configuration option is deprecated. It will be removed in version 8.0. No alternative is provided as PHP 8.4 has deprecated the related option.
 *         sid_bits_per_character?: int|Param, // Deprecated: Setting the "framework.session.sid_bits_per_character.sid_bits_per_character" configuration option is deprecated. It will be removed in version 8.0. No alternative is provided as PHP 8.4 has deprecated the related option.
 *     },
 *     request?: bool|array{ // Request configuration
 *         enabled?: bool|Param, // Default: false
 *         formats?: array<string, string|list<scalar|Param|null>>,
 *     },
 *     assets?: bool|array{ // Assets configuration
 *         enabled?: bool|Param, // Default: true
 *         strict_mode?: bool|Param, // Throw an exception if an entry is missing from the manifest.json. // Default: false
 *         version_strategy?: scalar|Param|null, // Default: null
 *         version?: scalar|Param|null, // Default: null
 *         version_format?: scalar|Param|null, // Default: "%%s?%%s"
 *         json_manifest_path?: scalar|Param|null, // Default: null
 *         base_path?: scalar|Param|null, // Default: ""
 *         base_urls?: string|list<scalar|Param|null>,
 *         packages?: array<string, array{ // Default: []
 *                 strict_mode?: bool|Param, // Throw an exception if an entry is missing from the manifest.json. // Default: false
 *                 version_strategy?: scalar|Param|null, // Default: null
 *                 version?: scalar|Param|null,
 *                 version_format?: scalar|Param|null, // Default: null
 *                 json_manifest_path?: scalar|Param|null, // Default: null
 *                 base_path?: scalar|Param|null, // Default: ""
 *                 base_urls?: string|list<scalar|Param|null>,
 *             }>,
 *     },
 *     asset_mapper?: bool|array{ // Asset Mapper configuration
 *         enabled?: bool|Param, // Default: false
 *         paths?: string|array<string, scalar|Param|null>,
 *         excluded_patterns?: list<scalar|Param|null>,
 *         exclude_dotfiles?: bool|Param, // If true, any files starting with "." will be excluded from the asset mapper. // Default: true
 *         server?: bool|Param, // If true, a "dev server" will return the assets from the public directory (true in "debug" mode only by default). // Default: true
 *         public_prefix?: scalar|Param|null, // The public path where the assets will be written to (and served from when "server" is true). // Default: "/assets/"
 *         missing_import_mode?: "strict"|"warn"|"ignore"|Param, // Behavior if an asset cannot be found when imported from JavaScript or CSS files - e.g. "import './non-existent.js'". "strict" means an exception is thrown, "warn" means a warning is logged, "ignore" means the import is left as-is. // Default: "warn"
 *         extensions?: array<string, scalar|Param|null>,
 *         importmap_path?: scalar|Param|null, // The path of the importmap.php file. // Default: "%kernel.project_dir%/importmap.php"
 *         importmap_polyfill?: scalar|Param|null, // The importmap name that will be used to load the polyfill. Set to false to disable. // Default: "es-module-shims"
 *         importmap_script_attributes?: array<string, scalar|Param|null>,
 *         vendor_dir?: scalar|Param|null, // The directory to store JavaScript vendors. // Default: "%kernel.project_dir%/assets/vendor"
 *         precompress?: bool|array{ // Precompress assets with Brotli, Zstandard and gzip.
 *             enabled?: bool|Param, // Default: false
 *             formats?: list<scalar|Param|null>,
 *             extensions?: list<scalar|Param|null>,
 *         },
 *     },
 *     translator?: bool|array{ // Translator configuration
 *         enabled?: bool|Param, // Default: true
 *         fallbacks?: string|list<scalar|Param|null>,
 *         logging?: bool|Param, // Default: false
 *         formatter?: scalar|Param|null, // Default: "translator.formatter.default"
 *         cache_dir?: scalar|Param|null, // Default: "%kernel.cache_dir%/translations"
 *         default_path?: scalar|Param|null, // The default path used to load translations. // Default: "%kernel.project_dir%/translations"
 *         paths?: list<scalar|Param|null>,
 *         pseudo_localization?: bool|array{
 *             enabled?: bool|Param, // Default: false
 *             accents?: bool|Param, // Default: true
 *             expansion_factor?: float|Param, // Default: 1.0
 *             brackets?: bool|Param, // Default: true
 *             parse_html?: bool|Param, // Default: false
 *             localizable_html_attributes?: list<scalar|Param|null>,
 *         },
 *         providers?: array<string, array{ // Default: []
 *                 dsn?: scalar|Param|null,
 *                 domains?: list<scalar|Param|null>,
 *                 locales?: list<scalar|Param|null>,
 *             }>,
 *         globals?: array<string, string|array{ // Default: []
 *                 value?: mixed,
 *                 message?: string|Param,
 *                 parameters?: array<string, scalar|Param|null>,
 *                 domain?: string|Param,
 *             }>,
 *     },
 *     validation?: bool|array{ // Validation configuration
 *         enabled?: bool|Param, // Default: true
 *         cache?: scalar|Param|null, // Deprecated: Setting the "framework.validation.cache.cache" configuration option is deprecated. It will be removed in version 8.0.
 *         enable_attributes?: bool|Param, // Default: true
 *         static_method?: string|list<scalar|Param|null>,
 *         translation_domain?: scalar|Param|null, // Default: "validators"
 *         email_validation_mode?: "html5"|"html5-allow-no-tld"|"strict"|"loose"|Param, // Default: "html5"
 *         mapping?: array{
 *             paths?: list<scalar|Param|null>,
 *         },
 *         not_compromised_password?: bool|array{
 *             enabled?: bool|Param, // When disabled, compromised passwords will be accepted as valid. // Default: true
 *             endpoint?: scalar|Param|null, // API endpoint for the NotCompromisedPassword Validator. // Default: null
 *         },
 *         disable_translation?: bool|Param, // Default: false
 *         auto_mapping?: array<string, array{ // Default: []
 *                 services?: list<scalar|Param|null>,
 *             }>,
 *     },
 *     annotations?: bool|array{
 *         enabled?: bool|Param, // Default: false
 *     },
 *     serializer?: bool|array{ // Serializer configuration
 *         enabled?: bool|Param, // Default: true
 *         enable_attributes?: bool|Param, // Default: true
 *         name_converter?: scalar|Param|null,
 *         circular_reference_handler?: scalar|Param|null,
 *         max_depth_handler?: scalar|Param|null,
 *         mapping?: array{
 *             paths?: list<scalar|Param|null>,
 *         },
 *         default_context?: array<string, mixed>,
 *         named_serializers?: array<string, array{ // Default: []
 *                 name_converter?: scalar|Param|null,
 *                 default_context?: array<string, mixed>,
 *                 include_built_in_normalizers?: bool|Param, // Whether to include the built-in normalizers // Default: true
 *                 include_built_in_encoders?: bool|Param, // Whether to include the built-in encoders // Default: true
 *             }>,
 *     },
 *     property_access?: bool|array{ // Property access configuration
 *         enabled?: bool|Param, // Default: true
 *         magic_call?: bool|Param, // Default: false
 *         magic_get?: bool|Param, // Default: true
 *         magic_set?: bool|Param, // Default: true
 *         throw_exception_on_invalid_index?: bool|Param, // Default: false
 *         throw_exception_on_invalid_property_path?: bool|Param, // Default: true
 *     },
 *     type_info?: bool|array{ // Type info configuration
 *         enabled?: bool|Param, // Default: true
 *         aliases?: array<string, scalar|Param|null>,
 *     },
 *     property_info?: bool|array{ // Property info configuration
 *         enabled?: bool|Param, // Default: true
 *         with_constructor_extractor?: bool|Param, // Registers the constructor extractor.
 *     },
 *     cache?: array{ // Cache configuration
 *         prefix_seed?: scalar|Param|null, // Used to namespace cache keys when using several apps with the same shared backend. // Default: "_%kernel.project_dir%.%kernel.container_class%"
 *         app?: scalar|Param|null, // App related cache pools configuration. // Default: "cache.adapter.filesystem"
 *         system?: scalar|Param|null, // System related cache pools configuration. // Default: "cache.adapter.system"
 *         directory?: scalar|Param|null, // Default: "%kernel.share_dir%/pools/app"
 *         default_psr6_provider?: scalar|Param|null,
 *         default_redis_provider?: scalar|Param|null, // Default: "redis://localhost"
 *         default_valkey_provider?: scalar|Param|null, // Default: "valkey://localhost"
 *         default_memcached_provider?: scalar|Param|null, // Default: "memcached://localhost"
 *         default_doctrine_dbal_provider?: scalar|Param|null, // Default: "database_connection"
 *         default_pdo_provider?: scalar|Param|null, // Default: null
 *         pools?: array<string, array{ // Default: []
 *                 adapters?: string|list<scalar|Param|null>,
 *                 tags?: scalar|Param|null, // Default: null
 *                 public?: bool|Param, // Default: false
 *                 default_lifetime?: scalar|Param|null, // Default lifetime of the pool.
 *                 provider?: scalar|Param|null, // Overwrite the setting from the default provider for this adapter.
 *                 early_expiration_message_bus?: scalar|Param|null,
 *                 clearer?: scalar|Param|null,
 *             }>,
 *     },
 *     php_errors?: array{ // PHP errors handling configuration
 *         log?: mixed, // Use the application logger instead of the PHP logger for logging PHP errors. // Default: true
 *         throw?: bool|Param, // Throw PHP errors as \ErrorException instances. // Default: true
 *     },
 *     exceptions?: array<string, array{ // Default: []
 *             log_level?: scalar|Param|null, // The level of log message. Null to let Symfony decide. // Default: null
 *             status_code?: scalar|Param|null, // The status code of the response. Null or 0 to let Symfony decide. // Default: null
 *             log_channel?: scalar|Param|null, // The channel of log message. Null to let Symfony decide. // Default: null
 *         }>,
 *     web_link?: bool|array{ // Web links configuration
 *         enabled?: bool|Param, // Default: true
 *     },
 *     lock?: bool|string|array{ // Lock configuration
 *         enabled?: bool|Param, // Default: true
 *         resources?: string|array<string, string|list<scalar|Param|null>>,
 *     },
 *     semaphore?: bool|string|array{ // Semaphore configuration
 *         enabled?: bool|Param, // Default: false
 *         resources?: string|array<string, scalar|Param|null>,
 *     },
 *     messenger?: bool|array{ // Messenger configuration
 *         enabled?: bool|Param, // Default: false
 *         routing?: array<string, string|array{ // Default: []
 *                 senders?: list<scalar|Param|null>,
 *             }>,
 *         serializer?: array{
 *             default_serializer?: scalar|Param|null, // Service id to use as the default serializer for the transports. // Default: "messenger.transport.native_php_serializer"
 *             symfony_serializer?: array{
 *                 format?: scalar|Param|null, // Serialization format for the messenger.transport.symfony_serializer service (which is not the serializer used by default). // Default: "json"
 *                 context?: array<string, mixed>,
 *             },
 *         },
 *         transports?: array<string, string|array{ // Default: []
 *                 dsn?: scalar|Param|null,
 *                 serializer?: scalar|Param|null, // Service id of a custom serializer to use. // Default: null
 *                 options?: array<string, mixed>,
 *                 failure_transport?: scalar|Param|null, // Transport name to send failed messages to (after all retries have failed). // Default: null
 *                 retry_strategy?: string|array{
 *                     service?: scalar|Param|null, // Service id to override the retry strategy entirely. // Default: null
 *                     max_retries?: int|Param, // Default: 3
 *                     delay?: int|Param, // Time in ms to delay (or the initial value when multiplier is used). // Default: 1000
 *                     multiplier?: float|Param, // If greater than 1, delay will grow exponentially for each retry: this delay = (delay * (multiple ^ retries)). // Default: 2
 *                     max_delay?: int|Param, // Max time in ms that a retry should ever be delayed (0 = infinite). // Default: 0
 *                     jitter?: float|Param, // Randomness to apply to the delay (between 0 and 1). // Default: 0.1
 *                 },
 *                 rate_limiter?: scalar|Param|null, // Rate limiter name to use when processing messages. // Default: null
 *             }>,
 *         failure_transport?: scalar|Param|null, // Transport name to send failed messages to (after all retries have failed). // Default: null
 *         stop_worker_on_signals?: int|string|list<scalar|Param|null>,
 *         default_bus?: scalar|Param|null, // Default: null
 *         buses?: array<string, array{ // Default: {"messenger.bus.default":{"default_middleware":{"enabled":true,"allow_no_handlers":false,"allow_no_senders":true},"middleware":[]}}
 *                 default_middleware?: bool|string|array{
 *                     enabled?: bool|Param, // Default: true
 *                     allow_no_handlers?: bool|Param, // Default: false
 *                     allow_no_senders?: bool|Param, // Default: true
 *                 },
 *                 middleware?: string|list<string|array{ // Default: []
 *                         id?: scalar|Param|null,
 *                         arguments?: list<mixed>,
 *                     }>,
 *             }>,
 *     },
 *     scheduler?: bool|array{ // Scheduler configuration
 *         enabled?: bool|Param, // Default: false
 *     },
 *     disallow_search_engine_index?: bool|Param, // Enabled by default when debug is enabled. // Default: true
 *     http_client?: bool|array{ // HTTP Client configuration
 *         enabled?: bool|Param, // Default: true
 *         max_host_connections?: int|Param, // The maximum number of connections to a single host.
 *         default_options?: array{
 *             headers?: array<string, mixed>,
 *             vars?: array<string, mixed>,
 *             max_redirects?: int|Param, // The maximum number of redirects to follow.
 *             http_version?: scalar|Param|null, // The default HTTP version, typically 1.1 or 2.0, leave to null for the best version.
 *             resolve?: array<string, scalar|Param|null>,
 *             proxy?: scalar|Param|null, // The URL of the proxy to pass requests through or null for automatic detection.
 *             no_proxy?: scalar|Param|null, // A comma separated list of hosts that do not require a proxy to be reached.
 *             timeout?: float|Param, // The idle timeout, defaults to the "default_socket_timeout" ini parameter.
 *             max_duration?: float|Param, // The maximum execution time for the request+response as a whole.
 *             bindto?: scalar|Param|null, // A network interface name, IP address, a host name or a UNIX socket to bind to.
 *             verify_peer?: bool|Param, // Indicates if the peer should be verified in a TLS context.
 *             verify_host?: bool|Param, // Indicates if the host should exist as a certificate common name.
 *             cafile?: scalar|Param|null, // A certificate authority file.
 *             capath?: scalar|Param|null, // A directory that contains multiple certificate authority files.
 *             local_cert?: scalar|Param|null, // A PEM formatted certificate file.
 *             local_pk?: scalar|Param|null, // A private key file.
 *             passphrase?: scalar|Param|null, // The passphrase used to encrypt the "local_pk" file.
 *             ciphers?: scalar|Param|null, // A list of TLS ciphers separated by colons, commas or spaces (e.g. "RC3-SHA:TLS13-AES-128-GCM-SHA256"...)
 *             peer_fingerprint?: array{ // Associative array: hashing algorithm => hash(es).
 *                 sha1?: mixed,
 *                 pin-sha256?: mixed,
 *                 md5?: mixed,
 *             },
 *             crypto_method?: scalar|Param|null, // The minimum version of TLS to accept; must be one of STREAM_CRYPTO_METHOD_TLSv*_CLIENT constants.
 *             extra?: array<string, mixed>,
 *             rate_limiter?: scalar|Param|null, // Rate limiter name to use for throttling requests. // Default: null
 *             caching?: bool|array{ // Caching configuration.
 *                 enabled?: bool|Param, // Default: false
 *                 cache_pool?: string|Param, // The taggable cache pool to use for storing the responses. // Default: "cache.http_client"
 *                 shared?: bool|Param, // Indicates whether the cache is shared (public) or private. // Default: true
 *                 max_ttl?: int|Param, // The maximum TTL (in seconds) allowed for cached responses. Null means no cap. // Default: null
 *             },
 *             retry_failed?: bool|array{
 *                 enabled?: bool|Param, // Default: false
 *                 retry_strategy?: scalar|Param|null, // service id to override the retry strategy. // Default: null
 *                 http_codes?: int|string|array<string, array{ // Default: []
 *                         code?: int|Param,
 *                         methods?: string|list<string|Param>,
 *                     }>,
 *                 max_retries?: int|Param, // Default: 3
 *                 delay?: int|Param, // Time in ms to delay (or the initial value when multiplier is used). // Default: 1000
 *                 multiplier?: float|Param, // If greater than 1, delay will grow exponentially for each retry: delay * (multiple ^ retries). // Default: 2
 *                 max_delay?: int|Param, // Max time in ms that a retry should ever be delayed (0 = infinite). // Default: 0
 *                 jitter?: float|Param, // Randomness in percent (between 0 and 1) to apply to the delay. // Default: 0.1
 *             },
 *         },
 *         mock_response_factory?: scalar|Param|null, // The id of the service that should generate mock responses. It should be either an invokable or an iterable.
 *         scoped_clients?: array<string, string|array{ // Default: []
 *                 scope?: scalar|Param|null, // The regular expression that the request URL must match before adding the other options. When none is provided, the base URI is used instead.
 *                 base_uri?: scalar|Param|null, // The URI to resolve relative URLs, following rules in RFC 3985, section 2.
 *                 auth_basic?: scalar|Param|null, // An HTTP Basic authentication "username:password".
 *                 auth_bearer?: scalar|Param|null, // A token enabling HTTP Bearer authorization.
 *                 auth_ntlm?: scalar|Param|null, // A "username:password" pair to use Microsoft NTLM authentication (requires the cURL extension).
 *                 query?: array<string, scalar|Param|null>,
 *                 headers?: array<string, mixed>,
 *                 max_redirects?: int|Param, // The maximum number of redirects to follow.
 *                 http_version?: scalar|Param|null, // The default HTTP version, typically 1.1 or 2.0, leave to null for the best version.
 *                 resolve?: array<string, scalar|Param|null>,
 *                 proxy?: scalar|Param|null, // The URL of the proxy to pass requests through or null for automatic detection.
 *                 no_proxy?: scalar|Param|null, // A comma separated list of hosts that do not require a proxy to be reached.
 *                 timeout?: float|Param, // The idle timeout, defaults to the "default_socket_timeout" ini parameter.
 *                 max_duration?: float|Param, // The maximum execution time for the request+response as a whole.
 *                 bindto?: scalar|Param|null, // A network interface name, IP address, a host name or a UNIX socket to bind to.
 *                 verify_peer?: bool|Param, // Indicates if the peer should be verified in a TLS context.
 *                 verify_host?: bool|Param, // Indicates if the host should exist as a certificate common name.
 *                 cafile?: scalar|Param|null, // A certificate authority file.
 *                 capath?: scalar|Param|null, // A directory that contains multiple certificate authority files.
 *                 local_cert?: scalar|Param|null, // A PEM formatted certificate file.
 *                 local_pk?: scalar|Param|null, // A private key file.
 *                 passphrase?: scalar|Param|null, // The passphrase used to encrypt the "local_pk" file.
 *                 ciphers?: scalar|Param|null, // A list of TLS ciphers separated by colons, commas or spaces (e.g. "RC3-SHA:TLS13-AES-128-GCM-SHA256"...).
 *                 peer_fingerprint?: array{ // Associative array: hashing algorithm => hash(es).
 *                     sha1?: mixed,
 *                     pin-sha256?: mixed,
 *                     md5?: mixed,
 *                 },
 *                 crypto_method?: scalar|Param|null, // The minimum version of TLS to accept; must be one of STREAM_CRYPTO_METHOD_TLSv*_CLIENT constants.
 *                 extra?: array<string, mixed>,
 *                 rate_limiter?: scalar|Param|null, // Rate limiter name to use for throttling requests. // Default: null
 *                 caching?: bool|array{ // Caching configuration.
 *                     enabled?: bool|Param, // Default: false
 *                     cache_pool?: string|Param, // The taggable cache pool to use for storing the responses. // Default: "cache.http_client"
 *                     shared?: bool|Param, // Indicates whether the cache is shared (public) or private. // Default: true
 *                     max_ttl?: int|Param, // The maximum TTL (in seconds) allowed for cached responses. Null means no cap. // Default: null
 *                 },
 *                 retry_failed?: bool|array{
 *                     enabled?: bool|Param, // Default: false
 *                     retry_strategy?: scalar|Param|null, // service id to override the retry strategy. // Default: null
 *                     http_codes?: int|string|array<string, array{ // Default: []
 *                             code?: int|Param,
 *                             methods?: string|list<string|Param>,
 *                         }>,
 *                     max_retries?: int|Param, // Default: 3
 *                     delay?: int|Param, // Time in ms to delay (or the initial value when multiplier is used). // Default: 1000
 *                     multiplier?: float|Param, // If greater than 1, delay will grow exponentially for each retry: delay * (multiple ^ retries). // Default: 2
 *                     max_delay?: int|Param, // Max time in ms that a retry should ever be delayed (0 = infinite). // Default: 0
 *                     jitter?: float|Param, // Randomness in percent (between 0 and 1) to apply to the delay. // Default: 0.1
 *                 },
 *             }>,
 *     },
 *     mailer?: bool|array{ // Mailer configuration
 *         enabled?: bool|Param, // Default: true
 *         message_bus?: scalar|Param|null, // The message bus to use. Defaults to the default bus if the Messenger component is installed. // Default: null
 *         dsn?: scalar|Param|null, // Default: null
 *         transports?: array<string, scalar|Param|null>,
 *         envelope?: array{ // Mailer Envelope configuration
 *             sender?: scalar|Param|null,
 *             recipients?: string|list<scalar|Param|null>,
 *             allowed_recipients?: string|list<scalar|Param|null>,
 *         },
 *         headers?: array<string, string|array{ // Default: []
 *                 value?: mixed,
 *             }>,
 *         dkim_signer?: bool|array{ // DKIM signer configuration
 *             enabled?: bool|Param, // Default: false
 *             key?: scalar|Param|null, // Key content, or path to key (in PEM format with the `file://` prefix) // Default: ""
 *             domain?: scalar|Param|null, // Default: ""
 *             select?: scalar|Param|null, // Default: ""
 *             passphrase?: scalar|Param|null, // The private key passphrase // Default: ""
 *             options?: array<string, mixed>,
 *         },
 *         smime_signer?: bool|array{ // S/MIME signer configuration
 *             enabled?: bool|Param, // Default: false
 *             key?: scalar|Param|null, // Path to key (in PEM format) // Default: ""
 *             certificate?: scalar|Param|null, // Path to certificate (in PEM format without the `file://` prefix) // Default: ""
 *             passphrase?: scalar|Param|null, // The private key passphrase // Default: null
 *             extra_certificates?: scalar|Param|null, // Default: null
 *             sign_options?: int|Param, // Default: null
 *         },
 *         smime_encrypter?: bool|array{ // S/MIME encrypter configuration
 *             enabled?: bool|Param, // Default: false
 *             repository?: scalar|Param|null, // S/MIME certificate repository service. This service shall implement the `Symfony\Component\Mailer\EventListener\SmimeCertificateRepositoryInterface`. // Default: ""
 *             cipher?: int|Param, // A set of algorithms used to encrypt the message // Default: null
 *         },
 *     },
 *     secrets?: bool|array{
 *         enabled?: bool|Param, // Default: true
 *         vault_directory?: scalar|Param|null, // Default: "%kernel.project_dir%/config/secrets/%kernel.runtime_environment%"
 *         local_dotenv_file?: scalar|Param|null, // Default: "%kernel.project_dir%/.env.%kernel.environment%.local"
 *         decryption_env_var?: scalar|Param|null, // Default: "base64:default::SYMFONY_DECRYPTION_SECRET"
 *     },
 *     notifier?: bool|array{ // Notifier configuration
 *         enabled?: bool|Param, // Default: true
 *         message_bus?: scalar|Param|null, // The message bus to use. Defaults to the default bus if the Messenger component is installed. // Default: null
 *         chatter_transports?: array<string, scalar|Param|null>,
 *         texter_transports?: array<string, scalar|Param|null>,
 *         notification_on_failed_messages?: bool|Param, // Default: false
 *         channel_policy?: array<string, string|list<scalar|Param|null>>,
 *         admin_recipients?: list<array{ // Default: []
 *                 email?: scalar|Param|null,
 *                 phone?: scalar|Param|null, // Default: ""
 *             }>,
 *     },
 *     rate_limiter?: bool|array{ // Rate limiter configuration
 *         enabled?: bool|Param, // Default: false
 *         limiters?: array<string, array{ // Default: []
 *                 lock_factory?: scalar|Param|null, // The service ID of the lock factory used by this limiter (or null to disable locking). // Default: "auto"
 *                 cache_pool?: scalar|Param|null, // The cache pool to use for storing the current limiter state. // Default: "cache.rate_limiter"
 *                 storage_service?: scalar|Param|null, // The service ID of a custom storage implementation, this precedes any configured "cache_pool". // Default: null
 *                 policy?: "fixed_window"|"token_bucket"|"sliding_window"|"compound"|"no_limit"|Param, // The algorithm to be used by this limiter.
 *                 limiters?: string|list<scalar|Param|null>,
 *                 limit?: int|Param, // The maximum allowed hits in a fixed interval or burst.
 *                 interval?: scalar|Param|null, // Configures the fixed interval if "policy" is set to "fixed_window" or "sliding_window". The value must be a number followed by "second", "minute", "hour", "day", "week" or "month" (or their plural equivalent).
 *                 rate?: array{ // Configures the fill rate if "policy" is set to "token_bucket".
 *                     interval?: scalar|Param|null, // Configures the rate interval. The value must be a number followed by "second", "minute", "hour", "day", "week" or "month" (or their plural equivalent).
 *                     amount?: int|Param, // Amount of tokens to add each interval. // Default: 1
 *                 },
 *             }>,
 *     },
 *     uid?: bool|array{ // Uid configuration
 *         enabled?: bool|Param, // Default: false
 *         default_uuid_version?: 7|6|4|1|Param, // Default: 7
 *         name_based_uuid_version?: 5|3|Param, // Default: 5
 *         name_based_uuid_namespace?: scalar|Param|null,
 *         time_based_uuid_version?: 7|6|1|Param, // Default: 7
 *         time_based_uuid_node?: scalar|Param|null,
 *     },
 *     html_sanitizer?: bool|array{ // HtmlSanitizer configuration
 *         enabled?: bool|Param, // Default: false
 *         sanitizers?: array<string, array{ // Default: []
 *                 allow_safe_elements?: bool|Param, // Allows "safe" elements and attributes. // Default: false
 *                 allow_static_elements?: bool|Param, // Allows all static elements and attributes from the W3C Sanitizer API standard. // Default: false
 *                 allow_elements?: array<string, mixed>,
 *                 block_elements?: string|list<string|Param>,
 *                 drop_elements?: string|list<string|Param>,
 *                 allow_attributes?: array<string, mixed>,
 *                 drop_attributes?: array<string, mixed>,
 *                 force_attributes?: array<string, array<string, string|Param>>,
 *                 force_https_urls?: bool|Param, // Transforms URLs using the HTTP scheme to use the HTTPS scheme instead. // Default: false
 *                 allowed_link_schemes?: string|list<string|Param>,
 *                 allowed_link_hosts?: null|string|list<string|Param>,
 *                 allow_relative_links?: bool|Param, // Allows relative URLs to be used in links href attributes. // Default: false
 *                 allowed_media_schemes?: string|list<string|Param>,
 *                 allowed_media_hosts?: null|string|list<string|Param>,
 *                 allow_relative_medias?: bool|Param, // Allows relative URLs to be used in media source attributes (img, audio, video, ...). // Default: false
 *                 with_attribute_sanitizers?: string|list<string|Param>,
 *                 without_attribute_sanitizers?: string|list<string|Param>,
 *                 max_input_length?: int|Param, // The maximum length allowed for the sanitized input. // Default: 0
 *             }>,
 *     },
 *     webhook?: bool|array{ // Webhook configuration
 *         enabled?: bool|Param, // Default: false
 *         message_bus?: scalar|Param|null, // The message bus to use. // Default: "messenger.default_bus"
 *         routing?: array<string, array{ // Default: []
 *                 service?: scalar|Param|null,
 *                 secret?: scalar|Param|null, // Default: ""
 *             }>,
 *     },
 *     remote-event?: bool|array{ // RemoteEvent configuration
 *         enabled?: bool|Param, // Default: false
 *     },
 *     json_streamer?: bool|array{ // JSON streamer configuration
 *         enabled?: bool|Param, // Default: false
 *     },
 * }
 * @psalm-type TwigConfig = array{
 *     form_themes?: list<scalar|Param|null>,
 *     globals?: array<string, array{ // Default: []
 *             id?: scalar|Param|null,
 *             type?: scalar|Param|null,
 *             value?: mixed,
 *         }>,
 *     autoescape_service?: scalar|Param|null, // Default: null
 *     autoescape_service_method?: scalar|Param|null, // Default: null
 *     base_template_class?: scalar|Param|null, // Deprecated: The child node "base_template_class" at path "twig.base_template_class" is deprecated.
 *     cache?: scalar|Param|null, // Default: true
 *     charset?: scalar|Param|null, // Default: "%kernel.charset%"
 *     debug?: bool|Param, // Default: "%kernel.debug%"
 *     strict_variables?: bool|Param, // Default: "%kernel.debug%"
 *     auto_reload?: scalar|Param|null,
 *     optimizations?: int|Param,
 *     default_path?: scalar|Param|null, // The default path used to load templates. // Default: "%kernel.project_dir%/templates"
 *     file_name_pattern?: string|list<scalar|Param|null>,
 *     paths?: array<string, mixed>,
 *     date?: array{ // The default format options used by the date filter.
 *         format?: scalar|Param|null, // Default: "F j, Y H:i"
 *         interval_format?: scalar|Param|null, // Default: "%d days"
 *         timezone?: scalar|Param|null, // The timezone used when formatting dates, when set to null, the timezone returned by date_default_timezone_get() is used. // Default: null
 *     },
 *     number_format?: array{ // The default format options for the number_format filter.
 *         decimals?: int|Param, // Default: 0
 *         decimal_point?: scalar|Param|null, // Default: "."
 *         thousands_separator?: scalar|Param|null, // Default: ","
 *     },
 *     mailer?: array{
 *         html_to_text_converter?: scalar|Param|null, // A service implementing the "Symfony\Component\Mime\HtmlToTextConverter\HtmlToTextConverterInterface". // Default: null
 *     },
 * }
 * @psalm-type WebProfilerConfig = array{
 *     toolbar?: bool|array{ // Profiler toolbar configuration
 *         enabled?: bool|Param, // Default: false
 *         ajax_replace?: bool|Param, // Replace toolbar on AJAX requests // Default: false
 *     },
 *     intercept_redirects?: bool|Param, // Default: false
 *     excluded_ajax_paths?: scalar|Param|null, // Default: "^/((index|app(_[\\w]+)?)\\.php/)?_wdt"
 * }
 * @psalm-type MonologConfig = array{
 *     use_microseconds?: scalar|Param|null, // Default: true
 *     channels?: list<scalar|Param|null>,
 *     handlers?: array<string, array{ // Default: []
 *             type?: scalar|Param|null,
 *             id?: scalar|Param|null,
 *             enabled?: bool|Param, // Default: true
 *             priority?: scalar|Param|null, // Default: 0
 *             level?: scalar|Param|null, // Default: "DEBUG"
 *             bubble?: bool|Param, // Default: true
 *             interactive_only?: bool|Param, // Default: false
 *             app_name?: scalar|Param|null, // Default: null
 *             fill_extra_context?: bool|Param, // Default: false
 *             include_stacktraces?: bool|Param, // Default: false
 *             process_psr_3_messages?: array{
 *                 enabled?: bool|Param|null, // Default: null
 *                 date_format?: scalar|Param|null,
 *                 remove_used_context_fields?: bool|Param,
 *             },
 *             path?: scalar|Param|null, // Default: "%kernel.logs_dir%/%kernel.environment%.log"
 *             file_permission?: scalar|Param|null, // Default: null
 *             use_locking?: bool|Param, // Default: false
 *             filename_format?: scalar|Param|null, // Default: "{filename}-{date}"
 *             date_format?: scalar|Param|null, // Default: "Y-m-d"
 *             ident?: scalar|Param|null, // Default: false
 *             logopts?: scalar|Param|null, // Default: 1
 *             facility?: scalar|Param|null, // Default: "user"
 *             max_files?: scalar|Param|null, // Default: 0
 *             action_level?: scalar|Param|null, // Default: "WARNING"
 *             activation_strategy?: scalar|Param|null, // Default: null
 *             stop_buffering?: bool|Param, // Default: true
 *             passthru_level?: scalar|Param|null, // Default: null
 *             excluded_404s?: list<scalar|Param|null>,
 *             excluded_http_codes?: list<array{ // Default: []
 *                     code?: scalar|Param|null,
 *                     urls?: list<scalar|Param|null>,
 *                 }>,
 *             accepted_levels?: list<scalar|Param|null>,
 *             min_level?: scalar|Param|null, // Default: "DEBUG"
 *             max_level?: scalar|Param|null, // Default: "EMERGENCY"
 *             buffer_size?: scalar|Param|null, // Default: 0
 *             flush_on_overflow?: bool|Param, // Default: false
 *             handler?: scalar|Param|null,
 *             url?: scalar|Param|null,
 *             exchange?: scalar|Param|null,
 *             exchange_name?: scalar|Param|null, // Default: "log"
 *             room?: scalar|Param|null,
 *             message_format?: scalar|Param|null, // Default: "text"
 *             api_version?: scalar|Param|null, // Default: null
 *             channel?: scalar|Param|null, // Default: null
 *             bot_name?: scalar|Param|null, // Default: "Monolog"
 *             use_attachment?: scalar|Param|null, // Default: true
 *             use_short_attachment?: scalar|Param|null, // Default: false
 *             include_extra?: scalar|Param|null, // Default: false
 *             icon_emoji?: scalar|Param|null, // Default: null
 *             webhook_url?: scalar|Param|null,
 *             exclude_fields?: list<scalar|Param|null>,
 *             team?: scalar|Param|null,
 *             notify?: scalar|Param|null, // Default: false
 *             nickname?: scalar|Param|null, // Default: "Monolog"
 *             token?: scalar|Param|null,
 *             region?: scalar|Param|null,
 *             source?: scalar|Param|null,
 *             use_ssl?: bool|Param, // Default: true
 *             user?: mixed,
 *             title?: scalar|Param|null, // Default: null
 *             host?: scalar|Param|null, // Default: null
 *             port?: scalar|Param|null, // Default: 514
 *             config?: list<scalar|Param|null>,
 *             members?: list<scalar|Param|null>,
 *             connection_string?: scalar|Param|null,
 *             timeout?: scalar|Param|null,
 *             time?: scalar|Param|null, // Default: 60
 *             deduplication_level?: scalar|Param|null, // Default: 400
 *             store?: scalar|Param|null, // Default: null
 *             connection_timeout?: scalar|Param|null,
 *             persistent?: bool|Param,
 *             dsn?: scalar|Param|null,
 *             hub_id?: scalar|Param|null, // Default: null
 *             client_id?: scalar|Param|null, // Default: null
 *             auto_log_stacks?: scalar|Param|null, // Default: false
 *             release?: scalar|Param|null, // Default: null
 *             environment?: scalar|Param|null, // Default: null
 *             message_type?: scalar|Param|null, // Default: 0
 *             parse_mode?: scalar|Param|null, // Default: null
 *             disable_webpage_preview?: bool|Param|null, // Default: null
 *             disable_notification?: bool|Param|null, // Default: null
 *             split_long_messages?: bool|Param, // Default: false
 *             delay_between_messages?: bool|Param, // Default: false
 *             topic?: int|Param, // Default: null
 *             factor?: int|Param, // Default: 1
 *             tags?: string|list<scalar|Param|null>,
 *             console_formater_options?: mixed, // Deprecated: "monolog.handlers..console_formater_options.console_formater_options" is deprecated, use "monolog.handlers..console_formater_options.console_formatter_options" instead.
 *             console_formatter_options?: mixed, // Default: []
 *             formatter?: scalar|Param|null,
 *             nested?: bool|Param, // Default: false
 *             publisher?: string|array{
 *                 id?: scalar|Param|null,
 *                 hostname?: scalar|Param|null,
 *                 port?: scalar|Param|null, // Default: 12201
 *                 chunk_size?: scalar|Param|null, // Default: 1420
 *                 encoder?: "json"|"compressed_json"|Param,
 *             },
 *             mongo?: string|array{
 *                 id?: scalar|Param|null,
 *                 host?: scalar|Param|null,
 *                 port?: scalar|Param|null, // Default: 27017
 *                 user?: scalar|Param|null,
 *                 pass?: scalar|Param|null,
 *                 database?: scalar|Param|null, // Default: "monolog"
 *                 collection?: scalar|Param|null, // Default: "logs"
 *             },
 *             mongodb?: string|array{
 *                 id?: scalar|Param|null, // ID of a MongoDB\Client service
 *                 uri?: scalar|Param|null,
 *                 username?: scalar|Param|null,
 *                 password?: scalar|Param|null,
 *                 database?: scalar|Param|null, // Default: "monolog"
 *                 collection?: scalar|Param|null, // Default: "logs"
 *             },
 *             elasticsearch?: string|array{
 *                 id?: scalar|Param|null,
 *                 hosts?: list<scalar|Param|null>,
 *                 host?: scalar|Param|null,
 *                 port?: scalar|Param|null, // Default: 9200
 *                 transport?: scalar|Param|null, // Default: "Http"
 *                 user?: scalar|Param|null, // Default: null
 *                 password?: scalar|Param|null, // Default: null
 *             },
 *             index?: scalar|Param|null, // Default: "monolog"
 *             document_type?: scalar|Param|null, // Default: "logs"
 *             ignore_error?: scalar|Param|null, // Default: false
 *             redis?: string|array{
 *                 id?: scalar|Param|null,
 *                 host?: scalar|Param|null,
 *                 password?: scalar|Param|null, // Default: null
 *                 port?: scalar|Param|null, // Default: 6379
 *                 database?: scalar|Param|null, // Default: 0
 *                 key_name?: scalar|Param|null, // Default: "monolog_redis"
 *             },
 *             predis?: string|array{
 *                 id?: scalar|Param|null,
 *                 host?: scalar|Param|null,
 *             },
 *             from_email?: scalar|Param|null,
 *             to_email?: string|list<scalar|Param|null>,
 *             subject?: scalar|Param|null,
 *             content_type?: scalar|Param|null, // Default: null
 *             headers?: list<scalar|Param|null>,
 *             mailer?: scalar|Param|null, // Default: null
 *             email_prototype?: string|array{
 *                 id?: scalar|Param|null,
 *                 method?: scalar|Param|null, // Default: null
 *             },
 *             lazy?: bool|Param, // Default: true
 *             verbosity_levels?: array{
 *                 VERBOSITY_QUIET?: scalar|Param|null, // Default: "ERROR"
 *                 VERBOSITY_NORMAL?: scalar|Param|null, // Default: "WARNING"
 *                 VERBOSITY_VERBOSE?: scalar|Param|null, // Default: "NOTICE"
 *                 VERBOSITY_VERY_VERBOSE?: scalar|Param|null, // Default: "INFO"
 *                 VERBOSITY_DEBUG?: scalar|Param|null, // Default: "DEBUG"
 *             },
 *             channels?: string|array{
 *                 type?: scalar|Param|null,
 *                 elements?: list<scalar|Param|null>,
 *             },
 *         }>,
 * }
 * @psalm-type DebugConfig = array{
 *     max_items?: int|Param, // Max number of displayed items past the first level, -1 means no limit. // Default: 2500
 *     min_depth?: int|Param, // Minimum tree depth to clone all the items, 1 is default. // Default: 1
 *     max_string_length?: int|Param, // Max length of displayed strings, -1 means no limit. // Default: -1
 *     dump_destination?: scalar|Param|null, // A stream URL where dumps should be written to. // Default: null
 *     theme?: "dark"|"light"|Param, // Changes the color of the dump() output when rendered directly on the templating. "dark" (default) or "light". // Default: "dark"
 * }
 * @psalm-type MakerConfig = array{
 *     root_namespace?: scalar|Param|null, // Default: "App"
 *     generate_final_classes?: bool|Param, // Default: true
 *     generate_final_entities?: bool|Param, // Default: false
 * }
 * @psalm-type SecurityConfig = array{
 *     access_denied_url?: scalar|Param|null, // Default: null
 *     session_fixation_strategy?: "none"|"migrate"|"invalidate"|Param, // Default: "migrate"
 *     hide_user_not_found?: bool|Param, // Deprecated: The "hide_user_not_found" option is deprecated and will be removed in 8.0. Use the "expose_security_errors" option instead.
 *     expose_security_errors?: \Symfony\Component\Security\Http\Authentication\ExposeSecurityLevel::None|\Symfony\Component\Security\Http\Authentication\ExposeSecurityLevel::AccountStatus|\Symfony\Component\Security\Http\Authentication\ExposeSecurityLevel::All|Param, // Default: "none"
 *     erase_credentials?: bool|Param, // Default: true
 *     access_decision_manager?: array{
 *         strategy?: "affirmative"|"consensus"|"unanimous"|"priority"|Param,
 *         service?: scalar|Param|null,
 *         strategy_service?: scalar|Param|null,
 *         allow_if_all_abstain?: bool|Param, // Default: false
 *         allow_if_equal_granted_denied?: bool|Param, // Default: true
 *     },
 *     password_hashers?: array<string, string|array{ // Default: []
 *             algorithm?: scalar|Param|null,
 *             migrate_from?: string|list<scalar|Param|null>,
 *             hash_algorithm?: scalar|Param|null, // Name of hashing algorithm for PBKDF2 (i.e. sha256, sha512, etc..) See hash_algos() for a list of supported algorithms. // Default: "sha512"
 *             key_length?: scalar|Param|null, // Default: 40
 *             ignore_case?: bool|Param, // Default: false
 *             encode_as_base64?: bool|Param, // Default: true
 *             iterations?: scalar|Param|null, // Default: 5000
 *             cost?: int|Param, // Default: null
 *             memory_cost?: scalar|Param|null, // Default: null
 *             time_cost?: scalar|Param|null, // Default: null
 *             id?: scalar|Param|null,
 *         }>,
 *     providers?: array<string, array{ // Default: []
 *             id?: scalar|Param|null,
 *             chain?: array{
 *                 providers?: string|list<scalar|Param|null>,
 *             },
 *             memory?: array{
 *                 users?: array<string, array{ // Default: []
 *                         password?: scalar|Param|null, // Default: null
 *                         roles?: string|list<scalar|Param|null>,
 *                     }>,
 *             },
 *             ldap?: array{
 *                 service?: scalar|Param|null,
 *                 base_dn?: scalar|Param|null,
 *                 search_dn?: scalar|Param|null, // Default: null
 *                 search_password?: scalar|Param|null, // Default: null
 *                 extra_fields?: list<scalar|Param|null>,
 *                 default_roles?: string|list<scalar|Param|null>,
 *                 role_fetcher?: scalar|Param|null, // Default: null
 *                 uid_key?: scalar|Param|null, // Default: "sAMAccountName"
 *                 filter?: scalar|Param|null, // Default: "({uid_key}={user_identifier})"
 *                 password_attribute?: scalar|Param|null, // Default: null
 *             },
 *         }>,
 *     firewalls?: array<string, array{ // Default: []
 *             pattern?: scalar|Param|null,
 *             host?: scalar|Param|null,
 *             methods?: string|list<scalar|Param|null>,
 *             security?: bool|Param, // Default: true
 *             user_checker?: scalar|Param|null, // The UserChecker to use when authenticating users in this firewall. // Default: "security.user_checker"
 *             request_matcher?: scalar|Param|null,
 *             access_denied_url?: scalar|Param|null,
 *             access_denied_handler?: scalar|Param|null,
 *             entry_point?: scalar|Param|null, // An enabled authenticator name or a service id that implements "Symfony\Component\Security\Http\EntryPoint\AuthenticationEntryPointInterface".
 *             provider?: scalar|Param|null,
 *             stateless?: bool|Param, // Default: false
 *             lazy?: bool|Param, // Default: false
 *             context?: scalar|Param|null,
 *             logout?: array{
 *                 enable_csrf?: bool|Param|null, // Default: null
 *                 csrf_token_id?: scalar|Param|null, // Default: "logout"
 *                 csrf_parameter?: scalar|Param|null, // Default: "_csrf_token"
 *                 csrf_token_manager?: scalar|Param|null,
 *                 path?: scalar|Param|null, // Default: "/logout"
 *                 target?: scalar|Param|null, // Default: "/"
 *                 invalidate_session?: bool|Param, // Default: true
 *                 clear_site_data?: string|list<"*"|"cache"|"cookies"|"storage"|"executionContexts"|Param>,
 *                 delete_cookies?: string|array<string, array{ // Default: []
 *                         path?: scalar|Param|null, // Default: null
 *                         domain?: scalar|Param|null, // Default: null
 *                         secure?: scalar|Param|null, // Default: false
 *                         samesite?: scalar|Param|null, // Default: null
 *                         partitioned?: scalar|Param|null, // Default: false
 *                     }>,
 *             },
 *             switch_user?: array{
 *                 provider?: scalar|Param|null,
 *                 parameter?: scalar|Param|null, // Default: "_switch_user"
 *                 role?: scalar|Param|null, // Default: "ROLE_ALLOWED_TO_SWITCH"
 *                 target_route?: scalar|Param|null, // Default: null
 *             },
 *             required_badges?: list<scalar|Param|null>,
 *             custom_authenticators?: list<scalar|Param|null>,
 *             login_throttling?: array{
 *                 limiter?: scalar|Param|null, // A service id implementing "Symfony\Component\HttpFoundation\RateLimiter\RequestRateLimiterInterface".
 *                 max_attempts?: int|Param, // Default: 5
 *                 interval?: scalar|Param|null, // Default: "1 minute"
 *                 lock_factory?: scalar|Param|null, // The service ID of the lock factory used by the login rate limiter (or null to disable locking). // Default: null
 *                 cache_pool?: string|Param, // The cache pool to use for storing the limiter state // Default: "cache.rate_limiter"
 *                 storage_service?: string|Param, // The service ID of a custom storage implementation, this precedes any configured "cache_pool" // Default: null
 *             },
 *             x509?: array{
 *                 provider?: scalar|Param|null,
 *                 user?: scalar|Param|null, // Default: "SSL_CLIENT_S_DN_Email"
 *                 credentials?: scalar|Param|null, // Default: "SSL_CLIENT_S_DN"
 *                 user_identifier?: scalar|Param|null, // Default: "emailAddress"
 *             },
 *             remote_user?: array{
 *                 provider?: scalar|Param|null,
 *                 user?: scalar|Param|null, // Default: "REMOTE_USER"
 *             },
 *             login_link?: array{
 *                 check_route?: scalar|Param|null, // Route that will validate the login link - e.g. "app_login_link_verify".
 *                 check_post_only?: scalar|Param|null, // If true, only HTTP POST requests to "check_route" will be handled by the authenticator. // Default: false
 *                 signature_properties?: list<scalar|Param|null>,
 *                 lifetime?: int|Param, // The lifetime of the login link in seconds. // Default: 600
 *                 max_uses?: int|Param, // Max number of times a login link can be used - null means unlimited within lifetime. // Default: null
 *                 used_link_cache?: scalar|Param|null, // Cache service id used to expired links of max_uses is set.
 *                 success_handler?: scalar|Param|null, // A service id that implements Symfony\Component\Security\Http\Authentication\AuthenticationSuccessHandlerInterface.
 *                 failure_handler?: scalar|Param|null, // A service id that implements Symfony\Component\Security\Http\Authentication\AuthenticationFailureHandlerInterface.
 *                 provider?: scalar|Param|null, // The user provider to load users from.
 *                 secret?: scalar|Param|null, // Default: "%kernel.secret%"
 *                 always_use_default_target_path?: bool|Param, // Default: false
 *                 default_target_path?: scalar|Param|null, // Default: "/"
 *                 login_path?: scalar|Param|null, // Default: "/login"
 *                 target_path_parameter?: scalar|Param|null, // Default: "_target_path"
 *                 use_referer?: bool|Param, // Default: false
 *                 failure_path?: scalar|Param|null, // Default: null
 *                 failure_forward?: bool|Param, // Default: false
 *                 failure_path_parameter?: scalar|Param|null, // Default: "_failure_path"
 *             },
 *             form_login?: array{
 *                 provider?: scalar|Param|null,
 *                 remember_me?: bool|Param, // Default: true
 *                 success_handler?: scalar|Param|null,
 *                 failure_handler?: scalar|Param|null,
 *                 check_path?: scalar|Param|null, // Default: "/login_check"
 *                 use_forward?: bool|Param, // Default: false
 *                 login_path?: scalar|Param|null, // Default: "/login"
 *                 username_parameter?: scalar|Param|null, // Default: "_username"
 *                 password_parameter?: scalar|Param|null, // Default: "_password"
 *                 csrf_parameter?: scalar|Param|null, // Default: "_csrf_token"
 *                 csrf_token_id?: scalar|Param|null, // Default: "authenticate"
 *                 enable_csrf?: bool|Param, // Default: false
 *                 post_only?: bool|Param, // Default: true
 *                 form_only?: bool|Param, // Default: false
 *                 always_use_default_target_path?: bool|Param, // Default: false
 *                 default_target_path?: scalar|Param|null, // Default: "/"
 *                 target_path_parameter?: scalar|Param|null, // Default: "_target_path"
 *                 use_referer?: bool|Param, // Default: false
 *                 failure_path?: scalar|Param|null, // Default: null
 *                 failure_forward?: bool|Param, // Default: false
 *                 failure_path_parameter?: scalar|Param|null, // Default: "_failure_path"
 *             },
 *             form_login_ldap?: array{
 *                 provider?: scalar|Param|null,
 *                 remember_me?: bool|Param, // Default: true
 *                 success_handler?: scalar|Param|null,
 *                 failure_handler?: scalar|Param|null,
 *                 check_path?: scalar|Param|null, // Default: "/login_check"
 *                 use_forward?: bool|Param, // Default: false
 *                 login_path?: scalar|Param|null, // Default: "/login"
 *                 username_parameter?: scalar|Param|null, // Default: "_username"
 *                 password_parameter?: scalar|Param|null, // Default: "_password"
 *                 csrf_parameter?: scalar|Param|null, // Default: "_csrf_token"
 *                 csrf_token_id?: scalar|Param|null, // Default: "authenticate"
 *                 enable_csrf?: bool|Param, // Default: false
 *                 post_only?: bool|Param, // Default: true
 *                 form_only?: bool|Param, // Default: false
 *                 always_use_default_target_path?: bool|Param, // Default: false
 *                 default_target_path?: scalar|Param|null, // Default: "/"
 *                 target_path_parameter?: scalar|Param|null, // Default: "_target_path"
 *                 use_referer?: bool|Param, // Default: false
 *                 failure_path?: scalar|Param|null, // Default: null
 *                 failure_forward?: bool|Param, // Default: false
 *                 failure_path_parameter?: scalar|Param|null, // Default: "_failure_path"
 *                 service?: scalar|Param|null, // Default: "ldap"
 *                 dn_string?: scalar|Param|null, // Default: "{user_identifier}"
 *                 query_string?: scalar|Param|null,
 *                 search_dn?: scalar|Param|null, // Default: ""
 *                 search_password?: scalar|Param|null, // Default: ""
 *             },
 *             json_login?: array{
 *                 provider?: scalar|Param|null,
 *                 remember_me?: bool|Param, // Default: true
 *                 success_handler?: scalar|Param|null,
 *                 failure_handler?: scalar|Param|null,
 *                 check_path?: scalar|Param|null, // Default: "/login_check"
 *                 use_forward?: bool|Param, // Default: false
 *                 login_path?: scalar|Param|null, // Default: "/login"
 *                 username_path?: scalar|Param|null, // Default: "username"
 *                 password_path?: scalar|Param|null, // Default: "password"
 *             },
 *             json_login_ldap?: array{
 *                 provider?: scalar|Param|null,
 *                 remember_me?: bool|Param, // Default: true
 *                 success_handler?: scalar|Param|null,
 *                 failure_handler?: scalar|Param|null,
 *                 check_path?: scalar|Param|null, // Default: "/login_check"
 *                 use_forward?: bool|Param, // Default: false
 *                 login_path?: scalar|Param|null, // Default: "/login"
 *                 username_path?: scalar|Param|null, // Default: "username"
 *                 password_path?: scalar|Param|null, // Default: "password"
 *                 service?: scalar|Param|null, // Default: "ldap"
 *                 dn_string?: scalar|Param|null, // Default: "{user_identifier}"
 *                 query_string?: scalar|Param|null,
 *                 search_dn?: scalar|Param|null, // Default: ""
 *                 search_password?: scalar|Param|null, // Default: ""
 *             },
 *             access_token?: array{
 *                 provider?: scalar|Param|null,
 *                 remember_me?: bool|Param, // Default: true
 *                 success_handler?: scalar|Param|null,
 *                 failure_handler?: scalar|Param|null,
 *                 realm?: scalar|Param|null, // Default: null
 *                 token_extractors?: string|list<scalar|Param|null>,
 *                 token_handler?: string|array{
 *                     id?: scalar|Param|null,
 *                     oidc_user_info?: string|array{
 *                         base_uri?: scalar|Param|null, // Base URI of the userinfo endpoint on the OIDC server, or the OIDC server URI to use the discovery (require "discovery" to be configured).
 *                         discovery?: array{ // Enable the OIDC discovery.
 *                             cache?: array{
 *                                 id?: scalar|Param|null, // Cache service id to use to cache the OIDC discovery configuration.
 *                             },
 *                         },
 *                         claim?: scalar|Param|null, // Claim which contains the user identifier (e.g. sub, email, etc.). // Default: "sub"
 *                         client?: scalar|Param|null, // HttpClient service id to use to call the OIDC server.
 *                     },
 *                     oidc?: array{
 *                         discovery?: array{ // Enable the OIDC discovery.
 *                             base_uri?: string|list<scalar|Param|null>,
 *                             cache?: array{
 *                                 id?: scalar|Param|null, // Cache service id to use to cache the OIDC discovery configuration.
 *                             },
 *                         },
 *                         claim?: scalar|Param|null, // Claim which contains the user identifier (e.g.: sub, email..). // Default: "sub"
 *                         audience?: scalar|Param|null, // Audience set in the token, for validation purpose.
 *                         issuers?: list<scalar|Param|null>,
 *                         algorithm?: array<mixed>,
 *                         algorithms?: list<scalar|Param|null>,
 *                         key?: scalar|Param|null, // Deprecated: The "key" option is deprecated and will be removed in 8.0. Use the "keyset" option instead. // JSON-encoded JWK used to sign the token (must contain a "kty" key).
 *                         keyset?: scalar|Param|null, // JSON-encoded JWKSet used to sign the token (must contain a list of valid public keys).
 *                         encryption?: bool|array{
 *                             enabled?: bool|Param, // Default: false
 *                             enforce?: bool|Param, // When enabled, the token shall be encrypted. // Default: false
 *                             algorithms?: list<scalar|Param|null>,
 *                             keyset?: scalar|Param|null, // JSON-encoded JWKSet used to decrypt the token (must contain a list of valid private keys).
 *                         },
 *                     },
 *                     cas?: array{
 *                         validation_url?: scalar|Param|null, // CAS server validation URL
 *                         prefix?: scalar|Param|null, // CAS prefix // Default: "cas"
 *                         http_client?: scalar|Param|null, // HTTP Client service // Default: null
 *                     },
 *                     oauth2?: scalar|Param|null,
 *                 },
 *             },
 *             http_basic?: array{
 *                 provider?: scalar|Param|null,
 *                 realm?: scalar|Param|null, // Default: "Secured Area"
 *             },
 *             http_basic_ldap?: array{
 *                 provider?: scalar|Param|null,
 *                 realm?: scalar|Param|null, // Default: "Secured Area"
 *                 service?: scalar|Param|null, // Default: "ldap"
 *                 dn_string?: scalar|Param|null, // Default: "{user_identifier}"
 *                 query_string?: scalar|Param|null,
 *                 search_dn?: scalar|Param|null, // Default: ""
 *                 search_password?: scalar|Param|null, // Default: ""
 *             },
 *             remember_me?: array{
 *                 secret?: scalar|Param|null, // Default: "%kernel.secret%"
 *                 service?: scalar|Param|null,
 *                 user_providers?: string|list<scalar|Param|null>,
 *                 catch_exceptions?: bool|Param, // Default: true
 *                 signature_properties?: list<scalar|Param|null>,
 *                 token_provider?: string|array{
 *                     service?: scalar|Param|null, // The service ID of a custom remember-me token provider.
 *                     doctrine?: bool|array{
 *                         enabled?: bool|Param, // Default: false
 *                         connection?: scalar|Param|null, // Default: null
 *                     },
 *                 },
 *                 token_verifier?: scalar|Param|null, // The service ID of a custom rememberme token verifier.
 *                 name?: scalar|Param|null, // Default: "REMEMBERME"
 *                 lifetime?: int|Param, // Default: 31536000
 *                 path?: scalar|Param|null, // Default: "/"
 *                 domain?: scalar|Param|null, // Default: null
 *                 secure?: true|false|"auto"|Param, // Default: null
 *                 httponly?: bool|Param, // Default: true
 *                 samesite?: null|"lax"|"strict"|"none"|Param, // Default: "lax"
 *                 always_remember_me?: bool|Param, // Default: false
 *                 remember_me_parameter?: scalar|Param|null, // Default: "_remember_me"
 *             },
 *         }>,
 *     access_control?: list<array{ // Default: []
 *             request_matcher?: scalar|Param|null, // Default: null
 *             requires_channel?: scalar|Param|null, // Default: null
 *             path?: scalar|Param|null, // Use the urldecoded format. // Default: null
 *             host?: scalar|Param|null, // Default: null
 *             port?: int|Param, // Default: null
 *             ips?: string|list<scalar|Param|null>,
 *             attributes?: array<string, scalar|Param|null>,
 *             route?: scalar|Param|null, // Default: null
 *             methods?: string|list<scalar|Param|null>,
 *             allow_if?: scalar|Param|null, // Default: null
 *             roles?: string|list<scalar|Param|null>,
 *         }>,
 *     role_hierarchy?: array<string, string|list<scalar|Param|null>>,
 * }
 * @psalm-type TwigExtraConfig = array{
 *     cache?: bool|array{
 *         enabled?: bool|Param, // Default: false
 *     },
 *     html?: bool|array{
 *         enabled?: bool|Param, // Default: false
 *     },
 *     markdown?: bool|array{
 *         enabled?: bool|Param, // Default: false
 *     },
 *     intl?: bool|array{
 *         enabled?: bool|Param, // Default: true
 *     },
 *     cssinliner?: bool|array{
 *         enabled?: bool|Param, // Default: false
 *     },
 *     inky?: bool|array{
 *         enabled?: bool|Param, // Default: false
 *     },
 *     string?: bool|array{
 *         enabled?: bool|Param, // Default: false
 *     },
 *     commonmark?: array{
 *         renderer?: array{ // Array of options for rendering HTML.
 *             block_separator?: scalar|Param|null,
 *             inner_separator?: scalar|Param|null,
 *             soft_break?: scalar|Param|null,
 *         },
 *         html_input?: "strip"|"allow"|"escape"|Param, // How to handle HTML input.
 *         allow_unsafe_links?: bool|Param, // Remove risky link and image URLs by setting this to false. // Default: true
 *         max_nesting_level?: int|Param, // The maximum nesting level for blocks. // Default: 9223372036854775807
 *         max_delimiters_per_line?: int|Param, // The maximum number of strong/emphasis delimiters per line. // Default: 9223372036854775807
 *         slug_normalizer?: array{ // Array of options for configuring how URL-safe slugs are created.
 *             instance?: mixed,
 *             max_length?: int|Param, // Default: 255
 *             unique?: mixed,
 *         },
 *         commonmark?: array{ // Array of options for configuring the CommonMark core extension.
 *             enable_em?: bool|Param, // Default: true
 *             enable_strong?: bool|Param, // Default: true
 *             use_asterisk?: bool|Param, // Default: true
 *             use_underscore?: bool|Param, // Default: true
 *             unordered_list_markers?: list<scalar|Param|null>,
 *         },
 *         ...<string, mixed>
 *     },
 * }
 * @psalm-type FosHttpCacheConfig = array{
 *     generate_url_type?: "auto"|1|0|3|2|Param, // Set what URLs to generate on CacheManager::invalidate/refresh and InvalidationListener. Auto tries to guess the right mode based on your proxy client.
 *     cacheable?: array{
 *         response?: array{
 *             additional_status?: list<scalar|Param|null>,
 *             expression?: scalar|Param|null, // Expression to decide whether response is cacheable. Replaces the default status codes. // Default: null
 *         },
 *     },
 *     cache_control?: array{
 *         defaults?: array{
 *             overwrite?: bool|Param, // Whether to overwrite existing cache headers // Default: false
 *         },
 *         ttl_header?: scalar|Param|null, // Specify the header name to use with the cache_control.reverse_proxy_ttl setting // Default: "X-Reverse-Proxy-TTL"
 *         rules?: list<array{ // Default: []
 *                 match?: array{
 *                     path?: scalar|Param|null, // Request path. // Default: null
 *                     query_string?: scalar|Param|null, // Request query string. // Default: null
 *                     host?: scalar|Param|null, // Request host name. // Default: null
 *                     methods?: string|array<string, scalar|Param|null>,
 *                     ips?: string|array<string, scalar|Param|null>,
 *                     attributes?: array<string, scalar|Param|null>,
 *                     additional_response_status?: list<scalar|Param|null>,
 *                     match_response?: scalar|Param|null, // Expression to decide whether response should be matched. Replaces cacheable configuration. // Default: null
 *                     expression_language?: scalar|Param|null, // Service name of a custom ExpressionLanguage to use.
 *                 },
 *                 headers?: array{
 *                     overwrite?: "default"|true|false|Param, // Whether to overwrite cache headers for this rule, defaults to the cache_control.defaults.overwrite setting // Default: "default"
 *                     cache_control?: array{ // Add the specified cache control directives.
 *                         max_age?: scalar|Param|null,
 *                         s_maxage?: scalar|Param|null,
 *                         private?: bool|Param,
 *                         public?: bool|Param,
 *                         must_revalidate?: bool|Param,
 *                         proxy_revalidate?: bool|Param,
 *                         no_transform?: bool|Param,
 *                         no_cache?: bool|Param,
 *                         no_store?: bool|Param,
 *                         stale_if_error?: scalar|Param|null,
 *                         stale_while_revalidate?: scalar|Param|null,
 *                     },
 *                     etag?: "weak"|"strong"|false|Param, // Set a simple ETag which is just the md5 hash of the response body. You can specify which type of ETag you want by passing "strong" or "weak". // Default: false
 *                     last_modified?: scalar|Param|null, // Set a default last modified timestamp if none is set yet. Value must be parseable by DateTime
 *                     reverse_proxy_ttl?: scalar|Param|null, // Specify a custom time to live in seconds for your caching proxy. This value is sent in the custom header configured in cache_control.ttl_header. // Default: null
 *                     vary?: string|list<scalar|Param|null>,
 *                 },
 *             }>,
 *     },
 *     proxy_client?: array{
 *         default?: "varnish"|"nginx"|"symfony"|"cloudflare"|"cloudfront"|"fastly"|"noop"|Param, // If you configure more than one proxy client, you need to specify which client is the default.
 *         varnish?: array{
 *             tags_header?: scalar|Param|null, // HTTP header to use when sending tag invalidation requests to Varnish
 *             header_length?: scalar|Param|null, // Maximum header length when invalidating tags. If there are more tags to invalidate than fit into the header, the invalidation request is split into several requests.
 *             default_ban_headers?: array<string, scalar|Param|null>,
 *             tag_mode?: "ban"|"purgekeys"|Param, // If you can enable the xkey module in Varnish, use the purgekeys mode for more efficient tag handling // Default: "ban"
 *             http?: array{
 *                 servers?: array<string, scalar|Param|null>,
 *                 servers_from_jsonenv?: mixed, // Addresses of the hosts the caching proxy is running on (env var that contains a json array as a string). The values may be hostnames or ips, and with :port if not the default port 80.
 *                 base_url?: scalar|Param|null, // Default host name and optional path for path based invalidation. // Default: null
 *                 http_client?: scalar|Param|null, // Httplug async client service name to use for sending the requests. // Default: null
 *                 request_factory?: scalar|Param|null, // Service name of PSR-17 message factory. // Default: null
 *                 stream_factory?: scalar|Param|null, // Service name of PSR-17 stream factory. // Default: null
 *             },
 *         },
 *         nginx?: array{
 *             purge_location?: scalar|Param|null, // Path to trigger the purge on Nginx for different location purge. // Default: false
 *             http?: array{
 *                 servers?: array<string, scalar|Param|null>,
 *                 servers_from_jsonenv?: mixed, // Addresses of the hosts the caching proxy is running on (env var that contains a json array as a string). The values may be hostnames or ips, and with :port if not the default port 80.
 *                 base_url?: scalar|Param|null, // Default host name and optional path for path based invalidation. // Default: null
 *                 http_client?: scalar|Param|null, // Httplug async client service name to use for sending the requests. // Default: null
 *                 request_factory?: scalar|Param|null, // Service name of PSR-17 message factory. // Default: null
 *                 stream_factory?: scalar|Param|null, // Service name of PSR-17 stream factory. // Default: null
 *             },
 *         },
 *         symfony?: array{
 *             tags_header?: scalar|Param|null, // HTTP header to use when sending tag invalidation requests to Symfony HttpCache // Default: "X-Cache-Tags"
 *             tags_method?: scalar|Param|null, // HTTP method for sending tag invalidation requests to Symfony HttpCache // Default: "PURGETAGS"
 *             header_length?: scalar|Param|null, // Maximum header length when invalidating tags. If there are more tags to invalidate than fit into the header, the invalidation request is split into several requests.
 *             purge_method?: scalar|Param|null, // HTTP method to use when sending purge requests to Symfony HttpCache // Default: "PURGE"
 *             use_kernel_dispatcher?: bool|Param, // Dispatches invalidation requests to the kernel directly instead of executing real HTTP requests. Requires special kernel setup! Refer to the documentation for more information. // Default: false
 *             http?: array{
 *                 servers?: array<string, scalar|Param|null>,
 *                 servers_from_jsonenv?: mixed, // Addresses of the hosts the caching proxy is running on (env var that contains a json array as a string). The values may be hostnames or ips, and with :port if not the default port 80.
 *                 base_url?: scalar|Param|null, // Default host name and optional path for path based invalidation. // Default: null
 *                 http_client?: scalar|Param|null, // Httplug async client service name to use for sending the requests. // Default: null
 *                 request_factory?: scalar|Param|null, // Service name of PSR-17 message factory. // Default: null
 *                 stream_factory?: scalar|Param|null, // Service name of PSR-17 stream factory. // Default: null
 *             },
 *         },
 *         cloudflare?: array{
 *             authentication_token?: scalar|Param|null, // API authorization token, requires Zone.Cache Purge permissions
 *             zone_identifier?: scalar|Param|null, // Identifier for your Cloudflare zone you want to purge the cache for
 *             http?: array{
 *                 servers?: array<string, scalar|Param|null>,
 *                 http_client?: scalar|Param|null, // Httplug async client service name to use for sending the requests. // Default: null
 *             },
 *         },
 *         cloudfront?: array{ // Configure a client to interact with AWS cloudfront. You need to install jean-beru/fos-http-cache-cloudfront to work with cloudfront
 *             distribution_id?: scalar|Param|null, // Identifier for your CloudFront distribution you want to purge the cache for
 *             client?: scalar|Param|null, // AsyncAws\CloudFront\CloudFrontClient client to use // Default: null
 *             configuration?: mixed, // Client configuration from https://async-aws.com/configuration.html // Default: []
 *         },
 *         fastly?: array{ // Configure a client to interact with Fastly.
 *             service_identifier?: scalar|Param|null, // Identifier for your Fastly service account.
 *             authentication_token?: scalar|Param|null, // User token for authentication against Fastly APIs.
 *             soft_purge?: scalar|Param|null, // Boolean for doing soft purges or not on tag & URL purging. Soft purges expires the cache unlike hard purge (removal), and allow grace/stale handling within Fastly VCL. // Default: true
 *             http?: array{
 *                 servers?: array<string, scalar|Param|null>,
 *                 base_url?: scalar|Param|null, // Default host name and optional path for path based invalidation. // Default: "service"
 *                 http_client?: scalar|Param|null, // Httplug async client service name to use for sending the requests. // Default: null
 *             },
 *         },
 *         noop?: bool|Param,
 *     },
 *     cache_manager?: array{ // Configure the cache manager. Needs a proxy_client to be configured.
 *         enabled?: true|false|"auto"|Param, // Allows to disable the invalidation manager. Enabled by default if you configure a proxy client. // Default: "auto"
 *         custom_proxy_client?: scalar|Param|null, // Service name of a custom proxy client to use. With a custom client, generate_url_type defaults to ABSOLUTE_URL and tag support needs to be explicitly enabled. If no custom proxy client is specified, the first proxy client you configured is used.
 *         generate_url_type?: "auto"|1|0|3|2|Param, // Deprecated: Configure the url type on top level to also have it apply to the InvalidationListener in addition to the CacheManager // Set what URLs to generate on invalidate/refresh Route. Auto tries to guess the right mode based on your proxy client. // Default: "auto"
 *     },
 *     tags?: array{
 *         enabled?: true|false|"auto"|Param, // Allows to disable tag support. Enabled by default if you configured the cache manager and have a proxy client that supports tagging. // Default: "auto"
 *         strict?: bool|Param, // Default: false
 *         expression_language?: scalar|Param|null, // Service name of a custom ExpressionLanguage to use. // Default: null
 *         response_header?: scalar|Param|null, // HTTP header that contains cache tags. Defaults to xkey-softpurge for Varnish xkey or X-Cache-Tags otherwise // Default: null
 *         separator?: scalar|Param|null, // Character(s) to use to separate multiple tags. Defaults to " " for Varnish xkey or "," otherwise // Default: null
 *         max_header_value_length?: scalar|Param|null, // If configured the tag header value will be split into multiple response headers of the same name (see "response_header" configuration key) that all do not exceed the configured "max_header_value_length" (recommended is 4KB = 4096) - configure in bytes. // Default: null
 *         rules?: list<array{ // Default: []
 *                 match?: array{
 *                     path?: scalar|Param|null, // Request path. // Default: null
 *                     query_string?: scalar|Param|null, // Request query string. // Default: null
 *                     host?: scalar|Param|null, // Request host name. // Default: null
 *                     methods?: string|array<string, scalar|Param|null>,
 *                     ips?: string|array<string, scalar|Param|null>,
 *                     attributes?: array<string, scalar|Param|null>,
 *                 },
 *                 tags?: list<scalar|Param|null>,
 *                 tag_expressions?: list<scalar|Param|null>,
 *             }>,
 *     },
 *     invalidation?: array{
 *         enabled?: true|false|"auto"|Param, // Allows to disable the listener for invalidation. Enabled by default if the cache manager is configured. When disabled, the cache manager is no longer flushed automatically. // Default: "auto"
 *         expression_language?: scalar|Param|null, // Service name of a custom ExpressionLanguage to use. // Default: null
 *         rules?: list<array{ // Default: []
 *                 match?: array{
 *                     path?: scalar|Param|null, // Request path. // Default: null
 *                     query_string?: scalar|Param|null, // Request query string. // Default: null
 *                     host?: scalar|Param|null, // Request host name. // Default: null
 *                     methods?: string|array<string, scalar|Param|null>,
 *                     ips?: string|array<string, scalar|Param|null>,
 *                     attributes?: array<string, scalar|Param|null>,
 *                 },
 *                 routes?: array<string, array{ // Default: []
 *                         ignore_extra_params?: bool|Param, // Default: true
 *                     }>,
 *             }>,
 *     },
 *     user_context?: bool|array{ // Listener that returns the request for the user context hash as early as possible.
 *         enabled?: bool|Param, // Default: false
 *         match?: array{
 *             matcher_service?: scalar|Param|null, // Service id of a request matcher that tells whether the request is a context hash request. // Default: "fos_http_cache.user_context.request_matcher"
 *             accept?: scalar|Param|null, // Specify the accept HTTP header used for context hash requests. // Default: "application/vnd.fos.user-context-hash"
 *             method?: scalar|Param|null, // Specify the HTTP method used for context hash requests. // Default: null
 *         },
 *         hash_cache_ttl?: scalar|Param|null, // Cache the response for the hash for the specified number of seconds. Setting this to 0 will not cache those responses at all. // Default: 0
 *         always_vary_on_context_hash?: bool|Param, // Whether to always add the user context hash header name in the response Vary header. // Default: true
 *         user_identifier_headers?: list<scalar|Param|null>,
 *         session_name_prefix?: scalar|Param|null, // Prefix for session cookies. Must match your PHP session configuration. Set to false to ignore the session in user context. // Default: false
 *         user_hash_header?: scalar|Param|null, // Name of the header that contains the hash information for the context. // Default: "X-User-Context-Hash"
 *         role_provider?: bool|Param, // Whether to enable a provider that automatically adds all roles of the current user to the context. // Default: false
 *         logout_handler?: bool|array{
 *             enabled?: true|false|"auto"|Param, // Whether to enable the user context logout handler. // Default: "auto"
 *         },
 *     },
 *     flash_message?: bool|array{ // Activate the flash message listener that puts flash messages into a cookie.
 *         enabled?: bool|Param, // Default: false
 *         name?: scalar|Param|null, // Name of the cookie to set for flashes. // Default: "flashes"
 *         path?: scalar|Param|null, // Cookie path validity. // Default: "/"
 *         host?: scalar|Param|null, // Cookie host name validity. // Default: null
 *         secure?: scalar|Param|null, // Whether the cookie should only be transmitted over a secure HTTPS connection from the client. // Default: false
 *     },
 *     test?: array{
 *         cache_header?: scalar|Param|null, // HTTP cache hit/miss header // Default: "X-Cache"
 *         proxy_server?: array{ // Configure how caching proxy will be run in your tests
 *             default?: "varnish"|"nginx"|Param, // If you configure more than one proxy server, specify which client is the default.
 *             varnish?: array{
 *                 config_file?: scalar|Param|null,
 *                 binary?: scalar|Param|null, // Default: "varnishd"
 *                 port?: int|Param, // Default: 6181
 *                 ip?: scalar|Param|null, // Default: "127.0.0.1"
 *             },
 *             nginx?: array{
 *                 config_file?: scalar|Param|null,
 *                 binary?: scalar|Param|null, // Default: "nginx"
 *                 port?: int|Param, // Default: 8080
 *                 ip?: scalar|Param|null, // Default: "127.0.0.1"
 *             },
 *         },
 *     },
 *     debug?: bool|array{
 *         enabled?: bool|Param, // Whether to send a debug header with the response to trigger a caching proxy to send debug information. If not set, defaults to kernel.debug. // Default: true
 *         header?: scalar|Param|null, // The header to send if debug is true. // Default: "X-Cache-Debug"
 *     },
 * }
 * @psalm-type StrataConfig = array{
 *     preview_mode?: array{
 *         data_provider?: scalar|Param|null, // Data provider name to set preview mode on // Default: null
 *     },
 *     tags?: array{
 *         enabled?: bool|Param, // Whether cache tags are enabled // Default: false
 *     },
 * }
 * @psalm-type NelmioCorsConfig = array{
 *     defaults?: array{
 *         allow_credentials?: bool|Param, // Default: false
 *         allow_origin?: list<scalar|Param|null>,
 *         allow_headers?: list<scalar|Param|null>,
 *         allow_methods?: list<scalar|Param|null>,
 *         allow_private_network?: bool|Param, // Default: false
 *         expose_headers?: list<scalar|Param|null>,
 *         max_age?: scalar|Param|null, // Default: 0
 *         hosts?: list<scalar|Param|null>,
 *         origin_regex?: bool|Param, // Default: false
 *         forced_allow_origin_value?: scalar|Param|null, // Default: null
 *         skip_same_as_origin?: bool|Param, // Default: true
 *     },
 *     paths?: array<string, array{ // Default: []
 *             allow_credentials?: bool|Param,
 *             allow_origin?: list<scalar|Param|null>,
 *             allow_headers?: list<scalar|Param|null>,
 *             allow_methods?: list<scalar|Param|null>,
 *             allow_private_network?: bool|Param,
 *             expose_headers?: list<scalar|Param|null>,
 *             max_age?: scalar|Param|null, // Default: 0
 *             hosts?: list<scalar|Param|null>,
 *             origin_regex?: bool|Param,
 *             forced_allow_origin_value?: scalar|Param|null, // Default: null
 *             skip_same_as_origin?: bool|Param,
 *         }>,
 * }
 * @psalm-type ExerciseHtmlPurifierConfig = array{
 *     default_cache_serializer_path?: scalar|Param|null, // Default: "%kernel.cache_dir%/htmlpurifier"
 *     default_cache_serializer_permissions?: scalar|Param|null, // Default: 493
 *     html_profiles?: array<string, array{ // Default: []
 *             config?: array<string, mixed>,
 *             attributes?: array<string, array<string, scalar|Param|null>>,
 *             elements?: array<string, list<mixed>>,
 *             blank_elements?: list<scalar|Param|null>,
 *             parents?: list<scalar|Param|null>,
 *         }>,
 * }
 * @psalm-type ChrisguitarguyRequestIdConfig = array{
 *     request_header?: scalar|Param|null, // The header in which the bundle will look for and set request IDs // Default: "Request-Id"
 *     trust_request_header?: bool|Param, // Whether or not to trust the incoming request's `Request-Id` header as a real ID // Default: true
 *     response_header?: scalar|Param|null, // The header the bundle will set the request ID at in the response // Default: "Request-Id"
 *     storage_service?: scalar|Param|null, // The service name for request ID storage. Defaults to `SimpleIdStorage`
 *     generator_service?: scalar|Param|null, // The service name for the request ID generator. Defaults to `Uuid4IdGenerator`
 *     enable_monolog?: bool|Param, // Whether or not to turn on the request ID processor for monolog // Default: true
 *     enable_twig?: bool|Param, // Whether or not to enable the twig `request_id()` function. Only works if TwigBundle is present. // Default: true
 * }
 * @psalm-type EkreativeHealthCheckConfig = array{
 *     redis?: list<scalar|Param|null>,
 *     optional_redis?: list<scalar|Param|null>,
 *     doctrine?: list<scalar|Param|null>,
 *     optional_doctrine?: list<scalar|Param|null>,
 *     doctrine_enabled?: bool|Param, // Default: true
 * }
 * @psalm-type ConfigType = array{
 *     imports?: ImportsConfig,
 *     parameters?: ParametersConfig,
 *     services?: ServicesConfig,
 *     framework?: FrameworkConfig,
 *     twig?: TwigConfig,
 *     monolog?: MonologConfig,
 *     security?: SecurityConfig,
 *     twig_extra?: TwigExtraConfig,
 *     fos_http_cache?: FosHttpCacheConfig,
 *     strata?: StrataConfig,
 *     nelmio_cors?: NelmioCorsConfig,
 *     exercise_html_purifier?: ExerciseHtmlPurifierConfig,
 *     chrisguitarguy_request_id?: ChrisguitarguyRequestIdConfig,
 *     ekreative_health_check?: EkreativeHealthCheckConfig,
 *     "when@dev"?: array{
 *         imports?: ImportsConfig,
 *         parameters?: ParametersConfig,
 *         services?: ServicesConfig,
 *         framework?: FrameworkConfig,
 *         twig?: TwigConfig,
 *         web_profiler?: WebProfilerConfig,
 *         monolog?: MonologConfig,
 *         debug?: DebugConfig,
 *         maker?: MakerConfig,
 *         security?: SecurityConfig,
 *         twig_extra?: TwigExtraConfig,
 *         fos_http_cache?: FosHttpCacheConfig,
 *         strata?: StrataConfig,
 *         nelmio_cors?: NelmioCorsConfig,
 *         exercise_html_purifier?: ExerciseHtmlPurifierConfig,
 *         chrisguitarguy_request_id?: ChrisguitarguyRequestIdConfig,
 *         ekreative_health_check?: EkreativeHealthCheckConfig,
 *     },
 *     "when@prod"?: array{
 *         imports?: ImportsConfig,
 *         parameters?: ParametersConfig,
 *         services?: ServicesConfig,
 *         framework?: FrameworkConfig,
 *         twig?: TwigConfig,
 *         monolog?: MonologConfig,
 *         security?: SecurityConfig,
 *         twig_extra?: TwigExtraConfig,
 *         fos_http_cache?: FosHttpCacheConfig,
 *         strata?: StrataConfig,
 *         nelmio_cors?: NelmioCorsConfig,
 *         exercise_html_purifier?: ExerciseHtmlPurifierConfig,
 *         chrisguitarguy_request_id?: ChrisguitarguyRequestIdConfig,
 *         ekreative_health_check?: EkreativeHealthCheckConfig,
 *     },
 *     "when@test"?: array{
 *         imports?: ImportsConfig,
 *         parameters?: ParametersConfig,
 *         services?: ServicesConfig,
 *         framework?: FrameworkConfig,
 *         twig?: TwigConfig,
 *         web_profiler?: WebProfilerConfig,
 *         monolog?: MonologConfig,
 *         security?: SecurityConfig,
 *         twig_extra?: TwigExtraConfig,
 *         fos_http_cache?: FosHttpCacheConfig,
 *         strata?: StrataConfig,
 *         nelmio_cors?: NelmioCorsConfig,
 *         exercise_html_purifier?: ExerciseHtmlPurifierConfig,
 *         chrisguitarguy_request_id?: ChrisguitarguyRequestIdConfig,
 *         ekreative_health_check?: EkreativeHealthCheckConfig,
 *     },
 *     ...<string, ExtensionType|array{ // extra keys must follow the when@%env% pattern or match an extension alias
 *         imports?: ImportsConfig,
 *         parameters?: ParametersConfig,
 *         services?: ServicesConfig,
 *         ...<string, ExtensionType>,
 *     }>
 * }
 */
final class App
{
    /**
     * @param ConfigType $config
     *
     * @psalm-return ConfigType
     */
    public static function config(array $config): array
    {
        /** @var ConfigType $config */
        $config = AppReference::config($config);

        return $config;
    }
}

namespace Symfony\Component\Routing\Loader\Configurator;

/**
 * This class provides array-shapes for configuring the routes of an application.
 *
 * Example:
 *
 *     ```php
 *     // config/routes.php
 *     namespace Symfony\Component\Routing\Loader\Configurator;
 *
 *     return Routes::config([
 *         'controllers' => [
 *             'resource' => 'routing.controllers',
 *         ],
 *     ]);
 *     ```
 *
 * @psalm-type RouteConfig = array{
 *     path: string|array<string,string>,
 *     controller?: string,
 *     methods?: string|list<string>,
 *     requirements?: array<string,string>,
 *     defaults?: array<string,mixed>,
 *     options?: array<string,mixed>,
 *     host?: string|array<string,string>,
 *     schemes?: string|list<string>,
 *     condition?: string,
 *     locale?: string,
 *     format?: string,
 *     utf8?: bool,
 *     stateless?: bool,
 * }
 * @psalm-type ImportConfig = array{
 *     resource: string,
 *     type?: string,
 *     exclude?: string|list<string>,
 *     prefix?: string|array<string,string>,
 *     name_prefix?: string,
 *     trailing_slash_on_root?: bool,
 *     controller?: string,
 *     methods?: string|list<string>,
 *     requirements?: array<string,string>,
 *     defaults?: array<string,mixed>,
 *     options?: array<string,mixed>,
 *     host?: string|array<string,string>,
 *     schemes?: string|list<string>,
 *     condition?: string,
 *     locale?: string,
 *     format?: string,
 *     utf8?: bool,
 *     stateless?: bool,
 * }
 * @psalm-type AliasConfig = array{
 *     alias: string,
 *     deprecated?: array{package:string, version:string, message?:string},
 * }
 * @psalm-type RoutesConfig = array{
 *     "when@dev"?: array<string, RouteConfig|ImportConfig|AliasConfig>,
 *     "when@prod"?: array<string, RouteConfig|ImportConfig|AliasConfig>,
 *     "when@test"?: array<string, RouteConfig|ImportConfig|AliasConfig>,
 *     ...<string, RouteConfig|ImportConfig|AliasConfig>
 * }
 */
final class Routes
{
    /**
     * @param RoutesConfig $config
     *
     * @psalm-return RoutesConfig
     */
    public static function config(array $config): array
    {
        return $config;
    }
}
