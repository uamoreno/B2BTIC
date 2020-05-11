<?php
/*
@author: Uriel Alejandro Moreno
@email: uamoreno@hotmail.//
@date: 11-05-2020
Este script permite la construcción de un documento XML, al que se le aplica una transformación
dependiendo del tipo de reporte, las transformaciones se encuentran en la carpeta xsl del aplicativo
*/

// Llamado de librerias necesarias
require_once 'config.php';

//Objeto de conexión a la base de datos
$mysqli = new mysqli(MYSQL_HOST, MYSQL_USER, MYSQL_PASS, MYSQL_DB);
if ($mysqli->connect_error) {
  die('Error de Conexión (' . $mysqli->connect_errno . ') '.$mysqli->connect_error.' Revise los parametros en config.php');
}
// Dependiendo del reporte se lee una de las dos plantillas xls configuradas.
if($_GET['reporte']==1){
  $sql="SELECT archivo.*,extension.extension FROM b2b.archivo,b2b.extension where archivo.id=extension.id";
  $xslt_file="xsl/reporte1.xsl";
}
if($_GET['reporte']==2){
  $sql="SELECT count(extension) cantidad,extension FROM b2b.extension GROUP BY extension ORDER BY 1";
  $xslt_file="xsl/reporte2.xsl";
}
$resultado=$mysqli->query($sql);

//Se modifica la cabecera enviada al navegador para que construya un documento XML en lugar de un HTML
header("Content-type: text/xml");
$XML = "<?xml version=\"1.0\"?>\n";

//Llamado para aplicar los estilos de la transformación XSLT
if ($xslt_file) $XML .= "<?xml-stylesheet href=\"$xslt_file\" type=\"text/xsl\" ?>";

// root node
$XML .= "<result>\n";
// rows
while ($row = mysqli_fetch_assoc($resultado)) {
  $XML .= "\t<row>\n";
  $i = 0;
  // cells
  foreach ($row as $key=>$cell) {
    // Escaping illegal characters - not tested actually ;)
    $cell = str_replace("&", "&amp;", $cell);
    $cell = str_replace("<", "&lt;", $cell);
    $cell = str_replace(">", "&gt;", $cell);
    $cell = str_replace("\"", "&quot;", $cell);
    $col_name = $key;
    // creates the "<tag>contents</tag>" representing the column
    $XML .= "\t\t<" . $col_name . ">" . $cell . "</" . $col_name . ">\n";
    $i++;
  }
  $XML .= "\t</row>\n";
 }
$XML .= "</result>\n";

// output the whole XML string
echo $XML;
?>
