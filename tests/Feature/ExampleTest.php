<?php

namespace Tests\Feature;

use Illuminate\Foundation\Testing\RefreshDatabase;

uses(RefreshDatabase::class);

it('returns a successful response for the application', function () {
    $response = $this->get('/');

    $response->assertStatus(200);
});
