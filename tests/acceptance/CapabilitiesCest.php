<?php
/**
 * Tests that a Role's Capabilities are honored when enabled/disabled for:
 * - Add-On Settings (Forms > Settings > ConvertKit)
 * - Form Settings (Forms > Edit Form > Settings > ConvertKit)
 * - Uninstall (Forms > Settings > Uninstall > ConvertKit)
 * 
 * @since 	1.2.5
 */
class CapabilitiesCest
{
	/**
	 * Run common actions before running the test functions in this class.
	 * 
	 * @since 	1.2.5
	 * 
	 * @param 	AcceptanceTester 	$I 	Tester
	 */
	public function _before(AcceptanceTester $I)
	{
		$I->activateConvertKitPlugin($I);
		$I->activateThirdPartyPlugin($I, 'gravity-forms');
		$I->setupConvertKitPlugin($I);
	}

	/**
	 * Test that a WordPress User assigned a Role with the Add-On Settings capability enabled
	 * can access the add-on settings screen at Forms > Settings > ConvertKit.
	 * 
	 * @since 	1.2.5
	 * 
	 * @param 	AcceptanceTester 	$I 	Tester
	 */
	public function testAddOnSettingsWhenCapabilityEnabledForRole(AcceptanceTester $I)
	{
		// Create Role.
		$I->createGravityFormsRole($I, true, false, false);

		// Create User, assigned to the Role.
		$I->haveUserInDatabase('gravity_forms_user', 'gravity_forms', [
			'user_email' 	=> 'gravity_forms_user@test.local',
			'user_pass' 	=> $_ENV['TEST_SITE_ADMIN_PASSWORD'],
		]);

		// Logout.
		$I->logOut();

		// Login as Gravity Forms users.
		$I->loginAs('gravity_forms_user', $_ENV['TEST_SITE_ADMIN_PASSWORD']);

		// Go to the Plugin's Settings Screen.
		$I->loadConvertKitSettingsScreen($I);

		// Confirm that an expected settings field displays.
		$I->seeInSource('<input type="text" name="_gform_setting_api_key"');
	}

	/**
	 * Test that a WordPress User assigned a Role with the Add-On Settings capability disabled
	 * cannot access the add-on settings screen at Forms > Settings > ConvertKit.
	 * 
	 * @since 	1.2.5
	 * 
	 * @param 	AcceptanceTester 	$I 	Tester
	 */
	public function testAddOnSettingsWhenCapabilityDisabledForRole(AcceptanceTester $I)
	{
		// Create Role.
		$I->createGravityFormsRole($I, false, false, false);

		// Create User, assigned to the Role.
		$I->haveUserInDatabase('gravity_forms_user', 'gravity_forms', [
			'user_email' 	=> 'gravity_forms_user@test.local',
			'user_pass' 	=> $_ENV['TEST_SITE_ADMIN_PASSWORD'],
		]);

		// Logout.
		$I->logOut();

		// Login as Gravity Forms users.
		$I->loginAs('gravity_forms_user', $_ENV['TEST_SITE_ADMIN_PASSWORD']);

		// Go to the Plugin's Settings Screen.
		$I->loadConvertKitSettingsScreen($I);

		// Confirm that an expected settings field displays.
		$I->dontSeeInSource('<input type="text" name="_gform_setting_api_key"');
	}

	/**
	 * Test that a WordPress User assigned a Role with the Form Settings capability enabled
	 * can access the add-on settings screen at Forms > Edit Form > Settings > ConvertKit.
	 * 
	 * @since 	1.2.5
	 * 
	 * @param 	AcceptanceTester 	$I 	Tester
	 */
	public function testFormSettingsWhenCapabilityEnabledForRole(AcceptanceTester $I)
	{
		// Create Role.
		$I->createGravityFormsRole($I, false, true, false);

		// Create User, assigned to the Role.
		$I->haveUserInDatabase('gravity_forms_user', 'gravity_forms', [
			'user_email' 	=> 'gravity_forms_user@test.local',
			'user_pass' 	=> $_ENV['TEST_SITE_ADMIN_PASSWORD'],
		]);

		// Logout.
		$I->logOut();

		// Login as Gravity Forms users.
		$I->loginAs('gravity_forms_user', $_ENV['TEST_SITE_ADMIN_PASSWORD']);

		// Create Form.
		$gravityFormID = $I->createGravityFormsForm($I);

		// Go to the Form's Settings Screen.
		$I->amOnAdminPage('admin.php?page=gf_edit_forms&view=settings&subview=ckgf&id='.$gravityFormID);

		// Confirm that ConvertKit Feeds are displayed.
		$I->seeInSource('ConvertKit Feeds');
	}

