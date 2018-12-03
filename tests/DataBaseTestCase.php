<?php

namespace Tests;

use Illuminate\Foundation\Testing\DatabaseMigrations;

abstract class DataBaseTestCase extends TestCase
{
    use CreatesApplication;
    use DatabaseMigrations;

}
