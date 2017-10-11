<?php

    $HOMEPAGE_V2_CONTENT_PARTIALS_PATHS = array(
        'global' => 'partials/content-',
        'v1'     => 'partials/departments/',
        'v2'     => 'partials/departments/v2/content-'
    );

    /**
     * Array of partials in include order
     * key is partial name, value is version name
     * @var array
     */

    $HOMEPAGE_V2_CONTENT_PARTIALS = array(
        'service-updates'              => array(
                                                'type' => 'global',
                                                'shown'   => true
                                            ),
        'curated-service-list'         => array(
                                                'type'=>'v2',
                                                'shown'=>true
                                            ),
        'content-row-one'              => array(
                                                'type'=>'v1',
                                                'shown'=>true
                                            ),
        'row-posts'                    => array(
                                                'type'=>'v1',
                                                'shown'=>$this->full_row_blog
                                            ),
        'homepage-full-width-cta'      => array(
                                                'type'=>'v2',
                                                'shown'=>true
                                            ),
        'full_row_news'                => array(
                                                'type'  => 'shortcode',
                                                'shown' => $this->full_row_news['exists'],
                                                'data'  => array(
                                                    'shortcode'=>'[recent-news posts="3" category=" ' . $this->full_row_news['category_id'] .' "]'
                                                )
                                            ),
        'content-row-two'               => array(
                                                'type'=>'v1',
                                                'shown'=>true
                                            ),

        'full_width_press_releases'    => array(
                                                'type'  => 'shortcode',
                                                'shown' => $this->full_width_press_releases['exists'],
                                                'data'  => array(
                                                    'shortcode'=>'[press-releases posts="3" category=" ' . $this->full_width_press_releases['category_id'] .' "]'
                                                )
                                            ),
        'content-staff-directory'      => array(
                                                'type'=>'v1',
                                                'shown'=>$this->staff_directory_listing
                                            ),
        'content-call-to-action-multi' => array(
                                                'type'=>'v1',
                                                'shown'=>true
                                            ),
        'featured'                     => array(
                                                'type'=>'v1',
                                                'shown'=>!empty( $this->featured )
                                             ),
    );





 // TODO: get curent user value (wp_get_current_user()->ID) when not logged in to admin
    // $meta_box_order = get_user_meta(wp_get_current_user()->ID ,sprintf( 'meta-box-order_%s', get_post_type() ),true);
    // $meta_box_order_arr = explode(',',$meta_box_order['normal']);

    $meta_box_order_arr = get_post_meta(get_the_ID(), 'phila_meta-box-order');
    d(
        explode(',',$meta_box_order_arr[0])
    );

    // re-orders position fo full-width cta based on placement in admin
    $cta_postiion = array_search('full-width-call-to-action', $meta_box_order_arr);
    // TODO: more accurate postion values
    if($cta_postiion == 21 ){
        $v = $HOMEPAGE_V2_CONTENT_PARTIALS['homepage-full-width-cta'];
        unset($HOMEPAGE_V2_CONTENT_PARTIALS['homepage-full-width-cta']);
        $HOMEPAGE_V2_CONTENT_PARTIALS['homepage-full-width-cta'] = $v;
    }







    /**
     * Loop through all template partials and add to page according to type
     */
    foreach ($HOMEPAGE_V2_CONTENT_PARTIALS as $partial_name => $partial_meta) {


        switch ($partial_meta['type']) {

            case 'shortcode':
                // shortcode calls
                if($partial_meta['shown']):
                    do_shortcode($partial_meta['data']['shortcode']);
                endif;

            break;


            case 'v1':
            case 'v2':
                // template includes
                $template_path =  $HOMEPAGE_V2_CONTENT_PARTIALS_PATHS[$partial_meta['type']].$partial_name;
                $template_data =  key_exists('data',$partial_meta) ? $partial_meta['data'] : array();

                if($partial_meta['shown']):
                    phila_get_template_part( $template_path, $template_data);
                endif;

            break;

        }


    }


 ?>
