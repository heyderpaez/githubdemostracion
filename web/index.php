<?php

use Symfony\Component\HttpFoundation\Request;

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

$app->get('/', function() use($app) {
  $app['monolog']->addDebug('logging output.');
  return $app['twig']->render('index.twig');
});


//Ruta de demostración, para validar que se recibe(n) dato(s) y se responde con este mismo
$app->post('/enviarDato', function (Request $request) use ($app) {
   return $request;
});


//Ruta de demostración, se recibe(n) dato(s) y se manipulan
$app->post('/guardarDato', function (Request $request) use ($app) {

	$dbconn = pg_pconnect("host=ec2-52-21-0-111.compute-1.amazonaws.com port=5432 dbname=da23ojrg1de3ae user=msmhlrvxhgltyv password=af2024024b59cdd7b5bd1a44e8d8a7773810a5ccbce3719f01225c9baac9bf2");

	if($dbconn){
		return "Me conecté";
	}
	else{
		return "Conexión Fallida";
	}

   	$nombre = $request->get('nombre');
	$respuesta = "Hola " .$nombre;
   	return $respuesta;
});

//Ruta de demostración, se recibe(n) dato(s) y se manipulan
$app->post('/postArduino', function (Request $request) use ($app) {
   	return "OK";
});

$app->run();
