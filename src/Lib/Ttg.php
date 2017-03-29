<?php

/**
 * Copyright 2017 Rafal Zajac <rzajac@gmail.com>.
 *
 * Licensed under the Apache License, Version 2.0 (the "License"); you may
 * not use this file except in compliance with the License. You may obtain
 * a copy of the License at
 *
 *     http://www.apache.org/licenses/LICENSE-2.0
 *
 * Unless required by applicable law or agreed to in writing, software
 * distributed under the License is distributed on an "AS IS" BASIS, WITHOUT
 * WARRANTIES OR CONDITIONS OF ANY KIND, either express or implied. See the
 * License for the specific language governing permissions and limitations
 * under the License.
 */

namespace Kicaj\Ttg\Lib;

use DateInterval;
use DatePeriod;
use DateTime;
use Exception;

/**
 * Time table generator.
 *
 * @author Rafal Zajac <rzajac@gmail.com>
 */
class Ttg
{
    /**
     * @var int
     */
    private $totalHours;

    /**
     * @var DateTime
     */
    private $startDate;

    /**
     * @var DateTime
     */
    private $endDate;

    /**
     * @var bool
     */
    private $skipSaturday = true;

    /**
     * @var bool
     */
    private $skipSunday = true;

    /**
     * @var int
     */
    private $maxPerDay = 10;

    /**
     * Constructor.
     *
     * @param int    $year
     * @param int    $month
     * @param int    $total
     * @param string $locale
     *
     * @throws Exception
     */
    public function __construct($year, $month, $total = 160, $locale = 'pl_PL')
    {
        setlocale(LC_TIME, $locale);
        $this->startDate = DateTime::createFromFormat('Y-m-d', $year . '-' . $month . '-01');
        if (!$this->startDate) {
            throw new Exception('Pathetic!');
        }

        $this->endDate = clone $this->startDate;
        $this->endDate->modify('first day of next month');

        $this->totalHours = $total;
    }

    /**
     * Generate time table.
     *
     * @throws Exception
     *
     * @return string
     */
    public function generate()
    {
        $workDays = $this->getDays();
        $hoursAvg = floor($this->totalHours / count($workDays));

        $total = 0;
        foreach ($workDays as $day) {
            $day->setHoursWorked($hoursAvg);
            $total += $hoursAvg;
        }

        // If we are missing hours we need to randomly adjust the work days.
        if ($total < $this->totalHours) {
            $this->addMissing($workDays, $this->totalHours - $total);
        }

        return $this->render($workDays);
    }

    /**
     * Get array of days in the given period.
     *
     * @return WorkDay[]
     */
    private function getDays()
    {
        $interval = DateInterval::createFromDateString('1 day');
        $period = new DatePeriod($this->startDate, $interval, $this->endDate);

        $days = [];
        /** @var DateTime $dt */
        foreach ($period as $dt) {
            $workDay = new WorkDay($dt);
            if ($workDay->isWeekend() && $this->skipSaturday) {
                continue;
            }

            if ($workDay->isWeekend() && $this->skipSunday) {
                continue;
            }

            $days[] = $workDay;
        }

        return $days;
    }

    /**
     * Add missing hours.
     *
     * @param array $workDays
     * @param int   $missingHours
     *
     * @throws Exception
     * @return array
     */
    private function addMissing(array $workDays, $missingHours)
    {
        $tries = 0;
        while ($missingHours) {
            // Protect against infinite loop.
            if ($tries > 1000) {
                throw new Exception('Not able to to balance hours.');
            }
            $tries += 1;

            /** @var WorkDay $workDay */
            $workDay = $workDays[array_rand($workDays)];
            if ($workDay->getHoursWorked() + 1 > $this->maxPerDay) {
                continue;
            }
            $workDay->addHoursWorked(1);

            $missingHours--;
        }

        return $workDays;
    }

    /**
     * Render tabulated work days array.
     *
     * @param array $workDays
     *
     * @return string
     */
    private function render(array $workDays)
    {
        $str = '';

        /** @var WorkDay $day */
        foreach ($workDays as $day) {
            $str .= $day . "\n";
        }

        return $str;
    }

    /**
     * @param int $maxPerDay
     */
    public function setMaxPerDay($maxPerDay)
    {
        $this->maxPerDay = $maxPerDay;
    }
}
