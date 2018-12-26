<?php
/**
 * Created by PhpStorm.
 * User: yariv
 * Date: 12/25/18
 * Time: 11:51 PM
 */

namespace App;

class Spam
{

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
        $this->detectInvalidKeyWords($body);

        return false;

    }

    /**
     * Detects SPAM according to save list of mal keywords
     * @param $body
     * @throws \Exception
     */
    private function detectInvalidKeyWords($body)
    {
        $invalidKeyWords = [
            'yahoo customers support',
        ];

        foreach ($invalidKeyWords as $keyWord) {
            if (stripos($body, $keyWord) !== false) {
                throw new \Exception('Spam alert');
            }
        }
    }

}
