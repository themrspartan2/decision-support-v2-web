<script>
    $(document).ready(function() {
	    hideLoader();
    });
    function showLoader(){
        console.log("showing loader");
        var loader = document.getElementById("preloader");
        loader.style.display = 'block';
    }

    function hideLoader(){
        console.log("hiding loader");
        var loader = document.getElementById("preloader");
        loader.style.display = 'none';
    }
</script>

<!-- diamond loader -->
<svg width="200" height="200" viewBox="0 0 100 100" id="preloader">
  <polyline class="line-cornered stroke-still" points="0,0 100,0 100,100" stroke-width="10" fill="none"></polyline>
  <polyline class="line-cornered stroke-still" points="0,0 0,100 100,100" stroke-width="10" fill="none"></polyline>
  <polyline class="line-cornered stroke-animation" points="0,0 100,0 100,100" stroke-width="10" fill="none"></polyline>
  <polyline class="line-cornered stroke-animation" points="0,0 0,100 100,100" stroke-width="10" fill="none"></polyline>
</svg>

<!-- Concentric circles loader
<div id="preloader">
  <div id="loader"></div>
</div>
-->
