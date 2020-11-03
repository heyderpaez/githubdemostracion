<?php

use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;

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


$app->post('/guardarDato', function (Request $request) use ($app) {

	$tabla = $request->get('tabla');

	if ($tabla == "consumo"){
		$corriente = $request->get('corriente');
		$voltaje = $request->get('voltaje');
		$lugar = $request->get('lugar');
		$data = array(
			"fecha"=>date('Y-m-d H:i:s'),
			"corriente" => $corriente,
			"voltaje" => $voltaje,
			"lugar" => $lugar
		);
	}
	else if ($tabla == "clima_house"){
		$temperature = $request->get('temperatura');
		$humidity = $request->get('humedad');
		$placeSense = $request->get('lugar');
		$data = array(
			"fecha"=>date('Y-m-d H:i:s'),
			"placeSense" => $placeSense,
			"temperature" => $temperature,
			"humidity" => $humidity
		);
	}
	else{
		return "Tabla no válida";
	}


	$dbconn = pg_pconnect("host=ec2-52-21-0-111.compute-1.amazonaws.com port=5432 dbname=da23ojrg1de3ae user=msmhlrvxhgltyv password=baf2024024b59cdd7b5bd1a44e8d8a7773810a5ccbce3719f01225c9baac9bf2");
	$respuesta = pg_insert($dbconn, $tabla, $data);

	echo $respuesta; echo "<br><br>";

   	return "OK";
});

//Ruta de demostración, se recibe(n) dato(s) y se manipulan
$app->get('/consultarDatos', function () use ($app) {

	$dbconn = pg_pconnect("host=ec2-52-21-0-111.compute-1.amazonaws.com port=5432 dbname=da23ojrg1de3ae user=msmhlrvxhgltyv password=baf2024024b59cdd7b5bd1a44e8d8a7773810a5ccbce3719f01225c9baac9bf2");
	$query = "SELECT * FROM climahouse ORDER BY id DESC LIMIT 15";

	$consulta = pg_query($dbconn, $query);

	// print_r(pg_fetch_all($consulta));

	// echo "<br><br>";

	// print_r(pg_fetch_array($consulta, 3, PGSQL_NUM));

	// echo "<br><br>";

	// $cons_array = pg_fetch_array($consulta, 5, PGSQL_ASSOC); 
	// print_r($cons_array);
	// echo $cons_array[fecha];

	// echo "<br><br>";

	// $cons_object = pg_fetch_object($consulta);
	// print_r($cons_object);
	// echo $cons_object->fecha;

	// echo "<br><br>";
	
	// echo pg_fetch_result($consulta, null, 3);

	// echo "<br><br> Ahora haremos un cambio de formato a JSON <br><br>";

	$resultArray = array();
  	while ($row = pg_fetch_array($consulta, null, PGSQL_ASSOC)) {
    	$resultArray[] = $row;
  	}

  	// print_r($resultArray);

  	// echo "<br><br>";

  $jsonResult = json_encode($resultArray, JSON_PRETTY_PRINT | JSON_FORCE_OBJECT);

  $response = new Response();
  $response->setContent($jsonResult);
  $response->setCharset('UTF-8');
  $response->headers->set('Content-Type', 'application/json');

  return $response;
});

//Ruta de demostración, se recibe(n) dato(s) y se manipulan
$app->get('/getDataGoogle', function () use ($app) {

	$dbconn = pg_pconnect("host=ec2-52-21-0-111.compute-1.amazonaws.com port=5432 dbname=da23ojrg1de3ae user=msmhlrvxhgltyv password=baf2024024b59cdd7b5bd1a44e8d8a7773810a5ccbce3719f01225c9baac9bf2");
	$query = 'SELECT "fecha", "corriente" FROM climahouse ORDER BY id DESC LIMIT 15';

	$consulta = pg_query($dbconn, $query);

	$table = array();
	$table['cols'] = array(
		array('id' => 'fecha', 'label' => 'FECHA', 'type' => 'datetime'),
		array('id' => 'corriente', 'label' => 'RPM', 'type' => 'number')
	);

	$rows = array();

	while($r = pg_fetch_assoc($consulta)) {
		echo $r;
		echo "<br><br>";
    $temp = array();
    $fecha_temp = strtotime($r['fecha']);
    $fecha_temp = $fecha_temp * 1000;
    // each column needs to have data inserted via the $temp array
    $temp[] = array('v' => 'Date('.$fecha_temp.')'); 
    $temp[] = array('v' => $r['corriente']);
    // etc...

    // insert the temp array into $rows
    $rows[] = array('c' => $temp); 
  	}

	// populate the table with rows of data
	  $table['rows'] = $rows;

	// encode the table as JSON
	  $jsonTable = json_encode($table, JSON_PRETTY_PRINT);

   // return $app['twig']->render('index.twig', array(
   //      'json_rows' => $table['rows'],
   //      'json_cols' => $table['cols'],
   //      'consumos' => htmlspecialchars_decode($jsonTable, ENT_QUOTES),
   //  ));

  //$jsonResult = json_encode($resultArray, JSON_PRETTY_PRINT | JSON_FORCE_OBJECT);

  // $response = new Response();
  // $response->setContent($jsonTable);
  // $response->setCharset('UTF-8');
  // $response->headers->set('Content-Type', 'application/json');

  return htmlspecialchars_decode($jsonTable, ENT_QUOTES);
});


$app->post('/limpiarDatos', function (Request $request) use ($app) {

	$tabla = $request->get('tabla');

	$dbconn = pg_pconnect("host=ec2-52-21-0-111.compute-1.amazonaws.com port=5432 dbname=da23ojrg1de3ae user=msmhlrvxhgltyv password=baf2024024b59cdd7b5bd1a44e8d8a7773810a5ccbce3719f01225c9baac9bf2");
	
	$query_last = "SELECT * FROM " .$tabla. " ORDER BY id DESC LIMIT 1";
	$query_first = "SELECT * FROM " .$tabla. " ORDER BY id ASC LIMIT 1";

	$consulta_last = pg_query($dbconn, $query_last);
	$consulta_first = pg_query($dbconn, $query_first);

	$id_last = pg_fetch_result($consulta_last, null, 0);
	$id_first = pg_fetch_result($consulta_first, null, 0);

	$registros = $id_last - $id_first + 1;

	if($registros >= 50){
		$id_borrar = $id_last - 30;
		$query_delete = "DELETE FROM " . $tabla . " WHERE id<=" .$id_borrar.";";
		$consulta_delete = pg_query($dbconn, $query_delete);
		return "Se borraron los registros";
	}
	else{
		return "No se borraron los registros";
	}
});

//Ruta de demostración, se recibe(n) dato(s) y se manipulan
$app->post('/postArduino', function (Request $request) use ($app) {
   	return "OK";
});

$app->run();
