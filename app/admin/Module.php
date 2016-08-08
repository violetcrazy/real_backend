<?php
namespace ITECH\Admin;

class Module
{
    public function registerAutoloaders()
    {
        $loader = new \Phalcon\Loader();

        $loader->registerNamespaces(array(
            'ITECH\Data\Model'           => ROOT . '/app/data/model/',
            'ITECH\Data\Repo'            => ROOT . '/app/data/repo/',
            'ITECH\Data\Lib'             => ROOT . '/app/data/lib/',
            'ITECH\Admin\Controller'     => ROOT . '/app/admin/controller/',
            'ITECH\Admin\Component'      => ROOT . '/app/admin/component/',
            'ITECH\Admin\Form'           => ROOT . '/app/admin/form/',
            'ITECH\Admin\Form\Validator' => ROOT . '/app/admin/form/validator/',
            'ITECH\Admin\Lib'            => ROOT . '/app/admin/lib/'
        ));
        $loader->register();
    }

    public function registerServices($di)
    {
        $config = $di->getService('config')->getDefinition();

        $di->setShared('volt', function($view, $di) use ($config) {
            $volt = new \Phalcon\Mvc\View\Engine\Volt($view, $di);

            $volt->setOptions(array(
                'compiledPath'      => ROOT . '/cache/admin/volt/',
                'compiledSeparator' => $config->volt->compiled_separator,
                'compileAlways'     => (bool)$config->volt->debug,
                'stat'              => (bool)$config->volt->stat
            ));

            $compiler = $volt->getCompiler();

            $compiler->addFunction('is_array', 'is_array');
            $compiler->addFunction('in_array', 'in_array');
            $compiler->addFunction('http_build_query', 'http_build_query');
            $compiler->addFunction('strtotime', 'strtotime');
            $compiler->addFunction('date', 'date');
            $compiler->addFunction('json_decode', 'json_decode');
            $compiler->addFunction('explode', 'explode');
            $compiler->addFunction('implode', 'implode');
            $compiler->addFunction('trim', 'trim');

            $compiler->addFunction('getSidebarMenu', function() {
                return '\ITECH\Admin\Lib\Constant::getSidebarMenu()';
            });

            $compiler->addFunction('currencyFormat', function($number) {
                return "\ITECH\Data\Lib\Util::currencyFormat({$number})";
            });

            $compiler->addFunction('hashId', function($number) {
                return "\ITECH\Data\Lib\Util::hashId({$number})";
            });

            $compiler->addFunction('getNameImage', function($path) {
                return "\ITECH\Data\Lib\Util::getNameImage({$path})";
            });

            $compiler->addFunction('getProjectStatus', function() {
                return "\ITECH\Data\Lib\Constant::getProjectStatus()";
            });

            $compiler->addFunction('getBlockStatus', function() {
                return "\ITECH\Data\Lib\Constant::getBlockStatus()";
            });

            $compiler->addFunction('getUserStatus', function() {
                return "\ITECH\Data\Lib\Constant::getUserStatus()";
            });

            $compiler->addFunction('getAttributeLanguage', function() {
                return "\ITECH\Data\Lib\Constant::getAttributeLanguage()";
            });

            $compiler->addFunction('getUserMembership', function() {
                return "\ITECH\Data\Lib\Constant::getUserMembership()";
            });

            $compiler->addFunction('getUserMembershipAdministrator', function() {
                return "\ITECH\Data\Lib\Constant::getUserMembershipAdministrator()";
            });

            $compiler->addFunction('getUserMembershipAgent', function() {
                return "\ITECH\Data\Lib\Constant::getUserMembershipAgent()";
            });

            $compiler->addFunction('getMapView', function() {
                return "\ITECH\Data\Lib\Constant::getMapView()";
            });

            $compiler->addFunction('getDirection', function() {
                return "\ITECH\Data\Lib\Constant::getDirection()";
            });

            $compiler->addFunction('getProjectPropertyType', function() {
                return "\ITECH\Data\Lib\Constant::getProjectPropertyType()";
            });

            $compiler->addFunction('getProjectPropertyView', function() {
                return "\ITECH\Data\Lib\Constant::getProjectPropertyView()";
            });

            $compiler->addFunction('getProjectPropertyUtility', function() {
                return "\ITECH\Data\Lib\Constant::getProjectPropertyView()";
            });

            $compiler->addFunction('getBlockPropertyView', function() {
                return "\ITECH\Data\Lib\Constant::getProjectPropertyView()";
            });

            $compiler->addFunction('getBlockPropertyUtility', function() {
                return "\ITECH\Data\Lib\Constant::getProjectPropertyView()";
            });

            $compiler->addFunction('getCeriterialStatus', function() {
                return "\ITECH\Data\Lib\Constant::getCeriterialStatus()";
            });

            $compiler->addFunction('getCategoryStatus', function() {
                return "\ITECH\Data\Lib\Constant::getCategoryStatus()";
            });

            $compiler->addFunction('getArticleStatus', function() {
                return "\ITECH\Data\Lib\Constant::getArticleStatus()";
            });

            $compiler->addFunction('getGroupStatus', function() {
                return "\ITECH\Data\Lib\Constant::getGroupStatus()";
            });

            $compiler->addFunction('getGroupType', function() {
                return "\ITECH\Data\Lib\Constant::getGroupType()";
            });

            $compiler->addFunction('getBannerStatus', function() {
                return "\ITECH\Data\Lib\Constant::getBannerStatus()";
            });

            $compiler->addFunction('getMessageType', function() {
                return "\ITECH\Data\Lib\Constant::getMessageType()";
            });

            $compiler->addFunction('getMessageStatus', function() {
                return "\ITECH\Data\Lib\Constant::getMessageStatus()";
            });

            $compiler->addFunction('getCeriterialIsHome', function() {
                return "\ITECH\Data\Lib\Constant::getCeriterialIsHome()";
            });

            $compiler->addFunction('getApartmentRequestMethod', function() {
                return "\ITECH\Data\Lib\Constant::getApartmentRequestMethod()";
            });

            $compiler->addFunction('getApartmentRequestStatus', function() {
                return "\ITECH\Data\Lib\Constant::getApartmentRequestStatus()";
            });

            $compiler->addFunction('getApartmentAttributeType', function() {
                return "\ITECH\Data\Lib\Constant::getApartmentAttributeType()";
            });

            $compiler->addFunction('getMapImagePosition', function() {
                return "\ITECH\Data\Lib\Constant::getMapImagePosition()";
            });

            $compiler->addFunction('getMapImageType', function() {
                return "\ITECH\Data\Lib\Constant::getMapImageType()";
            });

            return $volt;
        });

        $di->setShared('view', function() {
            $view = new \Phalcon\Mvc\View();
            $view->setViewsDir(ROOT . '/app/admin/view/');
            $view->registerEngines(array('.volt' => 'volt'));

            return $view;
        });
    }
}
