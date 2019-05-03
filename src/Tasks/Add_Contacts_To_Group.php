<?php
namespace Modern_Tribe\Purple_Team\LiveAgent_Tools\Tasks;

use Exception;
use Modern_Tribe\Purple_Team\LiveAgent_Tools;

class Add_Contacts_To_Group {
	private $contact_emails = [];
	private $failed = 0;
	private $group_id = '';
	private $skipped = 0;
	private $updated = 0;


	/**
	 * Given an array of contact email addresses, adds each to the specified
	 * group (can be a group ID or a group name).
	 *
	 * @param array $contact_emails
	 * @param $group_reference
	 *
	 * @throws Exception
	 */
	public function __construct( array $contact_emails, $group_reference ) {
		$this->contact_emails = $contact_emails;
		$this->determine_group_id( $group_reference );

		foreach ( LiveAgent_Tools\api()->each( 'contacts' ) as $contact ) {
			// Each contact may be associated with more than one email address
			foreach ( $contact->emails as $known_email_address ) {
				if ( in_array( $known_email_address, $this->contact_emails ) ) {
					$this->add_contact_to_group( $contact );
				}
			}
		}
	}

	/**
	 * @param string $group_reference
	 *
	 * @throws Exception
	 */
	private function determine_group_id( $group_reference ) {
		foreach ( LiveAgent_Tools\api()->each( 'groups') as $group ) {
			if ( $group->id === $group_reference || $group->name === $group_reference ) {
				$this->group_id = $group->id;
			}
		}

		if ( empty( $this->group_id ) ) {
			throw new Exception( 'Group does not exist.' );
		}
	}

	private function add_contact_to_group( $contact ) {
		$groups = (array) $contact->groups;

		if ( ! in_array( $this->group_id, $groups ) ) {
			// Append the new group, do not overwrite existing groups
			$groups[] = $this->group_id;

			if ( LiveAgent_Tools\api()->update( "contacts/{$contact->id}", [ 'groups' => $groups ] ) ) {
				$this->updated++;
				return true;
			}

			$this->failed++;
			return false;
		}

		// Is already in the desired target group
		$this->skipped++;
		return true;
	}

	public function summary() {
		return (object) [
			'failed'  => $this->failed,
			'skipped' => $this->skipped,
			'updated' => $this->updated,
		];
	}
}