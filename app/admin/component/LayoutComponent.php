<?php
namespace ITECH\Admin\Component;

class LayoutComponent extends \ITECH\Admin\Component\BaseComponent
{
    public function pagination($theme, array $params)
    {
        $url = $params['url'];
        $query = $params['query'];
        $page = $params['page'];
        $totalPages = $params['total_pages'];
        $pagesDisplay = isset($params['pages_display']) ? $params['pages_display'] : 3;

        $url = $params['url'] . '?' . http_build_query($query);
        $paging = '';

        if ($totalPages > 1) {
            $activePage = (!$page) ? 1 : $page;

            if ($totalPages > $pagesDisplay) {
                if ($activePage < $pagesDisplay) {
                    $min = 1;
                    $max = ($activePage + $pagesDisplay);
                    $max = ($max < $totalPages) ? $max : $totalPages;
                } elseif ($activePage >= $pagesDisplay) {
                    $min = ($activePage - $pagesDisplay);
                    if ($min <= 0) {
                        $min = 1;
                    }

                    $max = ($activePage + $pagesDisplay);
                    $max = ($max < $totalPages) ? $max : $totalPages;

                    if (($totalPages > 10) && (($totalPages - $activePage) < $pagesDisplay)) {
                        $min = $min - ($pagesDisplay - ($totalPages - $activePage));
                    }
                }

                for ($i = $min; $i <= $max; $i++) {
                    $query['page'] = $i;
                    $url = $params['url'] . '?' . http_build_query($query);
                    $paging .= ($i == $activePage) ? '<li><a class="active">' . $i . '</a></li>' : '<li><a href="'. $url .'" title="Trang ' . $i . '">' . $i . '</a></li>';
                }
            } else {
                for ($i = 1; $i <= $totalPages; $i++) {
                    $query['page'] = $i;
                    $url = $params['url'] . '?' . http_build_query($query);
                    $paging .= ($i == $activePage) ? '<li><a class="active" title="Trang '.$i.'">' . $i . '</a></li>' : '<li><a href="' . $url . '" title="Trang ' . $i . '">' . $i . '</a></li>';
                }
            }

            if ($activePage > 1) {
                $query['page'] = $activePage - 1;
                $url = $params['url'] . '?' . http_build_query($query);
                $paging = '<li><a title="Trang trước" href="' . $url .'"><span class="fa fa-angle-left"></a></li>' . $paging;
            }

            if ($activePage < $totalPages) {
                $query['page'] = $activePage + 1;
                $url = $params['url'] . '?' . http_build_query($query);
                $paging .= '<li><a title="Trang kế tiếp" href="'. $url .'"><span class="fa fa-angle-right"></a></li>';

                $query['page'] = $totalPages;
                $url = $params['url'] . '?' . http_build_query($query);
                $paging .= '<li><a title="Trang cuối" href="'. $url .'"><span class="fa fa-angle-double-right"></span></a></li>';
            }

            $query['page'] = 1;
            $url = $params['url'] . '?' . http_build_query($query);
            $paging = '<li><a title="Trang đầu tiên" href="' . $url . '"><span class="fa fa-angle-double-left"></span></a></li>' . $paging;
        }

        $this->view->start();
        $this->view->setVars(array(
            'totalPages' => $totalPages,
            'paging' => $paging
        ));
        $this->view->render($theme . '/component/layout/', 'pagination');
        $this->view->finish();

        return $this->view->getContent();
    }
}