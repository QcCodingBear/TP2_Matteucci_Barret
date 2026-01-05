<?php

namespace Tests;

use Illuminate\Foundation\Testing\TestCase as BaseTestCase;
use App\Http\Controllers\Controller;

abstract class TestCase extends BaseTestCase
{
    // sources: https://laravel.com/docs/master/testing#creating-tests
    // et ne trouvant rien à part des choses tel que spl_autolad qui ne correspondait pas à ce que je cherchais,
    // Copilot: "Comment charger une classe avant le debut des tests?" -Il m'a proposé class_exists dans setUp()
    protected function setUp(): void
    {
        parent::setUp();

        class_exists(Controller::class);
    }
}
