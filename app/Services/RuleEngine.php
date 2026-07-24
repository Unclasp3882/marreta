<?php

declare(strict_types=1);

namespace App\Services;

use App\Models\DomainRule;
use App\Models\GlobalRuleSet;
use Illuminate\Support\Facades\Cache;

final class RuleEngine
{
    private const SUPPORTED_RULE_TYPES = [
        'userAgent', 'headers', 'idElementRemove', 'classElementRemove',
        'scriptTagRemove', 'cookies', 'classAttrRemove', 'customCode',
        'excludeGlobalRules', 'customStyle', 'socialReferrer', 'fetchStrategies',
        'fromGoogleBot', 'removeElementsByTag', 'removeCustomAttr', 'urlMods',
        'proxy', 'engine',
    ];

    private const MERGE_ASSOC_TYPES = ['cookies', 'headers'];

    /**
     * Get merged rules for a domain (domain-specific + global).
     */
    public function getDomainRules(string $domain): array
    {
        $rules = $this->findDomainRules($domain);

        if ($rules === null) {
            return $this->getGlobalRules();
        }

        return $this->mergeWithGlobalRules($rules);
    }

    public function hasDomainRules(string $domain): bool
    {
        return $this->findDomainRules($domain) !== null;
    }

    /**
     * @return array<string, mixed>|null
     */
    private function findDomainRules(string $domain): ?array
    {
        $domainRules = $this->allDomainRules();
        $baseDomain = $this->stripWww($domain);

        // Exact match
        foreach ($domainRules as $pattern => $rules) {
            if ($baseDomain === $this->stripWww($pattern)) {
                return $rules;
            }
        }

        // Subdomain suffix match (longest first)
        foreach ($this->domainParts($baseDomain) as $part) {
            foreach ($domainRules as $pattern => $rules) {
                if ($part === $this->stripWww($pattern)) {
                    return $rules;
                }
            }
        }

        return null;
    }

    /**
     * @return array<string, array<string, mixed>>
     */
    private function allDomainRules(): array
    {
        return Cache::rememberForever('marreta.domain_rules', function () {
            return DomainRule::where('is_active', true)
                ->pluck('config', 'domain')
                ->filter()
                ->toArray();
        });
    }

    public function getGlobalRules(): array
    {
        return Cache::rememberForever('marreta.global_rules', function () {
            $set = GlobalRuleSet::current();

            return $set?->config ?? [];
        });
    }

    /**
     * Merge domain rules with global rules, respecting excludeGlobalRules.
     *
     * @param  array<string, mixed>  $rules
     */
    private function mergeWithGlobalRules(array $rules): array
    {
        $globalRules = $this->getGlobalRules();
        $merged = [];

        $excludeGlobalRules = $rules['excludeGlobalRules'] ?? [];
        unset($rules['excludeGlobalRules']);

        // Start from global rules
        foreach ($globalRules as $ruleType => $globalTypeRules) {
            if (! in_array($ruleType, self::SUPPORTED_RULE_TYPES, true)) {
                continue;
            }

            if (isset($excludeGlobalRules[$ruleType])) {
                if (is_assoc_array($globalTypeRules)) {
                    $result = array_diff_key($globalTypeRules, array_flip($excludeGlobalRules[$ruleType]));
                    $merged[$ruleType] = is_array($result) ? $result : [];
                } else {
                    $result = array_diff($globalTypeRules, $excludeGlobalRules[$ruleType]);
                    $merged[$ruleType] = is_array($result) ? $result : [];
                }
            } else {
                $merged[$ruleType] = is_array($globalTypeRules) ? $globalTypeRules : [];
            }
        }

        // Overlay domain-specific rules
        foreach ($rules as $ruleType => $domainTypeRules) {
            if (! in_array($ruleType, self::SUPPORTED_RULE_TYPES, true)) {
                continue;
            }

            if (! isset($merged[$ruleType])) {
                $merged[$ruleType] = $domainTypeRules;
                continue;
            }

            if (in_array($ruleType, self::MERGE_ASSOC_TYPES, true)) {
                $merged[$ruleType] = array_merge(
                    is_array($merged[$ruleType]) ? $merged[$ruleType] : [],
                    is_array($domainTypeRules) ? $domainTypeRules : [],
                );
            } else {
                $merged[$ruleType] = array_values(array_unique(array_merge(
                    is_array($merged[$ruleType]) ? $merged[$ruleType] : [],
                    (array) $domainTypeRules,
                )));
            }
        }

        return $merged;
    }

    private function stripWww(string $domain): string
    {
        return preg_replace('/^www\./', '', $domain);
    }

    /**
     * Generate domain suffix combinations sorted by length (longest first).
     *
     * @return list<string>
     */
    private function domainParts(string $domain): array
    {
        $domain = $this->stripWww($domain);
        $parts = explode('.', $domain);
        $combinations = [];

        for ($i = 0; $i < count($parts) - 1; $i++) {
            $combinations[] = implode('.', array_slice($parts, $i));
        }

        usort($combinations, fn ($a, $b) => strlen($b) - strlen($a));

        return $combinations;
    }
}
