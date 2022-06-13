<?php

namespace FMT\PublicBundle\Twig;

use FOS\UserBundle\Util\TokenGeneratorInterface;
use Twig_Extension;
use \Twig_SimpleFilter;

/**
 * Class TextExtension
 * @package FMT\PublicBundle\Twig
 */
class TextExtension extends Twig_Extension
{
    const NAME = 'text';

    private $tokenGenerator;

    public function __construct(TokenGeneratorInterface $tokenGenerator)
    {
        $this->tokenGenerator = $tokenGenerator;
    }

    /**
     * @return array
     */
    public function getFilters()
    {
        return [
            new Twig_SimpleFilter('plural', [$this, 'pluralFilter']),
            new Twig_SimpleFilter('truncate', [$this, 'truncateFilter']),
            new Twig_SimpleFilter('print_long_text', [$this, 'printLongText']),
            new Twig_SimpleFilter('quote', [$this, 'wrapQuote']),
        ];
    }

    /**
     * @return array|\Twig_Function[]
     */
    public function getFunctions()
    {
        return [
            new \Twig_SimpleFunction('random_string', [$this, 'getRandomString']),
        ];
    }

    /**
     * @return string
     */
    public function getName()
    {
        return self::NAME;
    }

    /**
     * @param $number
     * @param $word
     * @return string
     */
    public function pluralFilter($number, $word)
    {
        $number = abs($number);

        return $number <= 1 ? $word : $word . 's';
    }

    /**
     * @param $value
     * @param int $length
     * @param bool|false $preserve
     * @param string $separator
     * @return string
     */
    public function truncateFilter(
        $value,
        $length = 30,
        $preserve = false,
        $separator = '...'
    ) {
        if (strlen($value) > $length) {
            if ($preserve) {
                if (false !== ($breakpoint = strpos($value, ' ', $length))) {
                    $length = $breakpoint;
                }
            }

            return rtrim(substr($value, 0, $length)) . $separator;
        }

        return $value;
    }

    /**
     * @param $text
     * @param $template
     * @return string
     */
    public function printLongText($text, $template)
    {
        $result = '';
        $text = wordwrap($text, 70, PHP_EOL, true);
        $strings = preg_split('/\R/u', $text);
        foreach ($strings as $string) {
            $result .= sprintf($template, $string);
        }

        return $result;
    }

    /**
     * @param int $length
     * @return string
     */
    public function getRandomString(int $length = 32)
    {
        $string = '';
        while (mb_strlen($string) < $length) {
            $string .= preg_replace('/[^0-9a-z]/i', '', $this->tokenGenerator->generateToken());
        }

        return strtr($string, 0, $length);
    }

    public function wrapQuote($text)
    {
        $text = trim($text);

        return empty($text) ? '' : sprintf('"%s"', $text);
    }
}
