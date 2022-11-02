<?php
// poorman.php
 
echo "<form action='map.php' method='get'>";
echo "X: <input type='text' name='X' />";
echo "Y: <input type='text' name='Y' />";
echo "<input type='submit' />";
echo "</form>";
 
if(isset($_GET['X']) and isset($_GET['Y']))
{
  unlink('map.png');
  $X = $_GET['X'];
  $Y = $_GET['Y'];
 
  // execute R script from shell
  // this will save a plot at temp.png to the filesystem
  exec('C:/MAMP/bin/R-4.0.3/bin/rscript.exe map.r '.$X.' '.$Y);
 
  // return image tag
  echo("<img src='map.png' />");
}
?>