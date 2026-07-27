<?php

declare(strict_types=1);

namespace App\Services;

use App\Enums\MarretaError;
use App\Exceptions\MarretaException;
use Dom\Element;
use Dom\HTMLDocument;
use Dom\XPath;

final class ContentProcessor
{
    /** @var list<string> */
    private array $activatedRules = [];

    public function __construct(
        private readonly RuleEngine $ruleEngine,
    ) {}

    public function processContent(string $content, string $host, string $url): string
    {
        if (strlen($content) < 5120) {
            throw new MarretaException(MarretaError::ContentError);
        }

        $this->activatedRules = [];

        $dom = HTMLDocument::createFromString($content, LIBXML_NOERROR);

        $this->processCanonicalLinks($dom, $url);
        $this->fixRelativeUrls($dom, $url);
        $this->applyDomainRules($dom, $host);
        $this->cleanInlineStyles($dom);
        $this->addBrandBar($dom, $url);
        $this->addDebugBar($dom);

        return $dom->saveHTML();
    }

    /**
     * @return list<string>
     */
    public function activatedRules(): array
    {
        return $this->activatedRules;
    }

    private function processCanonicalLinks(HTMLDocument $dom, string $url): void
    {
        foreach ($dom->querySelectorAll("link[rel='canonical']") as $link) {
            $link->parentNode->removeChild($link);
        }

        $head = $dom->querySelector('head');
        if ($head) {
            $canonical = $dom->createElement('link');
            $canonical->setAttribute('rel', 'canonical');
            $canonical->setAttribute('href', $url);
            $head->append($canonical);
        }
    }

    private function fixRelativeUrls(HTMLDocument $dom, string $baseUrl): void
    {
        $parsed = parse_url($baseUrl);
        $baseHost = ($parsed['scheme'] ?? 'https').'://'.($parsed['host'] ?? '');

        foreach ($dom->querySelectorAll('[src]') as $el) {
            $src = $el->getAttribute('src');
            if (str_starts_with($src, 'data:')) {
                continue;
            }
            if (! str_starts_with($src, 'http') && ! str_starts_with($src, '//')) {
                $el->setAttribute('src', $baseHost.'/'.ltrim($src, '/'));
            }
        }

        foreach ($dom->querySelectorAll('[href]') as $el) {
            $href = $el->getAttribute('href');
            if (preg_match('/^(mailto:|tel:|javascript:|#)/', $href)) {
                continue;
            }
            if (! str_starts_with($href, 'http') && ! str_starts_with($href, '//')) {
                $el->setAttribute('href', $baseHost.'/'.ltrim($href, '/'));
            }
        }
    }

    private function applyDomainRules(HTMLDocument $dom, string $host): void
    {
        $rules = $this->ruleEngine->getDomainRules($host);

        if (isset($rules['customStyle'])) {
            $style = $dom->createElement('style');
            $style->textContent = $rules['customStyle'];
            $dom->querySelector('head')?->append($style);
            $this->activatedRules[] = 'customStyle';
        }

        if (isset($rules['customCode'])) {
            $script = $dom->createElement('script');
            $script->textContent = $rules['customCode'];
            $script->setAttribute('type', 'text/javascript');
            $dom->querySelector('body')?->append($script);
        }

        $this->removeUnwantedElements($dom, $rules);
    }

