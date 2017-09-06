<?php

namespace Hgabka\KunstmaanEmailBundle\DependencyInjection;

use Symfony\Component\Config\FileLocator;
use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\DependencyInjection\Loader\XmlFileLoader;
use Symfony\Component\HttpKernel\DependencyInjection\Extension;
use Symfony\Component\DependencyInjection\Loader;

class HgabkaKunstmaanEmailExtension extends Extension
{
    /**
     * {@inheritdoc}
     */
    public function load(array $configs, ContainerBuilder $container)
    {
        $configuration = new Configuration($container);
        $config = $this->processConfiguration($configuration, $configs);

        $loader = new Loader\YamlFileLoader($container, new FileLocator(__DIR__ . '/../Resources/config'));
        $loader->load('services.yml');

        $builderDefinition = $container->getDefinition( 'hgabka_kunstmaan_email.mail_builder');
        $builderDefinition->addMethodCall('setConfig', [$config]);

        $senderDefinition = $container->getDefinition( 'hgabka_kunstmaan_email.message_sender');
        $senderDefinition->addMethodCall('setConfig', [$config]);

        $loggerDefinition = $container->getDefinition( 'hgabka_kunstmaan_email.message_logger');
        $loggerDefinition->replaceArgument(1, $config['log_path']);

        $queueDefinition = $container->getDefinition( 'hgabka_kunstmaan_email.queue_manager');
        $queueDefinition->replaceArgument(2, $config['bounce_checking']);
        $queueDefinition->replaceArgument(3, $config['max_retries']);

        $substituterDefinition = $container->getDefinition( 'hgabka_kunstmaan_email.param_substituter');
        $substituterDefinition->replaceArgument(2, $config['template_var_chars']);

        $subscriberDefinition = $container->getDefinition( 'hgabka_kunstmaan_email.mailer_subscriber');
        $subscriberDefinition->replaceArgument(1, $config['email_logging_strategy']);

        $loggerPluginDefinition = $container->getDefinition( 'hgabka_kunstmaan_email.mail_logger_plugin');
        $loggerPluginDefinition->replaceArgument(0, $config['use_email_logging']);
    }
}
