<?php

use CReifenscheid\DbRector\Controller\SetupController;
use CReifenscheid\DbRector\Controller\TyposcriptController;
use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\DependencyInjection\Loader\Configurator\ContainerConfigurator;

return static function (ContainerConfigurator $containerConfigurator, ContainerBuilder $containerBuilder): void {
    $services = $containerConfigurator->services();
    $services->defaults()
        ->private()
        ->autowire()
        ->autoconfigure();

    $services->load('CReifenscheid\\DbRector\\', __DIR__ . '/../Classes/')->exclude([
        __DIR__ . '/../Classes/Domain/Model',
    ]);

    $services->set(TyposcriptController::class)
        ->tag('backend.controller');
    $services->set(SetupController::class)
        ->tag('backend.controller');
};
