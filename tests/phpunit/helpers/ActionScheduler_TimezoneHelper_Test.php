<?php

/**
 * @group timezone
 */
class ActionScheduler_TimezoneHelper_Test extends ActionScheduler_UnitTestCase {

	/**
	 * Ensure that the timezone string we expect works properly.
	 *
	 * @dataProvider local_timezone_provider
	 *
	 * @param $timezone_string
	 */
	public function test_local_timezone_strings( $timezone_string ) {
		$timezone_filter = function ( $tz ) use ( $timezone_string ) {
			return $timezone_string;
		};

		add_filter( 'option_timezone_string', $timezone_filter );
		$timezone = ActionScheduler_TimezoneHelper::get_local_timezone( true );
		$this->assertInstanceOf( 'DateTimeZone', $timezone );
		$this->assertEquals( $timezone_string, $timezone->getName() );
		remove_filter( 'option_timezone_string', $timezone_filter );
	}

	public function local_timezone_provider() {
		return array(
			array( 'America/New_York' ),
			array( 'Australia/Melbourne' ),
			array( 'UTC' ),
		);
	}

	/**
	 * Ensure that GMT offsets don't return UTC as the timezone.
	 *
	 * @dataProvider local_timezone_offsets_provider
	 *
	 * @param $gmt_offset
	 */
	public function test_local_timezone_offsets( $gmt_offset ) {
		$gmt_filter = function ( $gmt ) use ( $gmt_offset ) {
			return $gmt_offset;
		};

		add_filter( 'option_gmt_offset', $gmt_filter );
		try {
			$timezone = ActionScheduler_TimezoneHelper::get_local_timezone( true );
		} catch ( Exception $_e ) {
			$e = $_e;
			// Handle outside this block...
		}
		remove_filter( 'option_gmt_offset', $gmt_filter );

		if ( isset( $e ) ) {
			if ( false !== stripos( $e->getMessage(), 'unknown or bad timezone' ) ) {
				$this->fail( sprintf( 'GMT offset [%s] caused fatal error.', $gmt_offset ) );
			} else {
				throw $e;
			}
		}

		$this->assertInstanceOf( 'DateTimeZone', $timezone );
		$this->assertNotEquals( 'UTC', $timezone->getName() );
	}

	public function local_timezone_offsets_provider() {
		return array(
			array( '11' ),
			array( '10' ),
			array( '9' ),
			array( '8' ),
			array( '7' ),
			array( '6' ),
			array( '5' ),
			array( '4' ),
			array( '3' ),
			array( '2' ),
			array( '1' ),
			array( '-1' ),
			array( '-2' ),
			array( '-3' ),
			array( '-4' ),
			array( '-5' ),
			array( '-6' ),
			array( '-7' ),
			array( '-8' ),
		);
	}
}
