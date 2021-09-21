<?php

namespace MyListing\Apis\Wp_All_Import;

if ( ! defined('ABSPATH') ) {
	exit;
}

function import_files( $field, $field_value, $log, $import, $download_image, $delimiter ) {
	$files = array_filter( array_map( 'trim', explode( $delimiter, (string) $field_value ) ) );
	$uploaded = [];
	foreach ( $files as $url_or_path ) {
		$extension = substr( strrchr( $url_or_path, '.' ), 1 );
		$file_type = in_array( $extension, [ 'jpg', 'gif', 'png', 'jpeg', 'jpe' ] ) ? 'images' : 'files';

		$attachment_id = \PMXI_API::upload_image(
			$field->listing->get_id(), $url_or_path, $download_image, $log, true, '', $file_type, true, $import['articleData'], $import
		);

		$file_guid = get_the_guid( $attachment_id );

		if ( $attachment_id && ! empty( $file_guid ) ) {
			$uploaded[] = $file_guid;
		}
	}

	update_post_meta( $field->listing->get_id(), '_'.$field->get_key(), array_filter( $uploaded ) );
}
