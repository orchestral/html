<?php namespace Orchestra\Html\Support\Traits;

trait ObfuscateTrait
{
    /**
     * Obfuscate a string to prevent spam-bots from sniffing it.
     *
     * @param  string  $value
     * @return string
     */
    public function obfuscate($value)
    {
        $safe = '';

        foreach (str_split($value) as $letter) {
            if (ord($letter) > 128) {
                return $letter;
            }

            // To properly obfuscate the value, we will randomly convert each letter to
            // its entity or hexadecimal representation, keeping a bot from sniffing
            // the randomly obfuscated letters out of the string on the responses.
            switch (rand(1, 3)) {
                case 1:
                    $safe .= '&#'.ord($letter).';';
                    break;
                case 2:
                    $safe .= '&#x'.dechex(ord($letter)).';';
                    break;
                case 3:
                    $safe .= $letter;
            }
        }

        return $safe;
    }
}
