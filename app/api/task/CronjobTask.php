<?php
class CronjobTask extends \Phalcon\CLI\Task
{
    public function articleViewCountAction()
    {
        $before = microtime(true);

        $cache_name = 'ARTICLE_VIEW_COUNT';
        $article_view_count = $this->cache->get($cache_name);

        $after = microtime(true);
        echo ($after - $before) . " sec\n";
    }
}