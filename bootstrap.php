<?php

    /**
     * @author Michal Doubek <michal@doubkovi.cz>
     * @license LGPL 3
     *
     * This code is distributed under LGPL license version 3
     * For full license information view LICENSE file which is
     * Distributed with this source code
     *
     * This source code is part of Phlamingo project
     */
    $loader = require __DIR__.'/vendor/autoload.php';
    require __DIR__.'/Application.php';

    define('APP', __DIR__.'/app');
    define('PHLAMINGO', __DIR__.'/phlamingo');
    define('TEMP', PHLAMINGO.'/temp');
    define('DOMAIN', $_SERVER['HTTP_HOST']);

    // clear the Cache in development mode...
    \Phlamingo\Cache\Cache::clearCache();

    // Create instance of Application
    $application = new Application();

    // Setup DI
    $application->setupDI(\Phlamingo\Di\ContainerSingleton::getContainer());

    // Configurate application
    $config = $application->abstractConfig();
    $config = $application->config($config);

    // Run application
    $application->callMain(\Phlamingo\Di\ContainerSingleton::getContainer()->get('Router'));
