<?xml version="1.0" encoding="UTF-8"?>
<xsl:stylesheet version="1.0" xmlns:xsl="http://www.w3.org/1999/XSL/Transform">

<xsl:template match="/result">
 <html>
   <head>
       <link href="vendor/bootstrap/css/bootstrap.min.css" rel="stylesheet" />
   </head>
   <body id="page-top">
     <!-- Navigation -->
     <section id="about">
       <div class="container">
         <div class="row">
           <div class="col-lg-8 mx-auto">
                 <h2>Reporte 1: Tablas cruzadas desde Reporte1.xsl</h2>
           </div>
         </div>
           <div class="row">
              <table class="table table-striped table-bordered table-hover table-sm ">
                <thead class="thead-dark">
                <tr><th>Id local</th><th>Id remoto</th><th>Archivo</th><th>Extension</th></tr>
              </thead>
              <tbody>
                <xsl:for-each select="row">
                  <tr><td><xsl:value-of select="id" /></td>
                      <td><xsl:value-of select="id_archivo" /></td>
                      <td><xsl:value-of select="nombre" /></td>
                      <td><xsl:value-of select="extension" /></td></tr>
                </xsl:for-each>
              </tbody>
              </table>
           </div>

         </div>
     </section>
     <!-- Footer -->
     <footer class="py-5 bg-dark">
       <div class="container">
         <p class="m-0 text-center text-white">Copyright  B2BTIC 2020</p>
       </div>
       <!-- /.container -->
     </footer>
     <!-- Bootstrap core JavaScript -->
    </body>
 </html>
</xsl:template>

</xsl:stylesheet>
