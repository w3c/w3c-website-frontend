# Internationalization

## Setting up languages

See [adding languages](adding-languages.md).

## Routing

See [routing locales](routing.md#locale).

## Messages

The application uses [Symfony Translation](https://symfony.com/doc/current/translation.html) to manage translated messages 
on the page (e.g. microcopy such as 'Displaying 100 results').

Translations are stored in `translations/` and use the [ICU message format](https://symfony.com/doc/current/translation/message_format.html).

It's important for messages to have clear and understandable keywords that represent what the message is for. We can 
also use nested groups to help indicate where the content is used. 

E.g.

```
footer.copyright_message: Copyright © 2020 W3C <sup>®</sup>
```

See https://symfony.com/doc/current/translation.html#using-real-or-keyword-messages

### Outputting messages in Twig

Output message via the [trans](https://symfony.com/doc/current/reference/twig_reference.html#trans) filter:

```html
{ 'title'|trans }}
```

You can pass arguments to the translation via:

```html
<p>{{ 'api_available.w3c'|trans({'available': w3c_available}) }}</p>
```

Read more on [selecting different messages based on a condition](https://symfony.com/doc/current/translation/message_format.html#selecting-different-messages-based-on-a-condition). 

## Messages from w3c/website-templates-bundle

[w3c/website-templates-bundle](https://github.com/w3c/website-templates-bundle) also defines and uses some translatable
strings for messages that are common to all templates. They are defined in a different translation domain named
`w3c_website_templates_bundle` as recommended
in [Symfony best practices](https://symfony.com/doc/current/bundles/best_practices.html#translation-files).
Those strings are defined in the [bundle's translations directory](https://github.com/w3c/w3c-website-templates-bundle/tree/main/translations).

To use those strings you need to pass the domain as a parameter of the trans filter or tag:
```html
{{ 'w3c.description'|trans([], 'w3c_website_templates_bundle') }}
```

### Outputting messages in JavaScript

There is a need to display localized messages on the front-end via JavaScript. The translation of these messages is managed
in the template bundle. Please refer to [the documentation on internationalization of the W3C template bundle](https://github.com/w3c/w3c-website-templates-bundle/blob/main/docs/internationalization/README.md).
