<?php
/**
 * Created by PhpStorm.
 * User: yariv
 * Date: 12/26/18
 * Time: 7:57 AM
 */

namespace App\Inspections;

class InvalidKeywords implements SpamDetectionInterface
{

    protected $invalidKeyWords = [
        'yahoo customer support',
    ];

    /**
     * Detects SPAM according to save list of mal keywords
     * @param $body
     * @throws \Exception
     */

    public function detect($body)
    {
        foreach ($this->invalidKeyWords as $keyWord) {
            if (stripos($body, $keyWord) !== false) {
                throw new SpamDetectedException('Spam alert');
            }
        }
    }
}