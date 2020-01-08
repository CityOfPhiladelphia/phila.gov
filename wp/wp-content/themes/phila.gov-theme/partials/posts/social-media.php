<?php 
  /* Template for social media icon markup 
  *
  */

?>
<?php 
$tweet_intent = isset($tweet_intent) ? $tweet_intent : rwmb_meta('phila_social_intent'); 
$the_title =  isset($the_title) ? $the_title : get_the_title();
$email_title = urlencode(html_entity_decode($the_title));
?>
<script>
window.fbAsyncInit=function(){FB.init({appId:"115304222529365",xfbml:!0,version:"v2.10"}),FB.AppEvents.logPageView()},function(e,t,n){var r,o=e.getElementsByTagName(t)[0];e.getElementById(n)||((r=e.createElement(t)).id=n,r.src="//connect.facebook.net/en_US/sdk.js",o.parentNode.insertBefore(r,o))}(document,"script","facebook-jssdk"),window.twttr=function(e,t,n){var r,o=e.getElementsByTagName(t)[0],i=window.twttr||{};return e.getElementById(n)?i:(r=e.createElement(t),r.id=n,r.src="https://platform.twitter.com/widgets.js",o.parentNode.insertBefore(r,o),i._e=[],i.ready=function(e){i._e.push(e)},i)}(document,"script","twitter-wjs");
</script>
<div class="social-media">
  <a href="#" id="fb-share" data-analytics="social"><i class="fab fa-facebook fa-lg" aria-hidden="true"></i></a>
  <a href="https://twitter.com/intent/tweet?text=<?php echo ( $tweet_intent != '' ) ? phila_encode_title(rwmb_meta('phila_social_intent') ) :  phila_encode_title( $the_title );?>&url=<?php echo get_permalink()?>"><i class="fab fa-twitter fa-lg" aria-hidden="true"></i></a>
  <a href="mailto:?subject=<?php echo str_replace('+', '%20', $email_title) ?>&body=<?php echo get_permalink()?>" data-analytics="social"><i class="far fa-envelope fa-lg" aria-hidden="true"></i></a>
  <a href="javascript:window.print()" data-analytics="social"><i class="fal fa-print fa-lg" aria-hidden="true"></i></a>
</div>