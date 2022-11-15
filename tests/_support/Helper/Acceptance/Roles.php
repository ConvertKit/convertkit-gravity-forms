<?php
namespace Helper\Acceptance;

/**
 * Helper methods and actions related to WordPress Roles,
 * which are then available using $I->{yourFunctionName}.
 *
 * @since   1.2.1
 */
class Roles extends \Codeception\Module
{
	/**
	 * Helper method add a role to WordPress.
	 *
	 * @since   1.2.5
	 *
	 * @param   AcceptanceTester $I              Tester.
	 * @param   string           $name           Programmatic name of Role.
	 * @param   array            $capabilities   Array of capabilites for the Role.
	 */
	public function addRole($I, $name, $capabilities)
	{
		$roles          = $I->grabOptionFromDatabase('wp_user_roles');
		$roles[ $name ] = [
			'name'         => $name,
			'capabilities' => $capabilities,
		];
		$I->haveOptionInDatabase('wp_user_roles', $roles);
	}

	/**
	 * Helper method delete a role from WordPress.
	 *
	 * @since   1.2.5
	 *
	 * @param   AcceptanceTester $I              Tester.
	 * @param   string           $name           Programmatic name of Role.
	 */
	public function deleteRole($I, $name)
	{
		$roles = $I->grabOptionFromDatabase('wp_user_roles');
		unset($roles[ $name ]);
		$I->haveOptionInDatabase('wp_user_roles', $roles);
	}
}
