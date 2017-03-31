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

use DateTime;

/**
 * The representation of a work day.
 *
 * @author Rafal Zajac <rzajac@gmail.com>
 */
class WorkDay
{
    /**
     * @var DateTime
     */
    private $date;

    /**
     * @var int
     */
    private $hoursWorked = 0;

    /**
     * Constructor.
     *
     * @param DateTime $date
     */
    public function __construct(DateTime $date)
    {
        $this->date = $date;
    }

    /**
     * Set hours worked on this day.
     *
     * @param int $hoursWorked
     */
    public function setHoursWorked($hoursWorked)
    {
        $this->hoursWorked = $hoursWorked;
    }

    /**
     * Add / subtract hours worked on the work day.
     *
     * @param int $delta
     */
    public function addHoursWorked($delta)
    {
        $newValue = $this->hoursWorked + $delta;
        if ($newValue > 0) {
            $this->hoursWorked = $newValue;
        }
    }

    /**
     * @return int
     */
    public function getHoursWorked()
    {
        return $this->hoursWorked;
    }

    /**
     * Returns true if date is on saturday or sunday.
     *
     * @return bool
     */
    public function isWeekend()
    {
        return (int)$this->date->format('N') > 5;
    }

    function __toString()
    {
        return strftime("%A, %Y-%m-%d\t", $this->date->getTimestamp()) . "\t\t" . $this->hoursWorked;
    }
}
