<?php
require_once 'config.php';
require_once 'vendor/autoload.php';

$loader = new \Twig\Loader\FilesystemLoader('templates');
$twig = new \Twig\Environment($loader);

echo $twig->render('reports.html',
  ['app_name' => 'B2BTIC :: Soap Consumption',
   'FechaInicial'=>$_SESSION['FechaInicial'],
   'documentos'=>$_SESSION['DOCUMENTOS']->Archivo]);
