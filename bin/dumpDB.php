<?php

$dbconn = pg_pconnect("host=ec2-52-21-0-111.compute-1.amazonaws.com port=5432 dbname=da23ojrg1de3ae user=msmhlrvxhgltyv password=baf2024024b59cdd7b5bd1a44e8d8a7773810a5ccbce3719f01225c9baac9bf2");
	
$query_last = "SELECT * FROM consumo ORDER BY id DESC LIMIT 1";
$query_first = "SELECT * FROM consumo ORDER BY id ASC LIMIT 1";

$consulta_last = pg_query($dbconn, $query_last);
$consulta_first = pg_query($dbconn, $query_first);

$id_last = pg_fetch_result($consulta_last, null, 0);
$id_first = pg_fetch_result($consulta_first, null, 0);

$registros = $id_last - $id_first + 1;

	if($registros >= 50){
		$id_borrar = $id_last - 30;
		$query_delete = "DELETE FROM consumo WHERE id<=" .$id_borrar.";";
		$consulta_delete = pg_query($dbconn, $query_delete);
	}
});

$query_last = "SELECT * FROM clima_house ORDER BY id DESC LIMIT 1";
$query_first = "SELECT * FROM clima_house ORDER BY id ASC LIMIT 1";

$consulta_last = pg_query($dbconn, $query_last);
$consulta_first = pg_query($dbconn, $query_first);

$id_last = pg_fetch_result($consulta_last, null, 0);
$id_first = pg_fetch_result($consulta_first, null, 0);

$registros = $id_last - $id_first + 1;

	if($registros >= 50){
		$id_borrar = $id_last - 30;
		$query_delete = "DELETE FROM clima_house WHERE id<=" .$id_borrar.";";
		$consulta_delete = pg_query($dbconn, $query_delete);
	}
});


?>