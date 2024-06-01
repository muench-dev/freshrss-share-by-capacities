<?php

declare(strict_types=1);

/**
 * Entrypoint of extension
 * 
 * Inspired by xExtension-ShareByEmail
 */
final class ShareByCapacitiesExtension extends Minz_Extension 
{
	private string $apiToken = '';
	private string $spaceId = '';


	#[\Override]
	public function init(): void {
		$this->registerTranslates();
		$this->registerController('shareByCapacities');

		FreshRSS_Share::register([
			'type' => 'capacities',
			'url' => Minz_Url::display(['c' => 'shareByCapacities', 'a' => 'share']) . '&amp;id=~ID~',
			'transform' => [],
			'form' => 'simple',
			'method' => 'GET',
		]);

		spl_autoload_register(array($this, 'loader'));
	}

	public function loader(string $class_name): void {
		/**
		 * Add an PSR-4 autoloader for the nameSPACE "FreshExtension_ShareByCapacities_".
		 * Load from the extension directory.
		 * Register it with spl_autoload_register()
		 */
		if (strpos($class_name, 'FreshExtension_ShareByCapacities_') === 0) {
			// it should work with Models, Service, ...
			$filename = str_replace('_', '/', $class_name);
			$filename = substr($filename, strlen('FreshExtension_ShareByCapacities_'));
			$filename = __DIR__ . '/' . $filename . '.php';
			if (file_exists($filename)) {
				require_once $filename;
			}
		}
			
		
	}

	/**
     * This function is called by FreshRSS when the configuration page is loaded, and when configuration is saved.
     *  - We save configuration in case of a post.
     *  - We (re)load configuration in all case, so they are in-sync after a save and before a page load.
     */
	#[\Override]
    public function handleConfigureAction(): void
	{
		$this->registerTranslates();

		if (Minz_Request::isPost()) {
			FreshRSS_Context::userConf()->_attribute('shareByCapacities_apiToken', Minz_Request::paramString('api_token'));
			FreshRSS_Context::userConf()->_attribute('shareByCapacities_spaceId', Minz_Request::paramString('space_id'));
			FreshRSS_Context::userConf()->save();
		}

		$this->loadConfigValues();
	}

	/**
     * Initializes the extension configuration, if the user context is available.
     * Do not call that in your extensions init() method, it can't be used there.
     */
    public function loadConfigValues(): void
    {
        if (!class_exists('FreshRSS_Context', false) || !FreshRSS_Context::hasUserConf()) {
            return;
        }

		$apiToken = FreshRSS_Context::userConf()->attributeString('shareByCapacities_apiToken');
		if ($apiToken !== null) {
			$this->apiToken = $apiToken;
		}

		$spaceId = FreshRSS_Context::userConf()->attributeString('shareByCapacities_spaceId');
		if ($spaceId !== null) {
			$this->spaceId = $spaceId;
		}
	}

	public function getApiToken(): string
	{
		return $this->apiToken ?? '';
	}

	public function getSpaceId(): string
	{
		return $this->spaceId ?? '';
	}
}
