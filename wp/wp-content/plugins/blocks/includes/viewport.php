<?php

/* Add tools: viewport dimensions on the footer
================================================== */
if ( ! class_exists( 'spartan_viewport_size' ) ) {
  function spartan_viewport_sizes() {

    $content = '<p class="absol"><span id="width"></span> x <span id="height"><span></p>'. "\n";

    $content .= '<script type="text/javascript">'. "\n";

      $content .= '(function($){'. "\n";

        $content .= '$(window).on("load", function() {'. "\n";
          $content .= '   $("html").addClass("loaded")'. "\n";
        $content .= '});'. "\n";

        $content .= "\n";

        $content .= '$(document).ready(function() {'. "\n";

          $content .= ' showViewportSize();'. "\n";

            $content .= ' $(window).resize(function(e) {'. "\n";
            $content .= '   showViewportSize();'. "\n";
            $content .= ' });'. "\n";

            $content .= "\n";

            $content .= ' function showViewportSize() {'. "\n";
              $content .= '   var the_width = $(window).width();'. "\n";
              $content .= '   var the_height = $(window).height();'. "\n";
              $content .= '   $("#width").text(the_width);'. "\n";
              $content .= '   $("#height").text(the_height);'. "\n";
            $content .= ' }'. "\n";

        $content .= '});'. "\n";

      $content .= '})(jQuery)'. "\n";

    $content .= '</script>'. "\n";

    $content .= '<style id="tool" type="text/css">';
    $content .= '.absol{position:fixed;display:inline-block;font-size:12px; line-height:1.2em; font-family:Consolas, "Andale Mono", Courier, "Courier New", monospace;;margin:0;font-weight:normal;text-align:center;';
    $content .= 'color:#fff;bottom:0;right:0;padding:0.1rem 0.6rem;background-color:rgba(255,0,0,0.5);opacity:0;z-index:99999;}';
    $content .= '.loaded .absol{opacity:1;}';
    $content .= '</style>'. "\n";

    echo $content;

  }

}