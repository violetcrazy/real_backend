<?php
namespace ITECH\Api;

class Module
{
    public function registerAutoloaders()
    {
        $loader = new \Phalcon\Loader();

        $loader->registerNamespaces(array(
            'ITECH\Api\Controller' => ROOT . '/app/api/controller/',
            'ITECH\Api\Form' => ROOT . '/app/api/form/',
            'ITECH\Api\Form\Validator' => ROOT . '/app/api/form/validator/',
            'ITECH\Data\Lib' => ROOT . '/app/data/lib/',
            'ITECH\Data\Model' => ROOT . '/app/data/model/',
            'ITECH\Data\Repo' => ROOT . '/app/data/repo/'
        ));
        $loader->register();
    }

    public function registerServices($di)
    {
        $config = $di->getService('config')->getDefinition();

        $di->setShared('volt', function($view, $di) use ($config) {
            $volt = new \Phalcon\Mvc\View\Engine\Volt($view, $di);

            $volt->setOptions(array(
                'compiledPath' => ROOT . '/cache/api/volt/',
                'compiledSeparator' => $config->volt->compiled_separator,
                'compileAlways' => (bool)$config->volt->debug,
                'stat' => (bool)$config->volt->stat
            ));

            return $volt;
        });

        $di->setShared('view', function() {
            $view = new \Phalcon\Mvc\View();
            $view->setViewsDir(ROOT . '/app/api/view/');
            $view->registerEngines(array(
                '.volt' => 'volt'
            ));

            return $view;
        });
    }
}