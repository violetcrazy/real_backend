<?php
namespace ITECH\Data;

class Module
{
    public function registerAutoloaders()
    {
        $loader = new \Phalcon\Loader();

        $loader->registerNamespaces(array(
            'ITECH\Data\Lib' => ROOT . '/app/data/lib/',
            'ITECH\Data\Model' => ROOT . '/app/data/model/',
            'ITECH\Data\Repo' => ROOT . '/app/data/repo/'
        ));
        $loader->register();
    }

    public function registerServices($di)
    {
    }
}