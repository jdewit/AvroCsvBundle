<?php
namespace Avro\CsvBundle\DependencyInjection;

use Symfony\Component\HttpKernel\DependencyInjection\Extension;
use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\DependencyInjection\Loader\YamlFileLoader;
use Symfony\Component\Config\FileLocator;
use Symfony\Component\Config\Definition\Processor;
use Symfony\Component\DependencyInjection\Alias;

/*
 * Bundle DIC Extension
 *
 * @author Joris de Wit <joris.w.dewit@gmail.com>
 */
class AvroCsvExtension extends Extension
{
    public function load(array $configs, ContainerBuilder $container)
    {
        $processor = new Processor();
        $configuration = new Configuration();

        $config = $processor->processConfiguration($configuration, $configs);
        $loader = new YamlFileLoader($container, new FileLocator(__DIR__.'/../Resources/config'));
        $loader->load('services.yml');

        $container->setParameter('avro_csv.batch_size', $config['batch_size']);
        $container->setParameter('avro_csv.use_owner', $config['use_owner']);
        $container->setParameter('avro_csv.use_legacy_id', $config['use_legacy_id']);
        $container->setParameter('avro_csv.sample_count', $config['sample_count']);
        $container->setParameter('avro_csv.tmp_upload_dir', $config['tmp_upload_dir']);

        foreach($config['objects'] as $k => $v) {
            $container->setParameter(sprintf('avro_csv.objects.%s.class', $k), $v['class']);
            $container->setParameter(sprintf('avro_csv.objects.%s.redirect_route', $v), $k['redirect_route']);
        }
    }
}
