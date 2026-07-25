<?php

declare(strict_types=1);

namespace Database\Seeders;

use App\Models\BlockedDomain;
use App\Models\DomainRule;
use App\Models\GlobalRuleSet;
use Illuminate\Database\Seeder;
use Illuminate\Filesystem\Filesystem;
use Illuminate\Support\Facades\Log;

final class MarretaDataSeeder extends Seeder
{
    private string $legacyAppPath;

    public function __construct(
        private readonly Filesystem $files,
    ) {
        $this->legacyAppPath = dirname(__DIR__).DIRECTORY_SEPARATOR.'data';
    }

    /**
     * Runs on every container start. Each table is only populated while it is
     * still empty, so rules edited or removed from the admin panel stay that way.
     */
    public function run(): void
    {
        $this->seedGlobalRules();
        $this->seedDomainRules();
        $this->seedBlockedDomains();
    }

    private function seedGlobalRules(): void
    {
        if (GlobalRuleSet::query()->exists()) {
            return;
        }

        $file = $this->legacyAppPath.DIRECTORY_SEPARATOR.'global_rules.php';

        if (! $this->files->exists($file)) {
            Log::warning('Global rules file not found', ['path' => $file]);

            return;
        }

        $rules = require $file;

        GlobalRuleSet::create(['config' => $rules]);
    }

    private function seedDomainRules(): void
    {
        if (DomainRule::query()->exists()) {
            return;
        }

        $file = $this->legacyAppPath.DIRECTORY_SEPARATOR.'domain_rules.php';

        if (! $this->files->exists($file)) {
            Log::warning('Domain rules file not found', ['path' => $file]);

            return;
        }

        $rules = require $file;

        foreach ($rules as $domain => $config) {
            DomainRule::create([
                'domain' => $domain,
                'config' => $config,
                'is_active' => true,
            ]);
        }
    }

    private function seedBlockedDomains(): void
    {
        if (BlockedDomain::query()->exists()) {
            return;
        }

        $file = $this->legacyAppPath.DIRECTORY_SEPARATOR.'blocked_domains.php';

        if (! $this->files->exists($file)) {
            Log::warning('Blocked domains file not found', ['path' => $file]);

            return;
        }

        $domains = require $file;
        $appHost = parse_url(config('marreta.site_url'), PHP_URL_HOST);
        $unique = [];

        foreach ($domains as $domain) {
            if ($domain === false || $domain === null || $domain === '') {
                // First placeholder entry maps to the app host (anti-loop)
                $domain = $appHost;
            }

            if (! $domain || isset($unique[$domain])) {
                continue;
            }

            $unique[$domain] = true;

            BlockedDomain::create([
                'domain' => (string) $domain
            ]);
        }
    }
}
