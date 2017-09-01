<?php

class wpcmsb_Help_Tabs {

	private $screen;

	public function __construct( WP_Screen $screen ) {
		$this->screen = $screen;
	}

	public function set_help_tabs( $type ) {
		switch ( $type ) {
			case 'list':
				$this->screen->add_help_tab( array(
					'id' => 'list_overview',
					'title' => __( 'Overview', 'cms-block' ),
					'content' => $this->content( 'list_overview' ) ) );

				$this->screen->add_help_tab( array(
					'id' => 'list_available_actions',
					'title' => __( 'Available Actions', 'cms-block' ),
					'content' => $this->content( 'list_available_actions' ) ) );

				$this->sidebar();

				return;
			case 'add_new':
				$this->screen->add_help_tab( array(
					'id' => 'add_new',
					'title' => __( 'Adding A New CMS Block', 'cms-block' ),
					'content' => $this->content( 'add_new' ) ) );

				$this->sidebar();

				return;
			case 'edit':
				$this->screen->add_help_tab( array(
					'id' => 'edit_overview',
					'title' => __( 'Overview', 'cms-block' ),
					'content' => $this->content( 'edit_overview' ) ) );

				$this->screen->add_help_tab( array(
					'id' => 'edit_form_tags',
					'title' => __( 'Form-tags', 'cms-block' ),
					'content' => $this->content( 'edit_form_tags' ) ) );
				
				$this->sidebar();

				return;
			case 'integration':
				$this->screen->add_help_tab( array(
					'id' => 'integration_overview',
					'title' => __( 'Overview', 'cms-block' ),
					'content' => $this->content( 'integration_overview' ) ) );

				$this->sidebar();

				return;
		}
	}

	private function content( $name ) {
		$content = array();

		$content['list_overview'] = '<p>' . __( "On this screen, you can manage CMS Blocks provided by CMS Block 1. You can manage an unlimited number of CMS Blocks. Each CMS Block has a unique ID and CMS Block 1 shortcode ([cms-block ...]). To insert a CMS Block into a post or a text widget, insert the shortcode into the target.", 'cms-block' ) . '</p>';

		$content['list_available_actions'] = '<p>' . __( "Hovering over a row in the CMS Blocks list will display action links that allow you to manage your CMS Block. You can perform the following actions:", 'cms-block' ) . '</p>';
		$content['list_available_actions'] .= '<p>' . __( "<strong>Edit</strong> - Navigates to the editing screen for that CMS Block. You can also reach that screen by clicking on the CMS Block title.", 'cms-block' ) . '</p>';
		$content['list_available_actions'] .= '<p>' . __( "<strong>Duplicate</strong> - Clones that CMS Block. A cloned CMS Block inherits all content from the original, but has a different ID.", 'cms-block' ) . '</p>';

		$content['add_new'] = '<p>' . __( "You can add a new CMS Block on this screen. You can create a CMS Block in your language, which is set WordPress local settings, or in a language that you select from available options.", 'cms-block' ) . '</p>';

		$content['edit_overview'] = '<p>' . __( "On this screen, you can edit a CMS Block. A CMS Block is comprised of the following components:", 'cms-block' ) . '</p>';
		$content['edit_overview'] .= '<p>' . __( "<strong>Title</strong> is the title of a CMS Block. This title is only used for labeling a CMS Block, and can be edited.", 'cms-block' ) . '</p>';
		$content['edit_overview'] .= '<p>' . __( "<strong>Form</strong> is a content of HTML form. You can use arbitrary HTML, which is allowed inside a form element. You can also use CMS Block 1&#8217;s form-tags here.", 'cms-block' ) . '</p>';
		$content['edit_overview'] .= '<p>' . __( "<strong>Mail</strong> manages a mail template (headers and message body) that this CMS Block will send when users submit it. You can use CMS Block 1&#8217;s mail-tags here.", 'cms-block' ) . '</p>';
		$content['edit_overview'] .= '<p>' . __( "<strong>Mail (2)</strong> is an additional mail template that works similar to Mail. Mail (2) is different in that it is sent only when Mail has been sent successfully.", 'cms-block' ) . '</p>';
		$content['edit_overview'] .= '<p>' . __( "In <strong>Messages</strong>, you can edit various types of messages used for this CMS Block. These messages are relatively short messages, like a validation error message you see when you leave a required field blank.", 'cms-block' ) . '</p>';
		$content['edit_overview'] .= '<p>' . __( "<strong>Additional Settings</strong> provides a place where you can customize the behavior of this CMS Block by adding code snippets.", 'cms-block' ) . '</p>';

		$content['edit_form_tags'] = '<p>' . __( "A form-tag is a short code enclosed in square brackets used in a form content. A form-tag generally represents an input field, and its components can be separated into four parts: type, name, options, and values. CMS Block 1 supports several types of form-tags including text fields, number fields, date fields, checkboxes, radio buttons, menus, file-uploading fields, CAPTCHAs, and quiz fields.", 'cms-block' ) . '</p>';
		$content['edit_form_tags'] .= '<p>' . __( "While form-tags have a comparatively complex syntax, you don&#8217;t need to know the syntax to add form-tags because you can use the straightforward tag generator (<strong>Generate Tag</strong> button on this screen).", 'cms-block' ) . '</p>';
		
		$content['integration_overview'] = '<p>' . __( "On this screen, you can manage services that are available through CMS Block 1. Using API will allow you to collaborate with any services that are available.", 'cms-block' ) . '</p>';
		$content['integration_overview'] .= '<p>' . __( "You may need to first sign up for an account with the service that you plan to use. When you do so, you would need to authorize CMS Block 1 to access the service with your account.", 'cms-block' ) . '</p>';
		$content['integration_overview'] .= '<p>' . __( "Any information you provide will not be shared with service providers without your authorization.", 'cms-block' ) . '</p>';

		if ( ! empty( $content[$name] ) ) {
			return $content[$name];
		}
	}

	public function sidebar() {
		$content = '<p><strong>' . __( 'For more information:', 'cms-block' ) . '</strong></p>';
		$content .= '<p>' . wpcmsb_link( __( 'http://cmsblock.com/docs/', 'cms-block' ), __( 'Docs', 'cms-block' ) ) . '</p>';
		$content .= '<p>' . wpcmsb_link( __( 'http://cmsblock.com/faq/', 'cms-block' ), __( 'FAQ', 'cms-block' ) ) . '</p>';
		$content .= '<p>' . wpcmsb_link( __( 'http://cmsblock.com/support/', 'cms-block' ), __( 'Support', 'cms-block' ) ) . '</p>';

		$this->screen->set_help_sidebar( $content );
	}
}
