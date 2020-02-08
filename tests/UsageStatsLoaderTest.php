<?php

/**
 * @file plugins/generic/usageStats/tests/UsageStatsLoaderTest.php
 *
 * Copyright (c) 2013-2020 Simon Fraser University
 * Copyright (c) 2003-2020 John Willinsky
 * Distributed under the GNU GPL v3. For full terms see the file docs/COPYING.
 *
 * @class UsageStatsLoaderTest
 * @ingroup plugins_generic_usagestats_tests
 * @see UsageStatsLoader
 *
 * @brief Test class for the UsageStatsLoader class
 */

import('lib.pkp.tests.PKPTestCase');
import('plugins.generic.usageStats.UsageStatsLoader');

class UsageStatsLoaderTest extends PKPTestCase {

        //
        // Implementing protected template methods from PKPTestCase
        //
        /**
         * @see PKPTestCase::setUp()
         */
        protected function setUp() : void {
			parent::setUp();

			// Instantiate the class for testing.
			// disable the original constructor and mock the abstract getMetricType()
			$this->loader = $this->getMockBuilder('UsageStatsLoader')
			->setMethods(array('__construct', 'getMetricType'))
			->setConstructorArgs(array())
			->disableOriginalConstructor()
			->getMock();
        }

}
