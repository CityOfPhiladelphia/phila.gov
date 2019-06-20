<?php

add_filter( 'rwmb_meta_boxes', 'phila_register_tax_detail_meta_boxes' );

function phila_register_tax_detail_meta_boxes( $meta_boxes ){

  //Specific for tax detail template.
  $meta_var_tax_due_date = array(
    'id'  =>  'phila_tax_due_date',
    'type'  => 'group',
    'clone' => false,

    'fields' => array(
      array(
        'name'  => 'Due Date Callout',
        'type' => 'heading',
      ),

      array(
        'name'  => 'Due Date Type',
        'id'  => 'phila_tax_date_choice',
        'type'  => 'select',
        'options' => array(
          'monthly' => 'Tax is due monthly',
          'yearly'  => 'Tax is due yearly',
          'misc'  => 'Tax is miscellaneous'
        ),
      ),
      array(
        'visible' => array(
          'when' => array(
            array('phila_tax_date_choice', 'monthly'),
            array('phila_tax_date_choice', 'yearly'),
          ),
          'relation' => 'or',
        ),
        'name'  =>  'Tax Due Date',
        'id'  => 'phila_tax_date',
        'desc'  => 'Enter the day of the month this tax is due.',
        'type'  =>  'number',
        'min' => '1',
        'max' => '31',
        'required'  => true
      ),

      array(
        'visible' => array('phila_tax_date_choice', 'yearly'),
        'name'  => 'Month Due',
        'id'  => 'phila_tax_date_month',
        'desc'  => 'Enter the month of the year this tax is due.',
        'type'  => 'select',
        'placeholder' => 'Choose month...',
        'options' => phila_return_month_array(),
      ),
      array(
        'hidden' => array('phila_tax_date_choice', 'misc'),
        'name'  => 'Brief Explanation',
        'id'  => 'phila_tax_date_summary_brief',
        'type'  => 'wysiwyg',
        'desc'  => 'Example: "of each month, for the prior month\'s activity." <br>This content will appear in the date callout box . ',
        'options' => Phila_Gov_Standard_Metaboxes::phila_wysiwyg_options_basic(),
        'required'  => true
      ),
      array(
        'visible' => array('phila_tax_date_choice', 'misc'),
        'id'  => 'phila_tax_date_misc_details',
        'type'  => 'wysiwyg',
        'desc'  => 'This content will appear in the date callout box . ',
        'options' => Phila_Gov_Standard_Metaboxes::phila_wysiwyg_options_basic(),
        'required'  => true
      ),
      array(
        'name'  => 'Due Date Details',
        'type'  => 'heading',
      ),
      array(
        'id'  => 'phila_tax_date_summary_detailed',
        'type'  => 'wysiwyg',
        'desc'  => 'Provide detailed date information. This content will appear in the "Important Dates" section.',
        'options' => Phila_Gov_Standard_Metaboxes::phila_wysiwyg_options_basic(),
        'required'  => true
      ),
    ),
  );
  //Specific to the tax detail template
  $meta_var_tax_costs = array(
    'id'  =>  'phila_tax_costs',
    'type'  => 'group',
    'clone' => false,

    'fields' => array(
      array(
        'name'  => 'Tax Rate Callout',
        'type' => 'heading',
      ),
      array(
        'name'  =>  'Tax Cost',
        'id'  => 'phila_tax_cost_number',
        'type'  =>  'number',
        'step'  => 'any',
      ),
      array(
        'name'  => 'Unit',
        'id'  =>  'phila_tax_cost_unit',
        'type'  => 'select',
        'options' => array(
          'percent' => '%',
          'dollar'  => '$',
          'mills' => 'mills'
        )
      ),
      array(
        'name'  => 'Brief Explanation',
        'id'  => 'phila_tax_cost_summary_brief',
        'type'  => 'wysiwyg',
        'desc'  => 'Example: "of the admission charge." <br> This content will appear in the tax callout box . ',
        'options' => Phila_Gov_Standard_Metaboxes::phila_wysiwyg_options_basic(),
        'required'  => true
      ),
      array(
        'name'  => 'Cost Details',
        'type'  => 'heading'
      ),
      array(
        'id'  => 'phila_tax_cost_summary_detailed',
        'type'  => 'wysiwyg',
        'desc'  => 'Provide detailed cost information. This content will appear under the "Tax Rates, Penalties & Fees" section.',
        'options' => Phila_Gov_Standard_Metaboxes::phila_wysiwyg_options_basic(),
        'required'  => true
      ),
    ),
  );

  //Tax Detail Template
  $meta_boxes[] = array(
    'title' => 'Tax Highlights',
    'pages' => array('service_page'),
    'priority' => 'high',

    'visible' => array('phila_template_select', 'tax_detail'),

    'fields'  => array(
      array(
        'id'  => 'phila_tax_highlights',
        'type'   => 'group',

        'fields'  => array(
          array(
            'name' => 'Important changes about this tax',
            'type'  => 'heading'
          ),
          array(
            'id'  => 'phila_wysiwyg_callout',
            'type'  => 'wysiwyg',
            'options' =>
            Phila_Gov_Standard_Metaboxes::phila_wysiwyg_options_basic( 100 )
          ),
          $meta_var_tax_due_date,
          array(
            'type'  => 'divider'
          ),
          $meta_var_tax_costs,
          array(
            'type'  => 'divider'
          ),
          array(
            'id'  => 'phila_tax_code',
            'name'  => 'Tax Type Code',
            'type'  => 'number'
          ),
        ),
      )
    )
  );

  $meta_boxes[] = array(
    'title' => 'Tax Details',
    'pages' => array('service_page'),
    'priority' => 'high',
    'visible' => array('phila_template_select', 'tax_detail'),

    'fields'  => array(
      array(
        'id'  => 'phila_tax_payment_info',
        'type'   => 'group',

        'fields'  => array(
          array(
            'name' => 'Who has to pay the tax?',
            'type'  => 'heading'
          ),
          array(
            'id'  => 'phila_tax_who_pays',
            'type'  => 'wysiwyg',
            'options' => Phila_Gov_Standard_Metaboxes::phila_wysiwyg_options_basic_heading()
          ),
          array(
            'name'  => 'What happens if the tax is not paid on time?',
            'type'  => 'heading'
          ),
          array(
            'id'  => 'phila_tax_late_fees',
            'type'  => 'wysiwyg',
            'options' => Phila_Gov_Standard_Metaboxes::phila_wysiwyg_options_basic_heading()
          ),
          array(
            'name' => 'Who is eligible for a discount?',
            'type'  => 'heading'
          ),
          array(
            'id'  => 'phila_tax_discounts',
            'type'  => 'wysiwyg',
            'options' => Phila_Gov_Standard_Metaboxes::phila_wysiwyg_options_basic_heading()
            ),
          array(
            'name'  => 'Can you be excused from paying the tax?',
            'type'  => 'heading'
          ),
          array(
            'id'  => 'phila_tax_exemptions',
            'type'  => 'wysiwyg',
            'options' => Phila_Gov_Standard_Metaboxes::phila_wysiwyg_options_basic_heading()
          )
        )
      )
    )
  );

  $meta_boxes[] = array(
    'title' => 'How to pay',
    'pages' => array('service_page'),
    'priority' => 'high',
    'visible' => array('phila_template_select', 'tax_detail'),

    'fields'  => array(
      array(
        'id'  => 'phila_payment_group',
        'type'  => 'group',

      'fields' => array(
          array(
            'name' => 'Introduction',
            'type'  => 'heading',
          ),
          Phila_Gov_Standard_Metaboxes::phila_metabox_v2_wysiwyg_address(),
          array(
            'name' => 'Steps in payment process',
            'type'  => 'heading',
          ),
          Phila_Gov_Standard_Metaboxes::phila_metabox_v2_ordered_content()
        ),
      )
    )
  );

  return $meta_boxes;
}
