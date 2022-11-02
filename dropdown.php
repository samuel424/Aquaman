<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Document</title>
    <link rel="stylesheet" type="text/css" href="css/main.css?ts=<?=time()?>">
</head>
<body>

  <div class="dropdown">
  <button onclick="holdDrop('Account')" class="drop">Account</button>
  <div id='Account' class="droplist">
    <a href='accountSettings.php'>Account</a>
    <a href='loggingout.php'>Log Out</a>
  </div>
  </div>
  
  <div class="dropdown">
  <button onclick="holdDrop('Laboratory')" class="drop">Laboratory</button>
  <div id='Laboratory' class="droplist">
    <a href='form_Laboratory.php'>New Laboratory</a>
    <a href=''>Freezer</a>
    <a href=''>Register qPCR machine</a>
  </div>
  </div>
  
  <div class="dropdown">
  <button onclick="holdDrop('Target')" class="drop">Target species</button>
  <div id='Target' class="droplist">
    <a href='form_species.php'>New Species</a>
    <a href='form_gene.php'>New Gene</a>
    <a href='form_probe.php'>New Probe</a>
  </div>
  </div>
  
  <div class="dropdown">
  <button onclick="holdDrop('Location')" class="drop">Location</button>
  <div id='Location' class="droplist">
    <a href="form_location.php">Link 1</a>
    <a href='form_noise.php'>Report Noise</a>  
  </div>
  </div>

  <div class="dropdown">
  <button onclick="holdDrop('Expedition')" class="drop">Expedition</button>
  <div id='Expedition' class="droplist">
    <a href='form_catchmethod.php'>New Capture Method</a>
    <a href='form_rawmaterial.php'>Report Sampling</a>
    <a href='form_fish.php'>Report Fish</a> 
  </div>
  </div>
  
  <div class="dropdown">
  <button onclick="holdDrop('Labwork')" class="drop">Labwork</button>
  <div id='Labwork' class="droplist">
    <a href='form_RNA.php'>Report RNA</a>
    <a href='form_sample.php'>Report Disection</a>
    <a href='qpcr.php'>Report qPCR run</a>
  </div>
  </div>
  

  <script>
    /* When the user clicks on the button, 
    toggle between hiding and showing the dropdown content */
    function holdDrop(x) {
      document.getElementById(x).classList.toggle("show");
    }

  // Close the dropdown if the user clicks outside of it
  window.onclick = function(event) {
    if (!event.target.matches('.drop')) {
      var dropdowns = document.getElementsByClassName("droplist");
      var i;
      for (i = 0; i < dropdowns.length; i++) {
        var openDropdown = dropdowns[i];
        if (openDropdown.classList.contains('show')) {
          openDropdown.classList.remove('show');
        }
      }
    }
  }
  </script>

</body>
</html>