
<?php
// poorman.php
 
echo "<form action='poorman.php' method='get'>";
echo "Number values to generate: <input type='text' name='N' />";
echo "<input type='submit' />";
echo "</form>";
 
if(isset($_GET['N']))
{
  $N = $_GET['N'];
  echo "hello";
 
  // execute R script from shell
  // this will save a plot at temp.png to the filesystem
  exec('D:/wamp64/bin/R-4.1.1/bin/rscript.exe sample2.r'. $N);
  echo 'hello2';
 
  // return image tag
  echo("<img src='temp.png' />");
}
?>