    /** @param  array<string, mixed>  $rules */
    private function removeUnwantedElements(HTMLDocument $dom, array $rules): void
    {
        // classAttrRemove — strip specific class names from elements
        foreach ($rules['classAttrRemove'] ?? [] as $class) {
            $elements = $dom->querySelectorAll("*[class~='$class']");
            if ($elements->length > 0) {
                foreach ($elements as $el) {
                    $this->removeClassNames($el, [$class]);
                }
                $this->activatedRules[] = "classAttrRemove: $class";
            }
        }

        // removeElementsByTag
        foreach ($rules['removeElementsByTag'] ?? [] as $tag) {
            $elements = $dom->querySelectorAll($tag);
            if ($elements->length > 0) {
                foreach ($elements as $el) {
                    $el->parentNode->removeChild($el);
                }
                $this->activatedRules[] = "removeElementsByTag: $tag";
            }
        }

        // idElementRemove
        foreach ($rules['idElementRemove'] ?? [] as $id) {
            $el = $dom->querySelector("#$id");
            if ($el) {
                $el->parentNode->removeChild($el);
                $this->activatedRules[] = "idElementRemove: $id";
            }
        }

        // classElementRemove
        foreach ($rules['classElementRemove'] ?? [] as $class) {
            $elements = $dom->querySelectorAll(".$class");
            if ($elements->length > 0) {
                foreach ($elements as $el) {
                    $el->parentNode->removeChild($el);
                }
                $this->activatedRules[] = "classElementRemove: $class";
            }
        }

        // scriptTagRemove — by src, href and inline text
        foreach ($rules['scriptTagRemove'] ?? [] as $script) {
            $found = false;

            $elements = $dom->querySelectorAll("script[src*='$script']");
            if ($elements->length > 0) {
                $found = true;
                foreach ($elements as $el) {
                    $el->parentNode->removeChild($el);
                }
            }

            $elements = $dom->querySelectorAll("link[as='script'][href*='$script']");
            if ($elements->length > 0) {
                $found = true;
                foreach ($elements as $el) {
                    $el->parentNode->removeChild($el);
                }
            }

            $xpath = new XPath($dom);
            $elements = $xpath->query("//script[contains(text(), '$script')]");
            if ($elements->length > 0) {
                $found = true;
                foreach ($elements as $el) {
                    $el->parentNode->removeChild($el);
                }
            }

            if ($found) {
                $this->activatedRules[] = "scriptTagRemove: $script";
            }
        }

        // removeCustomAttr — by attribute name or pattern
        foreach ($rules['removeCustomAttr'] ?? [] as $attrPattern) {
            $found = false;
            if (str_contains($attrPattern, '*')) {
                $pattern = '/^'.str_replace('*', '.*', $attrPattern).'$/';
                foreach ($dom->querySelectorAll('*') as $el) {
                    foreach ($el->attributes as $attr) {
                        if (preg_match($pattern, $attr->name)) {
                            $el->removeAttribute($attr->name);
                            $found = true;
                        }
                    }
                }
            } else {
                $elements = $dom->querySelectorAll("[$attrPattern]");
                if ($elements->length > 0) {
                    $found = true;
                    foreach ($elements as $el) {
                        $el->removeAttribute($attrPattern);
                    }
                }
            }
            if ($found) {
                $this->activatedRules[] = "removeCustomAttr: $attrPattern";
            }
        }
    }

    private function cleanInlineStyles(HTMLDocument $dom): void
    {
        foreach ($dom->querySelectorAll('[style]') as $el) {
            $style = $el->getAttribute('style');
            $style = preg_replace(
                '/(max-height|height|overflow|position|display|visibility)\s*:\s*[^;]+;?/',
                '',
                $style
            );
            $el->setAttribute('style', $style);
        }
    }

