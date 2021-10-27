<?php

namespace App\Service;

class PasteWords
{
    public function paste(string $text, string $word = null, int $wordsCount = 1): string
    {
        if (is_null($word))
            return $text;

        $text = explode(' ',$text);

        for( $i = 0; $i < $wordsCount; $i++)
        {
            array_splice($text, rand( 0, count($text) ),0, $word);
        }
        return implode(' ',$text);
    }
}