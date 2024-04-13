<?php

namespace Nanicas\LegacyLaravelToolkit\Http\Controllers\Pages;

use Nanicas\LegacyLaravelToolkit\Services\HomeService;
use Nanicas\LegacyLaravelToolkit\Helpers\Helper;

class_alias(Helper::readTemplateConfig()['controllers']['dashboard'],  __NAMESPACE__ . '\DashboardControllerAlias');

class HomeController extends DashboardControllerAlias
{
    public function __construct(HomeService $homeService)
    {
        parent::__construct();

        $this->setService($homeService);
    }

    public function index()
    {
        $this->addIndexAssets();
        $this->beforeView();

        $data = $this->getService()->getIndexData();
        $packaged = $this->isPackagedView();

        return Helper::view('pages.home.index', $data, $packaged);
    }

}
