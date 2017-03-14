<?php
/**
 * Copyright 2016 University of Liverpool
 *
 * Licensed under the Apache License, Version 2.0 (the "License");
 * you may not use this file except in compliance with the License.
 * You may obtain a copy of the License at
 *
 * http://www.apache.org/licenses/LICENSE-2.0
 *
 * Unless required by applicable law or agreed to in writing, software
 * distributed under the License is distributed on an "AS IS" BASIS,
 * WITHOUT WARRANTIES OR CONDITIONS OF ANY KIND, either express or implied.
 * See the License for the specific language governing permissions and
 * limitations under the License.
 */
namespace pgb_liv\mascot_monitor\Test\Unit;

use pgb_liv\mascot_monitor\MascotMonitor;
use pgb_liv\php_ms\Search\MascotSearch;

class MascotMonitorTest extends \PHPUnit_Framework_TestCase
{

    /**
     * @covers pgb_liv\mascot_monitor\MascotMonitor::__construct
     *
     * @uses pgb_liv\mascot_monitor\MascotMonitor
     */
    public function testObjectCanBeConstructedForValidConstructorArguments()
    {
        $mascot = new MascotSearch('127.0.0.1', 80, '/mascot');
        $monitor = new MascotMonitor($mascot);
    }
}
