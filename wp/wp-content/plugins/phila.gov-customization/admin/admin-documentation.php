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
    <span>Help Topics: <a href="#mailchimp">MailChimp</a> <br>
      Guidelines: <a href="#file-names">File Naming Conventions</a> | <a href="#urls">Creating URLs</a></span>
  <nav>
  <hr>
  <section id="mailchimp">
    <h2>MailChimp</h2>
    <h3>Adding a new MailChimp form to Alpha</h3>
      <ol class="new-mailchimp-form">
        <li>
          Create "naked" embedded form in mailchimp
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
  <section id="file-names">
    <h2>File Naming Conventions</h2>
      <ul>
        <li>Use hyphens to separate words.</li>
        <li>Lowercase is better, because it’s easier to type and to remember.</li>
        <li>Use the right extension — PDFs should have .pdf at the end, JPGs should have .jpg at the end, etc.</li>
        <li>Avoid the use of special characters beyond the hyphen and period, unless absolutely necessary. Do not include spaces (use hyphens in their place).</li>
        <li>Avoid unnecessary abbreviations</li>
      </ul>

      <p>Shorter is best, but the content should be descriptive to the user. It’s better to have long descriptive filenames than short, obscure ones.</p>
      <p>If the owner of the file is important to the content, include that.
      </p>
      <blockquote>
        mayor-influental-women-collage.jpg<br>
        <strong>-NOT-</strong><br>
        womenmosaicfinal.jpg
      </blockquote>
      <p>If the file content is based on a date or time, include that information at the beginning.</p>
      <blockquote>
        2015-income-based-wage-tax-refund-petition-spanish.pdf<br>
        <strong>-NOT-</strong><br>
        incomebasedWAGETAXREFUNDpetition2015SP.pdf<br>
      </blockquote>
      <p><small>This content has been heavily influenced by <a href="https://pages.18f.gov/content-guide/urls-and-filenames/"> https://pages.18f.gov/content-guide/urls-and-filenames/</a></small></p>
  </section>
  <section id="urls">
    <h2>Creating URLs</h2>

      <ul>
        <li>URLs should be short, memorable, easy to type, and well-structured.</li>
        <li>Words in a url should be separated by a dash.</li>
        <li>Omit articles (a/an/the).</li>

      </ul>

      <p>WordPress administrators can modify urls after a page has been saved.</p>

      <p>When a page is created, WordpPress will automatically use dashes to separate words based on the title of the page when it was saved the first time. This is expected behavior.</p>

      WordPress generated URLs should be cleaned up to remove unnecesrary articles, as stated above.
      <blockquote>
        /documents/philadelphia-water-department-regulations/<br>
        <strong>-NOT-</strong><br>
        /documents/phillywaterregs/
      </blockquote>


  <p><small>This content has been heavily influenced by <a href="https://pages.18f.gov/content-guide/urls-and-filenames/"> https://pages.18f.gov/content-guide/urls-and-filenames/</a></small></p>
  </section>
	<?php
}
?>
