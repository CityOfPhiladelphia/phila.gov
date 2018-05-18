<?php

    $HOMEPAGE_V2_CONTENT_PARTIALS_PATHS = array(
        'global' => 'partials/',
        'v1'     => 'partials/departments/',
        'v2'     => 'partials/departments/v2/'
    );

    /**
     * Array of partials in include order
     * key is partial name, value is version name
     * @var array
     */
    $homepage_v2_content_partials = array(
        'our-services'         => array(
                                                'type'=>'v2',
                                                'shown'=>true
                                            ),
        'phila_module_row_1'              => array(
                                                'type'=>'v1',
                                                'shown'=>true
                                            ),
        'photo-callout-block'              => array(
                                                'type'=>'v2',
                                                'shown'=>true
                                            ),
        'homepage_programs' => array(
            'type'  => 'v2',
            'shown' => true,
        ),
        'phila_full_row_blog'                    => array(
                                                'type'=>'v1',
                                                'shown'=>$this->full_row_blog
                                            ),
        'full-width-call-to-action'      => array(
                                                'type'=>'v2',
                                                'shown'=>true
                                            ),
        'phila_module_row_2'               => array(
                                                'type'=>'v1',
                                                'shown'=>true
                                            ),

        'phila_full_row_press_releases'    => array(
                                                'type'  => 'shortcode',
                                                'shown' => $this->full_width_press_releases['exists'],
                                                'data'  => array(
                                                    'shortcode'=>'[press-releases posts="3" category="' . $this->full_width_press_releases['category_id'] .'"]'
                                                )
                                            ),
        'phila_staff_directory_listing'    => array(
                                                'type'=>'v1',
                                                'shown'=>$this->staff_directory_listing
                                            ),
        'phila_call_to_action_multi'    => array(
                                                'type'=>'v1',
                                                'shown'=>true
                                            ),
        'featured-programs-or-content'   => array(
                                                'type'=>'v2',
                                                'shown'=>!empty( $this->featured ),
                                                'data'=> array(
                                                    'featured'=>$this->featured
                                                )
                                             ),
       'board_commission_member_list'   => array(
                                         'type'=>'v2',
                                         'shown'=> true,
                                  ),
    );

    $meta_box_order = get_post_meta(get_the_ID(), 'phila_meta-box-order');

    //get the order of the visible (non-collpased) meta boxes from the Department Site Homepage admin
    $meta_box_order_arr  = explode(',', isset($meta_box_order[0]) ? $meta_box_order[0] : 'default');

    // placeholder array for our final template include order
    // defaults to our original content order
    $homepage_v2_content_paritals_include_order = $meta_box_order_arr[0] == "default" ?  $homepage_v2_content_partials :  array();

    /**
     * loop through our meta box order and populate the our final
     *  template include array with the proper template meta values
     * @var [type]
     */
    foreach ($meta_box_order_arr as $key => $value ) {

        if(key_exists($value, $homepage_v2_content_partials)){
            $_template_meta = $homepage_v2_content_partials[$value];
            // move the template_name to the meta so that we can retain an indexed array order
            $_template_meta['template_name'] = $value;

            array_push($homepage_v2_content_paritals_include_order, $_template_meta);
        }
    }


    /**
     * Loop through all template partials and add to page according to type
     */
    foreach ($homepage_v2_content_paritals_include_order as $partial_name => $partial_meta ) {

        switch ($partial_meta['type']) {

            case 'shortcode':

                // shortcode calls
                if($partial_meta['shown']):
                    echo do_shortcode($partial_meta['data']['shortcode']);
                endif;

            break;

            case 'global':
              $template_name =  key_exists('template_name', $partial_meta) ? $partial_meta['template_name'] : $partial_name;
              $template_path =  $HOMEPAGE_V2_CONTENT_PARTIALS_PATHS[$partial_meta['type']] .$template_name;
              $template_data =  key_exists('data',$partial_meta) ? $partial_meta['data'] : array();
              if($partial_meta['shown']):
                  phila_get_template_part( $template_path, $template_data);
              endif;

            case 'v1':
            case 'v2':
                // template includes
                $template_name =  key_exists('template_name',$partial_meta) ? $partial_meta['template_name'] : $partial_name;
                $template_path =  $HOMEPAGE_V2_CONTENT_PARTIALS_PATHS[$partial_meta['type']] .$template_name;
                $template_data =  key_exists('data',$partial_meta) ? $partial_meta['data'] : array();

                if($partial_meta['shown']):
                    phila_get_template_part( $template_path, $template_data);
                endif;

            break;

        }


    }


 ?>
