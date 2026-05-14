<?php

namespace App\Twig;

use Exception;
use Twig\Extension\AbstractExtension;
use Twig\TwigFilter;
use Twig\TwigFunction;
use DateTime;

class FrenchDateExtension extends AbstractExtension
{
    private const DAYS = [
        'Dimanche',
        'Lundi',
        'Mardi',
        'Mercredi',
        'Jeudi',
        'Vendredi',
        'Samedi',
    ];

    private const MONTHS = [
        'Janvier',
        'Février',
        'Mars',
        'Avril',
        'Mai',
        'Juin',
        'Juillet',
        'Août',
        'Septembre',
        'Octobre',
        'Novembre',
        'Décembre',

    ];


    public function getFilters(): array
    {

        return [
// If your filter generates SAFE HTML, you should add a third
// parameter: ['is_safe' => ['html']]
// Reference: https://twig.symfony.com/doc/2.x/advanced.html#automatic-escaping
            new TwigFilter('dateF', [$this, 'frenchFormatDate']),
            new TwigFilter('dateFWithHour', [$this, 'frenchFormatDateWithHour']),
            new TwigFilter('dateFNoDay', [$this, 'frenchFormatDateNoDay']),
            new TwigFilter('dateFNoDayWithHour', [$this, 'frenchFormatDateNoDayWithHour']),
        ];
    }


    public function getFunctions(): array
    {

        return [
            new TwigFunction('dateUpcoming', [$this, 'dateUpcoming']),
        ];
    }


    /**
     * @param DateTime|string       $date
     * @param string|null $type
     *
     * @return string
     * @throws Exception
     */
    public function dateUpcoming(DateTime|string $date, ?string $type): string
    {

        if (!$type) {
            $type = "day";
        }

        if (is_string($date)) {
            $date = new DateTime($date);
        }

        // Day
        $day = self::DAYS[$date->format('w')];

        // Month
        $month = self::MONTHS[$date->format('n') - 1];

        $year = $date->format('Y');

        // Suffix
        $numDay = $date->format('j');
        $suffixe = ($numDay == 1) ?
            'er' :
            '';

        return match ($type) {
            'year' => 'en '.$year,
            'month' => 'en '.$month." ".$year,
            'day' => 'le '.$day." ".$numDay.$suffixe." ".$month." ".$year,
            default => throw new \InvalidArgumentException(sprintf('Le type "%s" n\'est pas supporté.', $type)),
        };

    }


    /**
     * @param DateTime|string $date
     *
     * @return string
     * @throws Exception
     */
    public function frenchFormatDate(DateTime|string $date): string
    {

        if (is_string($date)) {
            $date = new DateTime($date);
        }

        // Day
        $day = self::DAYS[$date->format('w')];

        // Suffix
        $numberDay = $date->format('j');
        $suffix = ($numberDay == 1) ?
            'er' :
            '';

        // Month
        $month = self::MONTHS[$date->format('n') - 1];

        // Year
        $year = $date->format('Y');

        // return date
        return $day." ".$numberDay.$suffix." ".$month." ".$year;
    }


    /**
     * @param DateTime|string $date
     *
     * @return string
     * @throws Exception
     */
    public function frenchFormatDateWithHour(DateTime|string $date): string
    {

        if (is_string($date)) {
            $date = new DateTime($date);
        }

        // Day
        $day = self::DAYS[$date->format('w')];

        // Suffix
        $numberDay = $date->format('j');
        $suffix = ($numberDay == 1) ?
            'er' :
            '';

        // Month
        $month = self::MONTHS[$date->format('n') - 1];

        // Year
        $year = $date->format('Y');

        // return date
        return $day." ".$numberDay.$suffix." ".$month." ".$year.' à '.$date->format('H').'h'.$date->format('i');
    }


    /**
     * @param DateTime|string $date
     *
     * @return string
     * @throws Exception
     */
    public function frenchFormatDateNoDay(DateTime|string $date): string
    {

        if (is_string($date)) {
            $date = new DateTime($date);
        }

        // Suffix
        $numberDay = $date->format('j');
        $suffix = ($numberDay == 1) ?
            'er' :
            '';

        // Month
        $month = self::MONTHS[$date->format('n') - 1];

        // Year
        $year = $date->format('Y');

        // Return date
        return $numberDay.$suffix." ".$month." ".$year;
    }


    /**
     * @param DateTime|string $date
     *
     * @return string
     * @throws Exception
     */
    public function frenchFormatDateNoDayWithHour(DateTime|string $date): string
    {

        if (is_string($date)) {
            $date = new DateTime($date);
        }

        // Suffix
        $numberDay = $date->format('j');
        $suffix = ($numberDay == 1) ?
            'er' :
            '';

        // Month
        $month = self::MONTHS[$date->format('n') - 1];

        // Year
        $year = $date->format('Y');

        // Return date
        return $numberDay.$suffix." ".$month." ".$year.' - '.$date->format('H').':'.$date->format('i');
    }
}
