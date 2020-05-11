<?php
/*
@author: Uriel Alejandro Moreno
@email: uamoreno@hotmail.//
@date: 11-05-2020
Este script permite el llamado a un end-point mediante SOAP, también ejecuta el almacenamiento de N registros
en la base de datos configurada en el script config.php.
*/

// Llamado de librerias necesarias
// Confguración de la sesión de PHP y la base de datos
require_once 'config.php';
// Confguración de la plantillas Twig para la vista
require_once 'vendor/autoload.php';

//Inscricciones Twig para identificar la ruta de los templates
$loader = new \Twig\Loader\FilesystemLoader('templates');
$twig = new \Twig\Environment($loader);
//variables locales
$consumed=false;
$FechaInicial="";
$documentos=null;
$conteo=0;
$conDatos=false;
//conexion a as base de datos
$mysqli = new mysqli(MYSQL_HOST, MYSQL_USER, MYSQL_PASS, MYSQL_DB);
if ($mysqli->connect_error) {
  die('Error de Conexión (' . $mysqli->connect_errno . ') '.$mysqli->connect_error.' Revise los parametros en config.php');
}

//procesamiento de la peticiòn POST
if(isset($_POST)){
  // Primer momento: Consumo del servicio desde una fecha de corte
  //la información resultante es almacenada en la sesión para previa confirmación del usuario
  if(isset($_POST['FechaInicial'])){
    $consumed=true;
    $FechaInicial=$_POST['FechaInicial'].=" 00:00:00";
    $client = new SoapClient('http://test.analitica.com.co/AZDigital_Pruebas/WebServices/ServiciosAZDigital.wsdl');
    $client->__setLocation('http://test.analitica.com.co/AZDigital_Pruebas/WebServices/SOAP/index.php');
    $documentos=$client->BuscarArchivo(array('Condiciones'=>array('Condicion'=>array('Tipo'=>'FechaInicial','Expresion'=>"$FechaInicial"))));
    $_SESSION['DOCUMENTOS']=$documentos;
    $_SESSION['FechaInicial']=$FechaInicial;
  }
  //Segundo momento: Grabar la informacon contenida en la sesión, a dos tablas en la base de datos
  if(isset($_POST['Grabar'])){

    $mysqli->query("truncate table archivo");
    $mysqli->query("truncate table extension");

    $extension="";
    $nombre="";
    $cont=1;
    // Cuando se obtiene un unico docmento la estructura del resultado obtenido es diferente, por eso se debe
    // hacer la validación de la siguiente linea
    $objs=(is_array($_SESSION['DOCUMENTOS']->Archivo)?$_SESSION['DOCUMENTOS']->Archivo:$_SESSION['DOCUMENTOS']);
    $ok=true;
    $mysqli->begin_transaction();
    foreach($objs as $archi){
      //Se extrae la parte del archivo que contiene la extensión del mismo
      $tmp = explode(".", $archi->Nombre);
      if(count($tmp)>1){
        $extension=$mysqli->real_escape_string(end($tmp));
        $nombre=$mysqli->real_escape_string(substr(str_replace($extension,'',$archi->Nombre),0,-1));
      }else{
        //para documentos que no tienen extensión se ejecutan las lineas a continuación
        $extension="";
        $nombre=$archi->Nombre;
      }
      //DML lanzadas para registras los valores en la base de datos
      $sql1="INSERT INTO archivo(id,id_archivo,nombre) VALUES ($cont,{$archi->Id},'$nombre')";
      $sql2="INSERT INTO extension(id,extension) VALUES ($cont,'$extension')";
      $mysqli->query($sql1);
      $mysqli->query($sql2);
      $cont++;
    }
    $mysqli->commit();
    $_SESSION['count']=$cont;
    //Se presente el llamado a la secciòn de reportes
    header('Location: reports.php');
    exit;
  }
}else{
  unset($_SESSION);
}

$sql="SELECT ifnull(count(id),0) conteo FROM archivo";
$resultado=$mysqli->query($sql);
$row = mysqli_fetch_assoc($resultado);
if($row['conteo']>=1){
  $conDatos=true;
}

// mecanismo para renderizar las vistas html del aplicativo mediante Twig
echo $twig->render('index.html',
  ['app_name' => 'B2BTIC :: Soap Consumption',
   'consumed'=>$consumed,
   'FechaInicial'=>$FechaInicial,
   'conDatos'=>$conDatos,
   'documentos'=>(isset($documentos->Archivo)?$documentos->Archivo:null)]);
