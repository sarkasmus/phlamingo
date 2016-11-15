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

    $loader = require __DIR__ . "/vendor/autoload.php";
    require __DIR__ . "/Application.php";

    // Create instance of Application
    $application = new Application();

    // Setup DI
    $application->SetupDI(new \Phlamingo\Di\Container());

    // Configurate application
    $config = $application->AbstractConfig();
    $config = $application->Config($config);

    // Configurate router
    $router = $application->AbstractSetupRouter();
    $router = $application->SetupRouter($router);

    // Run application

    $application->CallMain($router);