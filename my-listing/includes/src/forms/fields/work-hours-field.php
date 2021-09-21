<?php

namespace MyListing\Src\Forms\Fields;

if ( ! defined('ABSPATH') ) {
	exit;
}

class Work_Hours_Field extends Base_Field {

	public function get_posted_value() {
		return ! empty( $_POST[ $this->key ] ) ? (array) $_POST[ $this->key ] : [];
	}

	public function validate() {
		$value = $this->get_posted_value();
		//
	}

	public function field_props() {
		$this->props['type'] = 'work-hours';
	}

	public function string_value( $modifier = null ) {
		$schedule = $this->listing->get_schedule();
		if ( $schedule->get_status() === 'not-available' ) {
			return '';
		}

		return $schedule->get_open_now()
			? _x( 'OPEN', 'Work hours status', 'my-listing' )
			: _x( 'CLOSED', 'Work hours status', 'my-listing' );
	}

	public function get_editor_options() {
		$this->getLabelField();
		$this->getKeyField();
		$this->getPlaceholderField();
		$this->getDescriptionField();
		$this->getRequiredField();
		$this->getShowInSubmitFormField();
		$this->getShowInAdminField();
	}
}