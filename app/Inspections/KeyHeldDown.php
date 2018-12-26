<?php
/**
 * Created by PhpStorm.
 * User: yariv
 * Date: 12/26/18
 * Time: 7:57 AM
 */

namespace App\Inspections;

class KeyHeldDown implements SpamDetectionInterface
{

    /**
     * @param $body
     * @return bool
     * @throws \Exception
     */
    public function detect($body)
    {
        if(preg_match('/(.)\\1{4,}/', $body)) {
            throw new \Exception('Spam alert');
        }
        return false;
    }
}