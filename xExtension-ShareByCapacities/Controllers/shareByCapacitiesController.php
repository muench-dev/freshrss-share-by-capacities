<?php

declare(strict_types=1);

final class FreshExtension_shareByCapacities_Controller extends Minz_ActionController
{
    /** @var ShareByCapacitiesExtension */
    public ?Minz_Extension $extension;

    #[\Override]
	public function init(): void {
		$this->extension = Minz_ExtensionManager::findExtension('Share By Capacities');
	}

    public function shareAction(): void {
        $this->extension->loadConfigValues();

		if (!FreshRSS_Auth::hasAccess()) {
			Minz_Error::error(403);
		}

		$id = Minz_Request::paramString('id');
		if ($id === '') {
			Minz_Error::error(404);
		}

		$entryDAO = FreshRSS_Factory::createEntryDao();
		$entry = $entryDAO->searchById($id);
		if ($entry === null) {
			Minz_Error::error(404);
			return;
		}

		if (!FreshRSS_Context::hasSystemConf()) {
			throw new FreshRSS_Context_Exception('System configuration not initialised!');
		}

        $service = new FreshExtension_ShareByCapacities_Service_Capacities(
            $this->extension->getApiToken(),
            $this->extension->getSpaceId(),
        );

        $ok = $service->shareEntryAsWeblink($entry);

        if ($ok) {
            Minz_Request::good(_t('shareByCapacities.share.feedback.sent'), [
                'c' => 'index',
                'a' => 'index',
            ]);
        } else {
            Minz_Request::bad(_t('shareByCapacities.share.feedback.failed'), [
                'c' => 'shareByEmail',
                'a' => 'share',
                'params' => [
                    'id' => $id,
                ],
            ]);
        }
    }
}