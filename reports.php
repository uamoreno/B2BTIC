<?php
/*
@author: Uriel Alejandro Moreno
@email: uamoreno@hotmail.//
@date: 11-05-2020
Este script permite seleccionar entre los reportes disponibles para su posterior consulta por pantalla
*/

// Confguración de la sesión de PHP y la base de datos
require_once 'config.php';
// Confguración de la plantillas Twig para la vista
require_once 'vendor/autoload.php';

//Inscricciones Twig para identificar la ruta de los templates
$loader = new \Twig\Loader\FilesystemLoader('templates');
$twig = new \Twig\Environment($loader);

// mecanismo para renderizar las vistas html del aplicativo mediante Twig
echo $twig->render('reports.html',
  ['app_name' => 'B2BTIC :: Soap Consumption']);
