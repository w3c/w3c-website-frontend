<?php

namespace App\Twig;

use Masterminds\HTML5;
use Twig\Extension\AbstractExtension;
use Twig\TwigFilter;

class ContentFromCraftTwigExtension extends AbstractExtension
{
    public function getFilters(): array
    {
        return [
            new TwigFilter('i_to_em', [$this, 'iToEm']),
            new TwigFilter('formatTable', [$this, 'formatTable']),
        ];
    }

    public function iToEm(?string $text = ''): array|string|null
    {
        if (null === $text) {
            return '';
        }

        // Replace <i> with <em>
        return preg_replace([
            '/<i>/i',
            '/<\/i>/i'
        ], [
            '<em>',
            '</em>'
        ], $text);
    }

    public function formatTable($content): string
    {
        $html5 = new HTML5();

        // Wrap the incoming snippet so we can safely query & replace
        $dom =  $html5->loadHTML(
            '<!DOCTYPE html><meta charset="utf-8"><div id="__root__">'.$content.'</div>'
        );

        // Find our temporary root
        $xpath = new \DOMXPath($dom);
        $root  = $xpath->query('//*[@id="__root__"]')->item(0);

        // Collect <figure> nodes into an array (DOMNodeList is live)
        $figures = [];
        foreach ($dom->getElementsByTagName('figure') as $figure) {
            $figures[] = $figure;
        }

        foreach ($figures as $figure) {
            $table = $figure->getElementsByTagName('table')->item(0);

            if (!$table) {
                continue; // Skip if there's no <table> in the <figure>
            }

            // Create a <div> with the required attributes to wrap the <table>
            $div = $dom->createElement('div');
            $div->setAttribute('role', 'region');
            $div->setAttribute('tabindex', '0');
            $div->setAttribute('class', 'table-wrap');

            // If a caption has been entered, move this out of <figcaption> (which can be removed)
            // and into <caption>, which should be the first child element of <table>
            // Also set a globally-unique id on <caption>
            $figcaption = $figure->getElementsByTagName('figcaption')->item(0);
            if ($figcaption) {
                $captionContent = $figcaption->textContent;

                // Generate globally unique strong ID, with safe fallback
                try {
                    $captionId = 'table-caption-'.bin2hex(random_bytes(8));
                } catch (\Exception $e) {
                    $captionId = 'table-caption-'.str_replace('.', '-', uniqid('', true));
                }

                $caption = $dom->createElement('caption', $captionContent);
                $caption->setAttribute('id', $captionId);

                // Link the table to its caption for accessibility
                $div->setAttribute('aria-labelledby', $captionId);

                $table->insertBefore($caption, $table->firstChild);

                // Remove the original <figcaption>
                $figcaption->parentNode->removeChild($figcaption);
            }

            // Replace the <figure> wrapping the <table> with <div role="region" tabindex="0" class="table-wrap">
            $figure->parentNode->replaceChild($div, $figure);

            // move the <table> inside the <div>
            if ($table) {
                $div->appendChild($table);
            }
        }

        // Serialize only the content inside our temporary root
        $out = '';
        foreach (iterator_to_array($root->childNodes) as $child) {
            $out .= $dom->saveHTML($child);
        }

        return $out;
    }
}
