<?php
/**
 * Tests for the CKGF_API class.
 *
 * @since   1.3.1
 */
class APITest extends \Codeception\TestCase\WPTestCase
{
	/**
	 * The testing implementation.
	 *
	 * @var \WpunitTester.
	 */
	protected $tester;

	/**
	 * Holds the ConvertKit API class.
	 *
	 * @since   1.3.1
	 *
	 * @var     CKGF_API
	 */
	private $api;

	/**
	 * Performs actions before each test.
	 *
	 * @since   1.3.1
	 */
	public function setUp(): void
	{
		parent::setUp();

		// Activate Plugin, to include the Plugin's constants in tests.
		activate_plugins('convertkit-gravity-forms/convertkit.php');

		// Include class from /includes to test, as they won't be loaded by the Plugin
		// because Gravity Forms is not active.
		require_once 'includes/class-wp-ckgf.php';

		// Initialize the classes we want to test.
		$this->api = new CKGF_API( $_ENV['CONVERTKIT_API_KEY'], $_ENV['CONVERTKIT_API_SECRET'] );
	}

	/**
	 * Performs actions after each test.
	 *
	 * @since   1.3.1
	 */
	public function tearDown(): void
	{
		// Destroy the classes we tested.
		unset($this->api);

		parent::tearDown();
	}

	/**
	 * Test that the User Agent string is in the expected format and
	 * includes the Plugin's name and version number.
	 *
	 * @since   1.3.1
	 */
	public function testUserAgent()
	{
		// When an API call is made, inspect the user-agent argument.
		add_filter(
			'http_request_args',
			function($args, $url) { // phpcs:ignore Generic.CodeAnalysis.UnusedFunctionParameter
				$this->assertStringContainsString(
					CKGF_PLUGIN_NAME . '/' . CKGF_PLUGIN_VERSION,
					$args['user-agent']
				);
				return $args;
			},
			10,
			2
		);

		// Perform a request.
		$result = $this->api->account();
	}
}
