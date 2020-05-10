<?php
require_once 'config.php';
require_once 'vendor/autoload.php';

$loader = new \Twig\Loader\FilesystemLoader('templates');
$twig = new \Twig\Environment($loader);

$consumed=false;
$FechaInicial="";
$documentos=null;
$conteo=0;

if(isset($_POST)){
  if(isset($_POST['FechaInicial'])){
    $consumed=true;
    $FechaInicial=$_POST['FechaInicial'].=" 00:00:00";
    $client = new SoapClient('http://test.analitica.com.co/AZDigital_Pruebas/WebServices/ServiciosAZDigital.wsdl');
    $client->__setLocation('http://test.analitica.com.co/AZDigital_Pruebas/WebServices/SOAP/index.php');
    $documentos=$client->BuscarArchivo(array('Condiciones'=>array('Condicion'=>array('Tipo'=>'FechaInicial','Expresion'=>"$FechaInicial"))));
    $_SESSION['DOCUMENTOS']=$documentos;
    $_SESSION['FechaInicial']=$FechaInicial;
  }
  if(isset($_POST['Grabar'])){
    $mysqli = new mysqli(MYSQL_HOST, MYSQL_USER, MYSQL_PASS, MYSQL_DB);
    if ($mysqli->connect_error) {
      die('Error de Conexión (' . $mysqli->connect_errno . ') '.$mysqli->connect_error.' Revise los parametros en config.php');
    }

    $mysqli->query("truncate table archivo");
    $mysqli->query("truncate table extension");

    $extension="";
    $nombre="";
    $cont=1;
    $objs=(is_array($_SESSION['DOCUMENTOS']->Archivo)?$_SESSION['DOCUMENTOS']->Archivo:$_SESSION['DOCUMENTOS']);
    $ok=true;
    $mysqli->begin_transaction();
    foreach($objs as $archi){
      $tmp = explode(".", $archi->Nombre);
      if(count($tmp)>1){
        $extension=$mysqli->real_escape_string(end($tmp));
        $nombre=$mysqli->real_escape_string(substr(str_replace($extension,'',$archi->Nombre),0,-1));
      }else{
        $extension="";
        $nombre=$archi->Nombre;
      }
      $sql1="INSERT INTO archivo(id,id_archivo,nombre) VALUES ($cont,{$archi->Id},'$nombre')";
      $sql2="INSERT INTO extension(id,extension) VALUES ($cont,'$extension')";
      $mysqli->query($sql1);
      $mysqli->query($sql2);
      $cont++;
    }
    $mysqli->commit();
    $_SESSION['count']=$cont;
    header('Location: reports.php');
    exit;
  }
}else{
  unset($_SESSION);
}

echo $twig->render('index.html',
  ['app_name' => 'B2BTIC :: Soap Consumption',
   'consumed'=>$consumed,
   'FechaInicial'=>$FechaInicial,
   'documentos'=>$documentos->Archivo]);
