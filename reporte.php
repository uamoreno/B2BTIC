<?php
require_once 'config.php';

$mysqli = new mysqli(MYSQL_HOST, MYSQL_USER, MYSQL_PASS, MYSQL_DB);
if ($mysqli->connect_error) {
  die('Error de Conexión (' . $mysqli->connect_errno . ') '.$mysqli->connect_error.' Revise los parametros en config.php');
}
if($_GET['reporte']==1){
  $sql="SELECT archivo.*,extension.extension FROM b2b.archivo,b2b.extension where archivo.id=extension.id";
  $xslt_file="xsl/reporte1.xsl";
}
if($_GET['reporte']==2){
  $sql="SELECT count(extension) cantidad,extension FROM b2b.extension GROUP BY extension";
  $xslt_file="xsl/reporte2.xsl";
}
$resultado=$mysqli->query($sql);

header("Content-type: text/xml");
$XML = "<?xml version=\"1.0\"?>\n";
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
