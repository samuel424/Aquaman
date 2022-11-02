/* If click */
function holdDrop(x) {
  document.getElementById(x).classList.toggle('show');
}

// Close dropdown
window.onclick = function(event) {
    if (!event.target.matches('.drop')) {
        var dropdowns = document.getElementsByClassName('droplist');
        var i;
        for (i = 0; i < dropdowns.length; i++) {
            var openDropdown = dropdowns[i];
            if (openDropdown.classList.contains('show')) {
            openDropdown.classList.remove('show');
            }
        }
    }
}
