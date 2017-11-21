<?php
namespace aliuly\grabbag\common;

use pocketmine\plugin\Plugin;
use pocketmine\permission\Permission;

/**
 * Simple class encapsulating some Permission related utilities
 */
abstract class PermUtils{
	/**
	 * Register a permission on the fly...
	 * @param Plugin $plugin - owning plugin
	 * @param string $name - permission name
	 * @param string $desc - permission description
	 * @param string $default - one of true,false,op,notop
	 */
	static public function add(Plugin $plugin, $name, $desc, $default){
		$perm = new Permission($name, $desc, $default);
		$plugin->getServer()->getPluginManager()->addPermission($perm);
	}
}
