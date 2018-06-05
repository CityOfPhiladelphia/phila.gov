<?php
/**
 * Phila.gov utility functions
 */

//this is used throughout the theme and is meant to be updated once the major switch happens
function phila_util_echo_website_url(){
  echo 'beta.phila.gov';
}

//this form is used throughout the theme and can be updated as needed
function phila_util_echo_tester_url(){
  echo '/sign-up-to-be-a-phila-gov-tester';
}

//spits out a nice version of the department category name
function phila_util_get_current_cat_name(){
  $category = get_the_category();
  foreach( $category as $cat){
    return $cat->name;
  }
}

//spits out a nice version of the department category slug
function phila_util_get_current_cat_slug(){
  $category = get_the_category();
  foreach( $category as $cat){
    return $cat->slug;
  }
}

/* Return post data for the furthest parent */

function phila_util_get_furthest_ancestor( $post ) {

  /* Get an array of Ancestors and Parents if they exist */
  $parents = get_post_ancestors( $post->ID );
  /* Get the top Level page->ID count base 1, array base 0 so -1 */
  $id = ($parents) ? $parents[count($parents)-1]: $post->ID;

  return $parent = get_post( $id );
}

function phila_util_month_format($date){
  if (strlen($date->format('F')) > 5){
     return 'M.';
  } else {
    return 'F';
  }
}

/**
 * Utility function to determine if selected template is v2 or not
**/

function phila_util_is_v2_template( $post_id = null ){
  $user_selected_template = phila_get_selected_template( $post_id );
  if( strpos( $user_selected_template, '_v2' ) === false ){
    return false;
  }else{
    return true;
  }
}

/* Use in the loop to get an array of current category IDs */
function phila_util_cat_ids(){
  $categories = get_the_category();
  $cat_ids = array();
  foreach ($categories as $category ){
    array_push($cat_ids, $category->cat_ID);
  }
  return $cat_ids;
}


function phila_util_is_array_empty($input){
   $result = true;

   if (is_array($input) && count($input) > 0)
   {
      foreach ($input as $v)
      {
         $result = $result && phila_util_is_array_empty($v);
      }
   }
   else
   {
      $result = empty($input);
   }

   return $result;
}

function phila_util_return_parsed_email( $email_address ){
  $parsed_email = explode('@', $email_address);
  $staff_email_parsed = '';

  if (count($parsed_email) === 2){
    $staff_email_parsed .=  $parsed_email[0];
    $staff_email_parsed .= "<wbr>@";
    $staff_email_parsed .= $parsed_email[1];
    $staff_email_parsed .= "</wbr>";
  }

  return $staff_email_parsed;

}

/* Used to determine if this item is a "post" --- in our case, this can mean legacy post types like "news", "phila_post" & "press_release" */
function phila_util_return_is_post( $current_post_type ){
  $possibilities = array(
    0 => 'phila_post',
    1 => 'news_post',
    2 => 'press_release',
    3 => 'post'
  );
  if ( in_array( $current_post_type, $possibilities) ) {
    return true;
  }else{
    return false;
  }
}

//thanks https://stackoverflow.com/questions/5305879/automatic-clean-and-seo-friendly-url-slugs/9535967#9535967
function phila_format_uri( $string, $separator = '-' ) {
    $accents_regex = '~&([a-z]{1,2})(?:acute|cedil|circ|grave|lig|orn|ring|slash|th|tilde|uml);~i';
    $special_cases = array( '&' => 'and', "'" => '');
    $string = mb_strtolower( trim( $string ), 'UTF-8' );
    $string = str_replace( array_keys($special_cases), array_values( $special_cases), $string );
    $string = preg_replace( $accents_regex, '$1', htmlentities( $string, ENT_QUOTES, 'UTF-8' ) );
    $string = preg_replace("/[^a-z0-9]/u", "$separator", $string);
    $string = preg_replace("/[$separator]+/u", "$separator", $string);
    return $string;
}

//thanks https://stackoverflow.com/questions/20025030/convert-all-types-of-smart-quotes-with-php
function phila_make_regular_quote($string){
  $chr_map = array(
     // Windows codepage 1252
     "\xC2\x82" => "'", // U+0082⇒U+201A single low-9 quotation mark
     "\xC2\x84" => '"', // U+0084⇒U+201E double low-9 quotation mark
     "\xC2\x8B" => "'", // U+008B⇒U+2039 single left-pointing angle quotation mark
     "\xC2\x91" => "'", // U+0091⇒U+2018 left single quotation mark
     "\xC2\x92" => "'", // U+0092⇒U+2019 right single quotation mark
     "\xC2\x93" => '"', // U+0093⇒U+201C left double quotation mark
     "\xC2\x94" => '"', // U+0094⇒U+201D right double quotation mark
     "\xC2\x9B" => "'", // U+009B⇒U+203A single right-pointing angle quotation mark

     // Regular Unicode     // U+0022 quotation mark (")
                            // U+0027 apostrophe     (')
     "\xC2\xAB"     => '"', // U+00AB left-pointing double angle quotation mark
     "\xC2\xBB"     => '"', // U+00BB right-pointing double angle quotation mark
     "\xE2\x80\x98" => "'", // U+2018 left single quotation mark
     "\xE2\x80\x99" => "'", // U+2019 right single quotation mark
     "\xE2\x80\x9A" => "'", // U+201A single low-9 quotation mark
     "\xE2\x80\x9B" => "'", // U+201B single high-reversed-9 quotation mark
     "\xE2\x80\x9C" => '"', // U+201C left double quotation mark
     "\xE2\x80\x9D" => '"', // U+201D right double quotation mark
     "\xE2\x80\x9E" => '"', // U+201E double low-9 quotation mark
     "\xE2\x80\x9F" => '"', // U+201F double high-reversed-9 quotation mark
     "\xE2\x80\xB9" => "'", // U+2039 single left-pointing angle quotation mark
     "\xE2\x80\xBA" => "'", // U+203A single right-pointing angle quotation mark
  );
  $chr = array_keys($chr_map);
  $rpl = array_values($chr_map);

  $string = str_replace($chr, $rpl, html_entity_decode($string, ENT_QUOTES, "UTF-8"));

  return $string;
}