	/**
	 * Test that a WordPress User assigned a Role with the Form Settings capability disabled
	 * cannot access the add-on settings screen at Forms > Edit Form > Settings > ConvertKit.
	 * 
	 * @since 	1.2.5
	 * 
	 * @param 	AcceptanceTester 	$I 	Tester
	 */
	public function testFormSettingsWhenCapabilityDisabledForRole(AcceptanceTester $I)
	{
		// Create Role.
		$I->createGravityFormsRole($I, false, false, false);

		// Create User, assigned to the Role.
		$I->haveUserInDatabase('gravity_forms_user', 'gravity_forms', [
			'user_email' 	=> 'gravity_forms_user@test.local',
			'user_pass' 	=> $_ENV['TEST_SITE_ADMIN_PASSWORD'],
		]);

		// Logout.
		$I->logOut();

		// Login as Gravity Forms users.
		$I->loginAs('gravity_forms_user', $_ENV['TEST_SITE_ADMIN_PASSWORD']);

		// Create Form.
		$gravityFormID = $I->createGravityFormsForm($I);

		// Go to the Form's Settings Screen.
		$I->amOnAdminPage('admin.php?page=gf_edit_forms&view=settings&subview=ckgf&id='.$gravityFormID);

		// Confirm that ConvertKit Feeds are displayed.
		$I->dontSeeInSource('ConvertKit Feeds');
	}

	/**
	 * Test that a WordPress User assigned a Role with the Uninstall capability enabled
	 * can access the add-on uninstall functionality at Forms > Settings > Uninstall.
	 * 
	 * @since 	1.2.5
	 * 
	 * @param 	AcceptanceTester 	$I 	Tester
	 */
	public function testUninstallWhenCapabilityEnabledForRole(AcceptanceTester $I)
	{
		// Create Role.
		$I->createGravityFormsRole($I, false, false, true);

		// Create User, assigned to the Role.
		$I->haveUserInDatabase('gravity_forms_user', 'gravity_forms', [
			'user_email' 	=> 'gravity_forms_user@test.local',
			'user_pass' 	=> $_ENV['TEST_SITE_ADMIN_PASSWORD'],
		]);

		// Logout.
		$I->logOut();

		// Login as Gravity Forms users.
		$I->loginAs('gravity_forms_user', $_ENV['TEST_SITE_ADMIN_PASSWORD']);

		// Go to the Settings > Uninstall Screen.
		$I->amOnAdminPage('admin.php?page=gf_settings&subview=uninstall');

		// Confirm that the uninstall option for this Plugin is displayed.
		$I->seeInSource('This operation deletes ALL ConvertKit settings.');
	}

	/**
	 * Test that a WordPress User assigned a Role with the Uninstall capability disabled
	 * cannot access the add-on uninstall functionality at Forms > Settings > Uninstall.
	 * 
	 * @since 	1.2.5
	 * 
	 * @param 	AcceptanceTester 	$I 	Tester
	 */
	public function testUninstallWhenCapabilityDisabledForRole(AcceptanceTester $I)
	{
		// Create Role.
		$I->createGravityFormsRole($I, false, false, false);

		// Create User, assigned to the Role.
		$I->haveUserInDatabase('gravity_forms_user', 'gravity_forms', [
			'user_email' 	=> 'gravity_forms_user@test.local',
			'user_pass' 	=> $_ENV['TEST_SITE_ADMIN_PASSWORD'],
		]);

		// Logout.
		$I->logOut();

		// Login as Gravity Forms users.
		$I->loginAs('gravity_forms_user', $_ENV['TEST_SITE_ADMIN_PASSWORD']);

		// Go to the Settings > Uninstall Screen.
		$I->amOnAdminPage('admin.php?page=gf_settings&subview=uninstall');

		// Confirm that the uninstall option for this Plugin is displayed.
		$I->dontSeeInSource('This operation deletes ALL ConvertKit settings.');
	}

	/**
	 * Deactivate and reset Plugin(s) after each test, if the test passes.
	 * We don't use _after, as this would provide a screenshot of the Plugin
	 * deactivation and not the true test error.
	 * 
	 * @since 	1.2.2
	 * 
	 * @param 	AcceptanceTester 	$I 	Tester
	 */
	public function _passed(AcceptanceTester $I)
	{
		$I->deactivateConvertKitPlugin($I);
		$I->deactivateThirdPartyPlugin($I, 'gravity-forms');
		$I->resetConvertKitPlugin($I);
	}
}
