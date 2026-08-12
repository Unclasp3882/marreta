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

uses(RefreshDatabase::class);

it('serves the domain rules pages', function () {
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
});

it('round trips the domain rule config through the form', function () {
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
    expect($hydratedExcludeRows)->toBe([['rule_type' => 'scriptTagRemove', 'values' => ['ga.js', 'gtm.js']]]);

    $component->fillForm([
        'config.idElementRemove' => ['paywall', 'subscribe-wall'],
        'config.customStyle' => 'body { display: none; }',
    ])
        ->call('save')
        ->assertHasNoFormErrors();

    $rule->refresh();

    expect($rule->config['idElementRemove'])->toBe(['paywall', 'subscribe-wall'])
        ->and($rule->config['customStyle'])->toBe('body { display: none; }')
        ->and($rule->config['excludeGlobalRules']['scriptTagRemove'])->toBe(['ga.js', 'gtm.js'])
        ->and($rule->config['urlMods']['query'])->toBe([['key' => 'amp', 'value' => '1']]);
});

it('serves the blocked domains page', function () {
    $user = User::factory()->create();
    BlockedDomain::create(['domain' => 'blocked.example', 'reason' => 'test']);

    $this->actingAs($user)
        ->get('/admin/blocked-domains')
        ->assertOk();
});

it('serves the dmca domains page', function () {
    $user = User::factory()->create();
    DmcaDomain::create(['host' => 'dmca.example', 'message' => 'blocked']);

    $this->actingAs($user)
        ->get('/admin/dmca-domains')
        ->assertOk();
});

it('saves the global rules page and clears the cache', function () {
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

    expect($ruleSet)->not->toBeNull()
        ->and($ruleSet->config['idElementRemove'])->toBe(['cookie-banner'])
        ->and($ruleSet->config['customStyle'])->toBe('body { color: red; }')
        ->and(Cache::has('marreta.global_rules'))->toBeFalse();
});

it('shows the cache count widget on the dashboard but not on the home page', function () {
    $this->actingAs(User::factory()->create());

    $this->get('/admin')->assertOk();

    Livewire::test(CacheStats::class)
        ->assertSeeText(__('admin.dashboard.cache_count'));

    $this->get('/')
        ->assertOk()
        ->assertDontSee('walls_destroyed')
        ->assertDontSeeLivewire('stats-counter');
});

it('translates the admin texts to english', function () {
    app()->setLocale('en');
    $this->actingAs(User::factory()->create());

    $response = $this->get('/admin/domain-rules')->assertOk();

    $this->assertStringContainsStringIgnoringCase('domain rules', strip_tags($response->getContent()));
});

it('translates the admin texts to spanish', function () {
    app()->setLocale('es');
    $this->actingAs(User::factory()->create());

    $response = $this->get('/admin/domain-rules')->assertOk();

    $this->assertStringContainsStringIgnoringCase('reglas de dominio', strip_tags($response->getContent()));
});
