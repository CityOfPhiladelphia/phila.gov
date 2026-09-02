<?php

namespace FileBird\Controller\Attachment;

class SizeMeta {
    private static $instance = null;
    private $optionState     = 'fbv_generate_attachment_size';
    private $updatedState    = 'fbv_updated_attachment_size';
    private $processState    = array(
        'isProcessing' => false,
        'total'        => null,
        'current'      => 0,
        'ids'          => array(),
    );

    public $meta_key = 'fb_filesize';

	public static function getInstance() {
		if ( null == self::$instance ) {
			self::$instance = new self();
		}
		return self::$instance;
	}

    public function doHooks() {
        add_action( 'added_post_meta', array( $this, 'added_post_meta' ), 10, 4 );
    }

    public function prepare( $state = array() ) {
        $query = new \WP_Query(
            array(
                'post_type'      => 'attachment',
                'posts_per_page' => -1,
                'post_status'    => 'inherit',
                'fields'         => 'ids',
            )
        );

        $state['total']        = $query->found_posts;
        $state['ids']          = $query->posts;
		$state['isProcessing'] = true;
        $this->update_state( $state );
    }

    public function processing( $generateAll = false ) {
        $state          = $this->get_state();
        $ids            = $state['ids'];
        $updatedIds     = get_option( $this->updatedState, array() );
        $updatedCounter = 0;
        if ( ! $generateAll ) {
            $ids = array_diff( $ids, $updatedIds );
            if ( empty( $ids ) ) {
                $updatedCounter = count( $state['ids'] );
            } else {
                $updatedCounter = count( $updatedIds );
            }
		}
        foreach ( $ids as $index => $post_id ) {
            $state['current'] = $index + 1;
            // usleep( 10 * 1000 );
            $this->generate_meta_size( $post_id );
            $this->update_state( $state );
        }

        $state['isProcessing'] = false;
        $state['current']      = $state['current'] + $updatedCounter;
        update_option( $this->updatedState, $state['ids'] );
        $this->update_state( $state );
    }

    public function get_state() {
        return get_option( $this->optionState, $this->processState );
    }

    public function update_state( $new_state = array() ) {
        update_option( $this->optionState, $new_state, false );
    }

    public function cancel_process() {
        delete_option( $this->optionState );
    }

    public function apiCallback() {
        $generateAll = true;
		$state       = $this->get_state();
        if ( $state['current'] >= intval( $state['total'] ) ) {
            $this->cancel_process();
    		$state = $this->get_state();
        }

		if ( ! $state['isProcessing'] ) {
            if ( is_null( $state['total'] ) ) {
                $this->prepare( $state );
            }
            if ( $state['current'] !== $state['total'] ) {
                $this->processing( $generateAll );
                $this->cancel_process();
            }
		}

        return $this->get_state();
    }

    public function generate_meta_size( $post_id ) {
        $is_file_exist = \file_exists( \get_attached_file( $post_id ) );
        $file_size     = '';
        if ( $is_file_exist ) {
            $file_size = \filesize( \get_attached_file( $post_id ) );
            update_post_meta( $post_id, $this->meta_key, $file_size );
        }
        return $file_size;
    }

    public function added_post_meta( $meta_id, $post_id, $meta_key, $meta_value ) {
        if ( '_wp_attachment_metadata' === $meta_key ) {
           $this->generate_meta_size( $post_id );
		}
    }
}