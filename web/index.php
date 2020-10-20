<?php

use Symfony\Component\HttpFoundation\Request;
date_default_timezone_set('America/Bogota');

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

	$temperature = $request->get('temperature');
	$tabla = $request->get('tabla');

	$dbconn = pg_pconnect("host=ec2-52-21-0-111.compute-1.amazonaws.com port=5432 dbname=da23ojrg1de3ae user=msmhlrvxhgltyv password=baf2024024b59cdd7b5bd1a44e8d8a7773810a5ccbce3719f01225c9baac9bf2");

	$data = array(
		"fecha"=>date('Y-m-d H:i:s'),
		"placeSense" => $request->get('lugar'),
		"temperature" => $temperature
		);

	$respuesta = pg_insert($dbconn, $tabla, $data);
   	
   	return $respuesta;
});

$app->post('/guardarConsumo', function (Request $request) use ($app) {

	$corriente = $request->get('corriente');
	$voltaje = $request->get('voltaje');
	$tabla = $request->get('tabla');
	$lugar = $request->get('lugar');

	$dbconn = pg_pconnect("host=ec2-52-21-0-111.compute-1.amazonaws.com port=5432 dbname=da23ojrg1de3ae user=msmhlrvxhgltyv password=baf2024024b59cdd7b5bd1a44e8d8a7773810a5ccbce3719f01225c9baac9bf2");

	$data = array(
		"fecha"=>date('Y-m-d H:i:s'),
		"corriente" => $corriente,
		"voltaje" => $voltaje,
		"lugar" => $lugar
		);

	$respuesta = pg_insert($dbconn, $tabla, $data);
   	
   	return $respuesta;
});

//Ruta de demostración, se recibe(n) dato(s) y se manipulan
$app->get('/consultarDatos', function () use ($app) {

	$dbconn = pg_pconnect("host=ec2-52-21-0-111.compute-1.amazonaws.com port=5432 dbname=da23ojrg1de3ae user=msmhlrvxhgltyv password=baf2024024b59cdd7b5bd1a44e8d8a7773810a5ccbce3719f01225c9baac9bf2");

	$consulta = pg_query($dbconn, "SELECT * FROM clima_house");

	print_r(pg_fetch_all($consulta));

	echo "<br><br>";

	print_r(pg_fetch_array($consulta, 3, PGSQL_NUM));

	echo "<br><br>";

	print_r(pg_fetch_array($consulta, 5, PGSQL_ASSOC));

	echo "<br><br>";
	
	return "OK";
});

//Ruta de demostración, se recibe(n) dato(s) y se manipulan
$app->post('/postArduino', function (Request $request) use ($app) {
   	return "OK";
});

$app->run();
