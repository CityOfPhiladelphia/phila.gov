var fbElement = document.getElementById('fb-share');

if (fbElement) {
  fbElement.onclick = function() {
    FB.ui({
      method: 'share',
      display: 'popup',
      mobile_iframe: true,
      href: window.location.href,
    }, function(response){
    });
  }
}
