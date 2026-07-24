<?php

declare(strict_types=1);

namespace Tests\Feature\Filament;

use App\Filament\Pages\GlobalRules;
use App\Filament\Resources\DomainRules\Pages\EditDomainRule;
use App\Filament\Widgets\CacheStats;
use App\Models\BlockedDomain;
use App\Models\DmcaDomain;
use App\Models\DomainRule;
use App\Models\GlobalRuleSet;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Cache;
use Livewire\Livewire;
use Tests\TestCase;

final class AdminResourcesTest extends TestCase
{
    use RefreshDatabase;

    public function test_domain_rules_pages_are_accessible(): void
    {
        $user = User::factory()->create();
        $rule = DomainRule::create([
            'domain' => 'example.com',
            'config' => [
                'fetchStrategies' => 'fetchFromSelenium',
                'proxy' => true,
                'idElementRemove' => ['paywall'],
                'urlMods' => ['query' => [['key' => 'amp', 'value' => '1']]],
                'excludeGlobalRules' => ['scriptTagRemove' => ['ga.js']],
            ],
            'is_active' => true,
        ]);

        $this->actingAs($user)
            ->get('/admin/domain-rules')
            ->assertOk();

        $this->actingAs($user)
            ->get('/admin/domain-rules/create')
            ->assertOk();

        $this->actingAs($user)
            ->get("/admin/domain-rules/{$rule->id}/edit")
            ->assertOk();
    }

    public function test_domain_rule_config_round_trips_through_the_form(): void
    {
        $this->actingAs(User::factory()->create());

        $rule = DomainRule::create([
            'domain' => 'roundtrip.example',
            'config' => [
                'fetchStrategies' => 'fetchFromSelenium',
                'proxy' => true,
                'idElementRemove' => ['paywall'],
                'urlMods' => ['query' => [['key' => 'amp', 'value' => '1']]],
                'excludeGlobalRules' => ['scriptTagRemove' => ['ga.js', 'gtm.js']],
            ],
            'is_active' => true,
        ]);

        $component = Livewire::test(EditDomainRule::class, ['record' => $rule->id])
            ->assertFormSet([
                'config.fetchStrategies' => 'fetchFromSelenium',
                'config.proxy' => true,
                'config.idElementRemove' => ['paywall'],
            ]);

        $hydratedExcludeRows = array_values($component->instance()->form->getState()['config']['excludeGlobalRules'] ?? []);
        $this->assertSame([['rule_type' => 'scriptTagRemove', 'values' => ['ga.js', 'gtm.js']]], $hydratedExcludeRows);

        $component->fillForm([
                'config.idElementRemove' => ['paywall', 'subscribe-wall'],
                'config.customStyle' => 'body { display: none; }',
            ])
            ->call('save')
            ->assertHasNoFormErrors();

        $rule->refresh();

        $this->assertSame(['paywall', 'subscribe-wall'], $rule->config['idElementRemove']);
        $this->assertSame('body { display: none; }', $rule->config['customStyle']);
        $this->assertSame(['ga.js', 'gtm.js'], $rule->config['excludeGlobalRules']['scriptTagRemove']);
        $this->assertSame([['key' => 'amp', 'value' => '1']], $rule->config['urlMods']['query']);
    }

    public function test_blocked_domains_page_is_accessible(): void
    {
        $user = User::factory()->create();
        BlockedDomain::create(['domain' => 'blocked.example', 'reason' => 'test']);

        $this->actingAs($user)
            ->get('/admin/blocked-domains')
            ->assertOk();
    }

    public function test_dmca_domains_page_is_accessible(): void
    {
        $user = User::factory()->create();
        DmcaDomain::create(['host' => 'dmca.example', 'message' => 'blocked']);

        $this->actingAs($user)
            ->get('/admin/dmca-domains')
            ->assertOk();
    }

    public function test_global_rules_page_saves_and_clears_cache(): void
    {
        $this->actingAs(User::factory()->create());

        Cache::put('marreta.global_rules', ['stale' => true]);

        Livewire::test(GlobalRules::class)
            ->fillForm([
                'config.idElementRemove' => ['cookie-banner'],
                'config.customStyle' => 'body { color: red; }',
            ])
            ->call('save')
            ->assertHasNoFormErrors();

        $ruleSet = GlobalRuleSet::current();

        $this->assertNotNull($ruleSet);
        $this->assertSame(['cookie-banner'], $ruleSet->config['idElementRemove']);
        $this->assertSame('body { color: red; }', $ruleSet->config['customStyle']);
        $this->assertFalse(Cache::has('marreta.global_rules'));
    }

    public function test_dashboard_shows_cache_count_widget_and_home_page_does_not(): void
    {
        $this->actingAs(User::factory()->create());

        $this->get('/admin')->assertOk();

        Livewire::test(CacheStats::class)
            ->assertSeeText(__('admin.dashboard.cache_count'));

        $this->get('/')
            ->assertOk()
            ->assertDontSee('walls_destroyed')
            ->assertDontSeeLivewire('stats-counter');
    }

    public function test_admin_texts_are_translated_to_english(): void
    {
        app()->setLocale('en');
        $this->actingAs(User::factory()->create());

        $response = $this->get('/admin/domain-rules')->assertOk();
        $this->assertStringContainsStringIgnoringCase('domain rules', strip_tags($response->getContent()));
    }

    public function test_admin_texts_are_translated_to_spanish(): void
    {
        app()->setLocale('es');
        $this->actingAs(User::factory()->create());

        $response = $this->get('/admin/domain-rules')->assertOk();
        $this->assertStringContainsStringIgnoringCase('reglas de dominio', strip_tags($response->getContent()));
    }
}
