<?php

/*
 * This file is part of the Symfony package.
 *
 * (c) Fabien Potencier <fabien.potencier@symfony-project.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Symfony\Bundle\SecurityBundle\DependencyInjection\Security\Factory;

use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\DependencyInjection\Reference;

/**
 * X509Factory creates services for X509 certificate authentication.
 *
 * @author Fabien Potencier <fabien.potencier@symfony-project.com>
 */
class X509Factory implements SecurityFactoryInterface
{
    public function create(ContainerBuilder $container, $id, $config, $userProvider, $defaultEntryPoint)
    {
        $provider = 'security.authentication.provider.pre_authenticated.'.$id;
        $container
            ->register($provider, '%security.authentication.provider.pre_authenticated.class%')
            ->setArguments(array(new Reference($userProvider), new Reference('security.account_checker'), $id))
            ->setPublic(false)
            ->addTag('security.authentication_provider')
        ;

        // listener
        $listenerId = 'security.authentication.listener.x509.'.$id;
        $listener = $container->setDefinition($listenerId, clone $container->getDefinition('security.authentication.listener.x509'));
        $arguments = $listener->getArguments();
        $arguments[2] = $id;
        $listener->setArguments($arguments);

        return array($provider, $listenerId, $defaultEntryPoint);
    }

    public function getPosition()
    {
        return 'pre_auth';
    }

    public function getKey()
    {
        return 'x509';
    }
}
