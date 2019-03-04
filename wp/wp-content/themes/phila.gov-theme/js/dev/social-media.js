var fbElement = document.getElementById('fb-share');

if (fbElement) {
  fbElement.onclick = function() {
    FB.ui({
      app_id : '115304222529365',
      method: 'share',
      display: 'popup',
      mobile_iframe: true,
      href: window.location.href,
    }, function(response){
    });
  }
}
