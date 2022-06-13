<?php

use Collegeplannerpro\InterviewReport\Controller;
use Collegeplannerpro\InterviewReport\Repository;

require __DIR__ . '/../vendor/autoload.php';

// instantiate and configure dependencies
$twigLoader = new \Twig\Loader\FilesystemLoader(__DIR__ . '/../templates');
$twig = new \Twig\Environment($twigLoader);
$twig->addExtension(new \Twig\Extra\Intl\IntlExtension());
$db = require __DIR__ . '/../dbConnection.php';
$repository = new Repository($db);

// set up dependency injection container
$container = new \DI\Container();
$container->set('twig', $twig);
$container->set('repository', $repository);

// instantiate Slim application
$app = \Slim\Factory\AppFactory::create(container: $container);
$app->addErrorMiddleware(displayErrorDetails: true, logErrors: true, logErrorDetails: true);

// define routes and handlers
$app->get('/', [Controller::class, 'home']);
$app->get('/reports/payments', [Controller::class, 'paymentsReport']);
$app->post('/mail/sendReminder', [Controller::class, 'sendReminder']);

$app->run();
