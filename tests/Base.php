<?php

namespace Tests;

use App\Models\User;

class Base extends TestCase
{
    public function setUp() : void
    {
        parent::setUp();

        $this->actingAs(User::factory(['client_id' => null])->create());
    }
}