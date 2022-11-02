<html>
  <head>
    <h1>PHP and R Integration Sample</h1>
  </head>
  <body>
        <?php
      // Execute the R script within PHP code
      // Generates output as test.png image.
    
    exec('C:/MAMP/bin/R-4.0.3/bin/rscript.exe sample.r');

    //exec("C:\MAMP\bin\R-4.0.3\bin\rscript.exe sample.r");
    ?>
    <img src="test.png?var1.1" alt="R Graph">

  </body>
</html>