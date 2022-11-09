<?php

add_action('admin_menu', 'phila_resource_hub_page');

function phila_resource_hub_page()
{
  add_menu_page('Resource hub', 'Resource hub', 'read', 'resource-hub', 'phila_resource_hub_content', 'dashicons-book-alt');
}

function phila_resource_hub_content()
{
?>
  <style>
  ul{
    list-style: disc;
    margin-left: 2em;
  }
  </style>
<h1 id="resource-hub">Resource hub</h1>
<p>The following resources may be useful as you create and manage content on phila.gov.  </p>
<h2 id="resources-for-site-editors">Resources for site editors</h2>
<p><a href="https://standards.phila.gov/">Digital standards</a> </p>
<p>The digital standards are guides for design, development, and content for City websites. </p>
<hr>
<p><a href="https://phila.sharepoint.com/:f:/s/Teams-OIT-PhilagovProjectTeam/EuFE222ofUBNi_XknL-R6WcBGAwpdXhlikRBTyZAXiEzKQ?e=vsRAtu">Training presentations, guidance, and tip sheets</a> </p>
<p>This is a collection of training presentations and other guidance produced by the Digital Services team. It also includes:  </p>
<ul>
<li><p>Documentation for various WordPress features. </p>
</li>
<li><p>Links to City stock photos and other imagery sources for blogs. </p>
</li>
<li><p>Links to useful tools and websites. </p>
</li>
</ul>
<hr>
<p><a href="https://us10.campaign-archive.com/home/?u=d8a1c28b2fe0bfca8576b5af0&amp;id=4e76664556">Newsletter archives</a> </p>
<p>The Digital Services team produces a newsletter where they share best practices and announce new features. By subscribing to this mailing list, you&#39;ll also be notified about the team&#39;s open office hours.  </p>
<hr>
<p><a href="https://github.com/CityOfPhiladelphia/phila.gov/releases">Phila.gov changelog</a> </p>
<p>This changelog tracks updates to the phila.gov platform and notes what has been added, changed, or fixed with each release. </p>
<h2 id="need-help-">Need help?</h2>
<p>If you have questions or need help with your website, contact the Digital Services team at websupport@phila.gov. </p>
  <?php
}
  ?>
