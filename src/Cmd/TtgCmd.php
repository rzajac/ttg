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

namespace Kicaj\Ttg\Cmd;

use Kicaj\Ttg\Lib\Ttg;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Output\OutputInterface;

/**
 * Time table generator command.
 *
 * @author Rafal Zajac <rzajac@gmail.com>
 */
class TtgCmd extends Command
{
    protected function configure()
    {
        $this
            ->setName('ttg')
            ->setDescription('Generate time table.')
            ->addArgument(
                'month',
                InputArgument::REQUIRED,
                'The month to generate values for.'
            )
            ->addArgument(
                'total',
                InputArgument::OPTIONAL,
                'Total number of hours.',
                160
            )
            ->addOption(
                'year',
                'y',
                InputOption::VALUE_OPTIONAL,
                'The year to generate values for.',
                (new \DateTime())->format('Y')
            )
            ->addOption(
                'max',
                'm',
                InputOption::VALUE_OPTIONAL,
                'Maximum number of hours per day.',
                12
            )
            ->addOption(
                'locale',
                'l',
                InputOption::VALUE_OPTIONAL,
                'The locale for generated dates.',
                'pl_PL'
            )
        ;
    }

    protected function execute(InputInterface $input, OutputInterface $output)
    {
        $month = (int)$input->getArgument('month');
        $total = (int)$input->getArgument('total');
        $year =  (int)$input->getOption('year');

        $gen = new Ttg($year, $month, $total, $input->getOption('locale'));
        $gen->setMaxPerDay((int)$input->getOption('max'));

        $output->writeln($gen->generate());
    }
}
