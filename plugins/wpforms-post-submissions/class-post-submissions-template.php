<?php
/**
 * Post Submissions form template.
 *
 * @package    WPFormsPostSubmissions
 * @author     WPForms
 * @since      1.0.0
 * @license    GPL-2.0+
 * @copyright  Copyright (c) 2016, WPForms LLC
*/
class WPForms_Template_Post_Submission extends WPForms_Template {

	/**
	 * Primary class constructor.
	 *
	 * @since 1.0.0
	 */
	public function init() {

		$this->name        = __( 'Blog Post Submission Form', 'wpforms_post_submissions' );
		$this->slug        = 'post_submission';
		$this->description = __( 'User-submitted content made easy. Allow your users to submit guest blog posts in WordPress. You can add and remove fields as needed.', 'wpforms_post_submissions' );
		$this->includes    = '';
		$this->icon        = '';
		$this->core        = true;
		$this->modal       = array(
			'title'   => __( 'Don&#39;t Forget', 'wpforms_post_submissions' ),
			'message' => __( 'Additional post submission options are available in the settings panel.', 'wpforms_post_submissions' ),
		);
		$this->data        = array(
			'field_id' => '11',
			'fields'   => array(
				'1' => array(
					'id'            => '1',
					'type'          => 'divider',
					'label'         => __( 'Author Details', 'wpforms_post_submissions' ),
					'description'   => __( 'Please enter your contact details, so we can give you proper credit for this blog post.', 'wpforms_post_submissions' ),
					'label_disable' => '1'
				),
				'2' => array(
					'id'            => '2',
					'type'          => 'name',
					'label'         => __( 'Name', 'wpforms_post_submissions' ),
					'format'        => 'first-last',
					'required'      => '1',
					'size'          => 'medium',
				),
				'3' => array(
					'id'            => '3',
					'type'          => 'email',
					'label'         => __( 'E-mail', 'wpforms_post_submissions' ),
					'required'      => '1',
					'size'          => 'medium',
				),
				'4' => array(
					'id'            => '4',
					'type'          => 'textarea',
					'label'         => __( 'Short Author Bio', 'wpforms_post_submissions' ),
					'description'   => __( 'Please keep it below 300 characters.', 'wpforms_post_submissions' ),
					'required'      => '1',
					'size'          => 'medium'
				),
				'5' => array(
					'id'            => '5',
					'type'          => 'divider',
					'label'         => __( 'Create a Blog Post', 'wpforms_post_submissions' ),
					'description'   => __( 'Please submit your guest blog posts by using the fields below.', 'wpforms_post_submissions' ),
					'label_disable' => '1',
				),
				'6' => array(
					'id'            => '6',
					'type'          => 'text',
					'label'         => __( 'Post Title', 'wpforms_post_submissions' ),
					'required'      => '1',
					'size'          => 'medium'
				),
				'7' => array(
					'id'            => '7',
					'type'          => 'textarea',
					'label'         => __( 'Post Content', 'wpforms_post_submissions' ),
					'required'      => '1',
					'size'          => 'medium'
				),
				'8' => array(
					'id'            => '8',
					'type'          => 'file-upload',
					'label'         => __( 'Featured Image', 'wpforms_post_submissions' ),
					'description'   => __( 'Please make sure the dimensions are (600 x 300px).', 'wpforms_post_submissions' ),
					'required'      => '1',
					'size'          => 'medium',
					'extensions'    => 'jpg,jpeg,png,gif',
					'media_library' => '1',
				),
				'9' => array(
					'id'            => '9',
					'type'          => 'textarea',
					'label'         => __( 'Post Excerpt', 'wpforms_post_submissions' ),
					'required'      => '1',
					'size'          => 'small'
				),
				'10' => array(
					'id'            => '10',
					'type'          => 'select',
					'label'         => __( 'Category', 'wpforms_post_submissions' ),
					'choices'       => array(
						'1' => array(
							'label' => __( 'First Choice', 'wpforms_post_submissions' ),
							'value' => '',
						),
					),
					'required'      => '1',
					'size'          => 'medium',
					'dynamic_choices'  => 'taxonomy',
					'dynamic_taxonomy' => 'category',
				),
			),
			'settings' => array(
				'honeypot'                    => '1',
				'confirmation_message_scroll' => '1',
				'submit_text'                 => __( 'Submit', 'wpforms_post_submissions' ),
				'submit_text_processing'      => __( 'Sending...', 'wpforms_post_submissions' ),
				'post_submissions'            => '1',
				'post_submissions_title'      => '6',
				'post_submissions_content'    => '7',
				'post_submissions_excerpt'    => '9',
				'post_submissions_featured'   => '8',
				'post_submissions_type'       => 'post',
				'post_submissions_status'     => 'pending',
				'post_submissions_author'     => 'current_user',
			),
			'meta'     => array(
				'template' => $this->slug,
			),
		);
	}

	/**
	 * Conditional to determine if the template informational modal screens
	 * should display.
	 *
	 * @since 1.0.0
	 * @param array $form_data
	 * @return boolean
	 */
	public function template_modal_conditional( $form_data ) {

		return true;
	}
}
new WPForms_Template_Post_Submission;
