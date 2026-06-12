<?php

namespace App\Twig;

use Twig\Extension\AbstractExtension;
use Twig\TwigFilter;

class MsToHM extends AbstractExtension
{
    /**
     * Registers custom Twig filters for time conversion.
     *
     * @return TwigFilter[]
     */
    public function getFilters(): array
    {
        return [
            // If your filter generates SAFE HTML, add a third parameter: ['is_safe' => ['html']]
            // Reference: https://twig.symfony.com/doc/2.x/advanced.html#automatic-escaping
            new TwigFilter('MsToHM', [$this, 'convertMillisecondsToHoursMinutes']),
            new TwigFilter('MsToMS', [$this, 'convertMillisecondsToMinutesSeconds']),
            new TwigFilter('SToHM', [$this, 'convertSecondsToHoursMinutes']),
            new TwigFilter('MToHM', [$this, 'convertMinutesToHoursMinutes']),
        ];
    }

    /**
     * @param int $milliseconds
     * @return string
     */
    public function convertMillisecondsToHoursMinutes(int $milliseconds): string
    {
        $totalSeconds = (int) ($milliseconds / 1000);
        $hours        = floor($totalSeconds / 3600);
        $minutes      = floor(($totalSeconds % 3600) / 60);

        return sprintf('%dh%02d', $hours, $minutes);
    }

    /**
     * @param int $milliseconds
     * @return string
     */
    public function convertMillisecondsToMinutesSeconds(int $milliseconds): string
    {
        $totalSeconds = (int) ($milliseconds / 1000);
        $totalMinutes = (int) floor($totalSeconds / 60);
        $seconds      = $totalSeconds % 60;

        $hoursPart   = '';
        $minutesPart = $totalMinutes;

        if ($totalMinutes >= 60) {
            $hours       = (int) floor($totalMinutes / 60);
            $minutesPart = $totalMinutes % 60;
            $hoursPart   = $hours . 'h ';
        }

        if ($seconds === 0) {
            return $hoursPart . $minutesPart . 'min';
        }

        return sprintf('%s%dmin %02ds', $hoursPart, $minutesPart, $seconds);
    }

    /**
     * @param int $seconds
     * @return string
     */
    public function convertSecondsToHoursMinutes(int $seconds): string
    {
        $hours   = (int) floor($seconds / 3600);
        $minutes = (int) floor(($seconds % 3600) / 60);

        if ($minutes === 0) {
            return $hours . 'h';
        }

        return sprintf('%dh%02d', $hours, $minutes);
    }

    /**
     * @param int $minutes
     * @return string
     */
    public function convertMinutesToHoursMinutes(int $minutes): string
    {
        $hours = intdiv($minutes, 60);
        $mins  = $minutes % 60;

        return $mins === 0 ? "{$hours}h" : sprintf('%dh%02d', $hours, $mins);
    }
}
