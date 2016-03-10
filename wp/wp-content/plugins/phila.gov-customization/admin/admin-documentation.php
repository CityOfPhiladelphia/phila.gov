<?php

add_action('admin_menu', 'phila_documentation_menu');

function phila_documentation_menu() {
  add_object_page( 'Alpha Docs', 'Alpha Docs', 'read', 'alpha-docs', 'phila_documentation_content', 'dashicons-book-alt' );
}

function phila_documentation_content(){
	?>
	<div class="wrap">
		<h2>alpha.phila.gov Documentation</h2>
	</div>
  <section id="intro">
    <p>The following documentation is intended to address workflows specific to alpha.phila.gov. If you don't see a topic listed, assume that the workflow is unchanged from that of a standard WordPress implementation and consult the original source of documentation.</p>
  </section>
  <nav>
    <span>Topics: <a href="#mailchimp">MailChimp</a></span>
  <nav>
  <hr>
  <section id="mailchimp">
    <h2>MailChimp</h2>
    <h3>Adding a new MailChimp form to Alpha</h3>
      <ol class="new-mailchimp-form">
        <li>
          Create "naked" embeded form in mailchimp
        </li>
        <li>
          Remove extraneous spaces from markup
        </li>
        <li>
          Ensure the form is wrapped in <code>&lt;div id="mc_embed_signup"&gt;</code> and the form element has an ID of <code>mc-embedded-subscribe-form</code>
        </li>
        <li>
          Update form action from <code>subscribe/post</code> to <code>subscribe/post-json</code>
        </li>
      </ol>
  <section>
	<?php
}
?>
