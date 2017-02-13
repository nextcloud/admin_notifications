<?php
/**
 * @copyright Copyright (c) 2017 Joas Schilling <coding@schilljs.com>
 *
 * @author Joas Schilling <coding@schilljs.com>
 *
 * @license GNU AGPL version 3 or any later version
 *
 * This program is free software: you can redistribute it and/or modify
 * it under the terms of the GNU Affero General Public License as
 * published by the Free Software Foundation, either version 3 of the
 * License, or (at your option) any later version.
 *
 * This program is distributed in the hope that it will be useful,
 * but WITHOUT ANY WARRANTY; without even the implied warranty of
 * MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
 * GNU Affero General Public License for more details.
 *
 * You should have received a copy of the GNU Affero General Public License
 * along with this program.  If not, see <http://www.gnu.org/licenses/>.
 *
 */

namespace OCA\AdminNotifications\Tests\AppInfo;


use OCA\AdminNotifications\AppInfo\Application;
use OCA\AdminNotifications\Command\Generate;
use OCA\AdminNotifications\Controller\APIController;
use OCA\AdminNotifications\Notification\Notifier;
use OCP\AppFramework\App;
use OCP\AppFramework\Controller;
use OCP\AppFramework\OCSController;
use OCP\Notification\INotifier;
use Symfony\Component\Console\Command\Command;
use Test\TestCase;

/**
 * Class ApplicationTest
 *
 * @package OCA\AdminNotifications\Tests
 * @group DB
 */
class ApplicationTest extends TestCase {
	/** @var \OCA\AdminNotifications\AppInfo\Application */
	protected $app;

	/** @var \OCP\AppFramework\IAppContainer */
	protected $container;

	protected function setUp() {
		parent::setUp();
		$this->app = new Application();
		$this->container = $this->app->getContainer();
	}

	public function testContainerAppName() {
		$this->app = new Application();
		$this->assertEquals(Application::APP_ID, $this->container->getAppName());
	}

	public function dataContainerQuery() {
		return [
			[Application::class, App::class],

			[APIController::class, Controller::class],
			[APIController::class, OCSController::class],

			[Generate::class, Command::class],
			[Notifier::class, INotifier::class],
		];
	}

	/**
	 * @dataProvider dataContainerQuery
	 * @param string $service
	 * @param string $expected
	 */
	public function testContainerQuery($service, $expected) {
		$this->assertInstanceOf($expected, $this->container->query($service));
	}
}
