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

namespace OCA\AdminNotifications\AppInfo;

use OCA\AdminNotifications\Notification\Notifier;
use OCP\AppFramework\App;

class Application extends App {
	const APP_ID = 'admin_notifications';

	public function __construct() {
		parent::__construct(self::APP_ID);
	}

	public function register() {
		$config = $this->getContainer()->getServer()->getConfig();

		if ($config->getAppValue(self::APP_ID, 'published-deprecation-notification', 'no') === 'yes') {
			return;
		}

		$notificationManager = $this->getContainer()->getServer()->getNotificationManager();
		$groupManager = $this->getContainer()->getServer()->getGroupManager();

		$notification = $notificationManager->createNotification();
		$time = time();
		$datetime = new \DateTime();
		$datetime->setTimestamp($time);

		try {
			$notification->setApp(Application::APP_ID)
				->setDateTime($datetime)
				->setObject(Application::APP_ID, dechex($time))
				->setSubject('cli', ['App "admin_notifications" is obsolete'])
				->setMessage('cli', ['The functionality of the "admin_notifications" app has been merged into the default notifications app for Nextcloud 14. You can safely uninstall and delete the "admin_notifications" app, because it does not do anything anymore.']);

			$admins = $groupManager->get('admin');
			foreach ($admins->getUsers() as $admin) {
				$notification->setUser($admin->getUID());
				$notificationManager->notify($notification);
			}
		} catch (\InvalidArgumentException $e) {
			return;
		}

		$config->setAppValue(self::APP_ID, 'published-deprecation-notification', 'yes');
	}
}
