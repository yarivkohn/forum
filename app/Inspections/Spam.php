<?php
/**
 * Created by PhpStorm.
 * User: yariv
 * Date: 12/25/18
 * Time: 11:51 PM
 */

namespace App\Inspections;

class Spam
{

    protected $inspections = [
        InvalidKeywords::class,
        KeyHeldDown::class,
    ];

    /**
     * If SPAM was detected throws an exception,
     * Returns false otherwise.
     *
     * @param $body
     * @return bool
     * @throws \Exception
     */
    public function detect($body)
    {
        foreach($this->inspections as $inspection) {
            app($inspection)->detect($body);
        }
        return false;
    }
}
