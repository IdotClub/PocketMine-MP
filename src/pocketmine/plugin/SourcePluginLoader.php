<?php

declare(strict_types=1);

namespace pocketmine\plugin;

use function file_exists;
use function file_get_contents;
use function is_dir;

class SourcePluginLoader implements PluginLoader{

	/** @var \ClassLoader */
	private $loader;

	public function __construct(\ClassLoader $loader){
		$this->loader = $loader;
	}

	public function canLoadPlugin(string $path) : bool{
		return is_dir($path) &&
			file_exists($path . "/plugin.yml") &&
			file_exists($path . "/src/");
	}

	/**
	 * Loads the plugin contained in $file
	 */
	public function loadPlugin(string $file) : void{
		/*$composer = "$file/vendor/composer/";
		if (is_dir($composer) && file_exists("$composer/autoload_classmap.php")) {
			$arr = require "$composer/autoload_classmap.php";
			if (is_array($arr)) {
				$classes = array_values($arr);
				foreach ($classes as $class) {
					$this->loader->addPath($class);
				}
			}
		}*/
		$this->loader->addPath("$file/src");
	}

	/**
	 * Gets the PluginDescription from the file
	 */
	public function getPluginDescription(string $file) : ?PluginDescription{
		if(is_dir($file) && file_exists($file . "/plugin.yml")){
			$yaml = @file_get_contents($file . "/plugin.yml");
			if($yaml !== "" && $yaml !== false){
				return new PluginDescription($yaml);
			}
		}

		return null;
	}

	public function getAccessProtocol() : string{
		return "";
	}
}