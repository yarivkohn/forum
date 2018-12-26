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

    protected $invalidKeyWords = [
        'yahoo customer support',
    ];


    public function detect($body)
    {
        if(preg_match('/(.)\\1{4,}/', $body)) {
            throw new SpamDetectedException('Spam alert');
        }
        return false;

    }
}