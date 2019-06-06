<?php

/*
 * This file is part of the PLEGRO application.
 *
 * (c) AS-Trainer <http://www.as-trainer.com/>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Avro\CsvBundle\Tests\DependencyInjection;

use Avro\CsvBundle\DependencyInjection\Configuration;
use Symfony\Component\Config\Definition\Builder\TreeBuilder;

/**
 * @author Steffen Roßkamp <steffen.rosskamp@gimmickmedia.de>
 */
class ConfigurationTest extends \PHPUnit_Framework_TestCase
{
    public function testGetConfigTreeBuilder()
    {
        $config = new Configuration();
        $this->assertInstanceOf(TreeBuilder::class, $config->getConfigTreeBuilder());
    }
}
