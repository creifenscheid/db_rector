<?php

use CReifenscheid\DbRector\Controller\RectorController;
use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\DependencyInjection\Loader\Configurator\ContainerConfigurator;

return static function (ContainerConfigurator $containerConfigurator, ContainerBuilder $containerBuilder): void {
    $services = $containerConfigurator->services();
    $services->defaults()
        ->private()
        ->autowire()
        ->autoconfigure();

    $services->load('CReifenscheid\\DbRector\\', __DIR__ . '/../Classes/');

    $services->set(RectorController::class)
        ->tag('backend.controller');
};
