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
        $queueDefinition->replaceArgument(3, $config['bounce_checking']);
        $queueDefinition->replaceArgument(4, $config['max_retries']);
        $queueDefinition->replaceArgument(5, $config['send_limit']);
        $queueDefinition->replaceArgument(6, $config['message_logging']);
        $queueDefinition->replaceArgument(7, $config['delete_sent_messages_after']);

        $substituterDefinition = $container->getDefinition( 'hgabka_kunstmaan_email.param_substituter');
        $substituterDefinition->replaceArgument(2, $config['template_var_chars']);

        $mailerSubscriberDefinition = $container->getDefinition( 'hgabka_kunstmaan_email.mailer_subscriber');
        $mailerSubscriberDefinition->replaceArgument(1, $config['email_logging_strategy']);

        $redirectPluginDefinition = $container->getDefinition( 'hgabka_kunstmaan_email.redirect_plugin');
        $redirectPluginDefinition->addMethodCall('setRedirectConfig', [$config['redirect']]);

        $addHeadersPluginDefinition = $container->getDefinition( 'hgabka_kunstmaan_email.add_headers_plugin');
        $addHeadersPluginDefinition->addMethodCall('setConfig', [$config['add_headers']]);

        $addHeadersPluginDefinition = $container->getDefinition( 'hgabka_kunstmaan_email.add_recipients_plugin');
        $addHeadersPluginDefinition->addMethodCall('setConfig', [$config['add_recipients']]);

        $addReturnPathPluginDefinition = $container->getDefinition( 'hgabka_kunstmaan_email.add_return_path_plugin');
        $addReturnPathPluginDefinition->addMethodCall('setConfig', [$config['return_path']]);
    }
}
