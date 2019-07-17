<?php

/*
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Avro\CsvBundle\Tests\DependencyInjection;

use Avro\CsvBundle\DependencyInjection\Configuration;
use PHPUnit\Framework\TestCase;
use Symfony\Component\Config\Definition\Builder\TreeBuilder;

/**
 * @author Steffen Roßkamp <steffen.rosskamp@gimmickmedia.de>
 */
class ConfigurationTest extends TestCase
{
    public function testGetConfigTreeBuilder(): void
    {
        $config = new Configuration();
        $this->assertInstanceOf(TreeBuilder::class, $config->getConfigTreeBuilder());
    }
}
