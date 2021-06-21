

jQuery( document ).ready( function( $ ) {

  var cssLink = document.createElement("link");
  cssLink.href = "https://localhost:19107/wp-includes/css/dashicons.min.css"; 
  cssLink.rel = "stylesheet"; 
  cssLink.type = "text/css"; 

  var cssLink1 = document.createElement("link");
  cssLink1.href = "https://localhost:19107/wp-includes/js/tinymce/skins/wordpress/wp-content.css"; 
  cssLink1.rel = "stylesheet"; 
  cssLink1.type = "text/css"; 

  // var cssLink2 = document.createElement("link");
  // cssLink2.href = "https://localhost:19107/wp-includes/js/tinymce/skins/lightgray/content.min.css"; 
  // cssLink2.rel = "stylesheet"; 
  // cssLink2.type = "text/css"; 


  frames['module_row_1_col_1_module_row_1_col_1_options_phila_module_row_1_col_1_textarea_ifr'].contentDocument.head.appendChild(cssLink);
  frames['module_row_1_col_1_module_row_1_col_1_options_phila_module_row_1_col_1_textarea_ifr'].contentDocument.head.appendChild(cssLink1);
  // frames['module_row_1_col_1_module_row_1_col_1_options_phila_module_row_1_col_1_textarea_ifr'].contentDocument.head.appendChild(cssLink2);

  frames['phila_row_phila_full_options_faq_accordion_group_phila_custom_wysiwyg_phila_wysiwyg_title_ifr'].contentDocument.head.appendChild(cssLink);
  frames['phila_row_phila_full_options_faq_accordion_group_phila_custom_wysiwyg_phila_wysiwyg_title_ifr'].contentDocument.head.appendChild(cssLink1);
  // frames['phila_row_phila_full_options_faq_accordion_group_phila_custom_wysiwyg_phila_wysiwyg_title_ifr'].contentDocument.head.appendChild(cssLink2);

});
