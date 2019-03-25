<?php
/**
 * Created by PhpStorm.
 * User: yariv
 * Date: 12/26/18
 * Time: 8:10 AM
 */

namespace App\Inspections;

interface SpamDetectionInterface {

    public function detect($body);
}