    private function addBrandBar(HTMLDocument $dom, string $url): void
    {
        $body = $dom->querySelector('body');
        if (! $body) {
            return;
        }

        $brandDiv = $dom->createElement('div');
        $brandDiv->setAttribute(
            'style',
            'z-index:2147483647;position:fixed;top:0;right:1rem;display:flex;gap:8px;'
        );

        $safeUrl = htmlspecialchars($url, ENT_QUOTES | ENT_HTML5, 'UTF-8');
        $siteUrl = htmlspecialchars(config('marreta.site_url'), ENT_QUOTES | ENT_HTML5, 'UTF-8');

        $linkSvg = '<svg xmlns="http://www.w3.org/2000/svg" fill="#fff" viewBox="0 0 16 16" width="20" height="20"><path d="M4.715 6.542 3.343 7.914a3 3 0 1 0 4.243 4.243l1.828-1.829A3 3 0 0 0 8.586 5.5L8 6.086a1 1 0 0 0-.154.199 2 2 0 0 1 .861 3.337L6.88 11.45a2 2 0 1 1-2.83-2.83l.793-.792a4 4 0 0 1-.128-1.287z"/><path d="M6.586 4.672A3 3 0 0 0 7.414 9.5l.775-.776a2 2 0 0 1-.896-3.346L9.12 3.55a2 2 0 1 1 2.83 2.83l-.793.792c.112.42.155.855.128 1.287l1.372-1.372a3 3 0 1 0-4.243-4.243z"/></svg>';
        $siteSvg = '<svg xmlns="http://www.w3.org/2000/svg" fill="#fff" viewBox="0 0 640 512" width="20" height="20"><path d="m283.9 378.6 18.3-60.1c18-4.1 34.2-16 43.1-33.8l64-128c10.5-21.1 8.4-45.2-3.7-63.6l52.7-76.6c3.7-5.4 10.4-8 16.7-6.5s11.2 6.7 12.2 13.1l16.2 104.1 105.1-7.4c6.5-.5 12.7 3.1 15.5 9s1.8 12.9-2.6 17.8L550.1 224l71.3 77.5c4.4 4.8 5.5 11.9 2.6 17.8s-9 9.5-15.5 9l-105.1-7.4L487.3 425c-1 6.5-5.9 11.7-12.2 13.1s-13-1.1-16.7-6.5l-59.7-86.7-91.4 52.2c-5.7 3.3-12.8 2.7-17.9-1.4s-7.2-10.9-5.3-17.2zm28.3-101.7c-9.3 10.9-25.2 14.4-38.6 7.7l-65.9-32.9-85.7-42.9-104.3-52.2c-15.8-7.9-22.2-27.1-14.3-42.9l40-80C48.8 22.8 59.9 16 72 16h120c5 0 9.9 1.2 14.3 3.4l78.2 39.1 81.8 40.9c15.8 7.9 22.2 27.1 14.3 42.9l-64 128c-1.2 2.4-2.7 4.6-4.4 6.6zm-204.6-39.5 85.9 42.9L90.9 485.5C79 509.2 50.2 518.8 26.5 507s-33.3-40.8-21.4-64.5l102.5-205.1z"/></svg>';

        $btnStyle = 'color:#fff;line-height:1em;z-index:2147483647;text-decoration:none;font-weight:bold;background:rgba(37,99,235,0.9);box-shadow:0 4px 6px rgba(0,0,0,0.1);padding:6px 10px;margin:0px;overflow:hidden;border-bottom-left-radius:8px;border-bottom-right-radius:8px;';

        $brandDiv->innerHTML = "<a href=\"$safeUrl\" style=\"$btnStyle\" target=\"_blank\">$linkSvg</a><a href=\"$siteUrl\" style=\"$btnStyle\" target=\"_blank\">$siteSvg</a>";

        $body->append($brandDiv);
    }

    private function addDebugBar(HTMLDocument $dom): void
    {
        if (! app()->hasDebugModeEnabled()) {
            return;
        }

        $body = $dom->querySelector('body');
        if (! $body) {
            return;
        }

        $debugDiv = $dom->createElement('div');
        $debugDiv->setAttribute(
            'style',
            'z-index:2147483647;position:fixed;bottom:1rem;right:1rem;max-width:400px;padding:1rem;color:#000;background:rgba(255,255,255,0.9);backdrop-filter:blur(8px);border:1px solid #e5e7eb;border-radius:0.5rem;box-shadow:0 4px 6px rgba(0,0,0,0.1);overflow:auto;max-height:80vh;font-family:monospace;font-size:13px;line-height:1.4;'
        );

        if ($this->activatedRules === []) {
            $item = $dom->createElement('div');
            $item->textContent = 'No rules activated / Nenhuma regra ativada';
            $debugDiv->append($item);
        } else {
            foreach ($this->activatedRules as $rule) {
                $item = $dom->createElement('div');
                $item->textContent = $rule;
                $debugDiv->append($item);
            }
        }

        $body->append($debugDiv);
    }

    /** @param  list<string>  $classesToRemove */
    private function removeClassNames(Element $element, array $classesToRemove): void
    {
        if (! $element->hasAttribute('class')) {
            return;
        }

        $classes = explode(' ', $element->getAttribute('class'));
        $newClasses = array_filter($classes, fn ($c) => ! in_array(trim($c), $classesToRemove));

        if (empty($newClasses)) {
            $element->removeAttribute('class');
        } else {
            $element->setAttribute('class', implode(' ', $newClasses));
        }
    }
}
