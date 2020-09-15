<?php

require('../vendor/autoload.php');

$app = new Silex\Application();
$app['debug'] = true;

// Register the monolog logging service
$app->register(new Silex\Provider\MonologServiceProvider(), array(
  'monolog.logfile' => 'php://stderr',
));

// Register view rendering
$app->register(new Silex\Provider\TwigServiceProvider(), array(
    'twig.path' => __DIR__.'/views',
));

// Our web handlers

$app->get('/{dato}', function($dato) use($app) {
  $app['monolog']->addDebug('logging output.');
  return $app['twig']->render('index.twig', array(
        'dato' => $dato,
    ));
});


$app->post('/enviarDato', function (Request $request) use ($app) {
   $temperatura = $request->get('tempeHouse');
  	return $app['twig']->render('index.twig', array(
        'dato' => $temperatura,
    ));
});


$app->run();
