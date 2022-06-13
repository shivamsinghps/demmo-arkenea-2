<?php

declare(strict_types=1);

namespace FMT\InfrastructureBundle\Helper;

use Exception;

/**
 * Class CaseHelper
 */
class CaseHelper
{
    const CAMEL_CASE = 'camelCase';
    const SNAKE_CASE = 'snake_case';
    const KEBAB_CASE = 'kebab-case';

    /**
     * @param string $str
     * @param string $inCase
     * @param string $outCase
     *
     * @return string
     */
    public static function toCase(string $str, string $inCase, string $outCase): string
    {
        return self::toString(self::toWords($str, $inCase), $outCase);
    }

    /**
     * @param array  $words
     * @param string $case
     *
     * @return string
     * @throws Exception
     */
    private static function toString(array $words, string $case): string
    {
        $wordsInLoweCase = array_map('mb_strtolower', $words);

        switch ($case) {
            case self::CAMEL_CASE:
                $wordsWithFirstUpperCase = array_map('ucfirst', $wordsInLoweCase);
                $wordsWithFirstUpperCase[0] = lcfirst($wordsInLoweCase[0]);

                return implode('', $wordsWithFirstUpperCase);
            case self::SNAKE_CASE:
                return implode('_', $wordsInLoweCase);
            case self::KEBAB_CASE:
                return implode('-', $wordsInLoweCase);
        }

        throw new Exception(sprintf('Case "%s" not supported in %s', $case, self::class));
    }

    /**
     * @param string $str
     * @param string $case
     *
     * @return string[]
     * @throws Exception
     */
    private static function toWords(string $str, string $case): array
    {
        if (empty($str)) {
            throw new Exception('String cannot be empty');
        }

        switch ($case) {
            case self::CAMEL_CASE:
                return preg_split('/(?=[A-ZА-Я])/', $str);
            case self::SNAKE_CASE:
                return explode('_', $str);
            case self::KEBAB_CASE:
                return explode('-', $str);
        }

        throw new Exception(sprintf('Case "%s" not supported in %s', $case, self::class));
    }
}
