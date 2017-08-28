<?php
/*
 * Add social media scripts to the page for sharing purposes
*/
?>
<!-- facebook -->
<script>
  window.fbAsyncInit = function() {
    FB.init({
      appId      : '115304222529365',
      xfbml      : true,
      version    : 'v2.10'
    });
    FB.AppEvents.logPageView();
  };
  (function(d, s, id){
     var js, fjs = d.getElementsByTagName(s)[0];
     if (d.getElementById(id)) {return;}
     js = d.createElement(s); js.id = id;
     js.src = "//connect.facebook.net/en_US/sdk.js";
     fjs.parentNode.insertBefore(js, fjs);
   }(document, 'script', 'facebook-jssdk'));
</script>
<!-- /facebook -->
