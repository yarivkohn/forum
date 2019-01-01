<?php

namespace Tests;

use Illuminate\Foundation\Testing\DatabaseMigrations;
use Illuminate\Support\Facades\DB;

abstract class DataBaseTestCase extends TestCase
{
    use CreatesApplication;
    use DatabaseMigrations;

    public function __construct(?string $name = null, array $data = [], string $dataName = '')
    {
        parent::__construct($name, $data, $dataName);
        DB::statement('PRAGMA foreign_key=on');
    }
}
