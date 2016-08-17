<?php
namespace ITECH\Admin\Component;

class LinkComponent extends \ITECH\Admin\Component\BaseComponent
{
    public function sub($params)
    {
        $link = \ITECH\Data\Model\LinkModel::find(array(
            'conditions' => 'group_id = :group_id: and parent_id = :parent_id:',
            'bind' => array(
                    'group_id' => $params['conditions']['group_id'],
                    'parent_id' => $params['conditions']['parent_id']
                ),
            'order' => 'ordering ASC'
        ));
        
        if (count($link) > 0) {
            $subLinkLayout = '<ol class="dd-list">';
            foreach ($link as $item) {
                $query = array(
                'id' => $item->id
                );
                    
                $subLinkLayout .= '<li class="dd-item dd3-item" data-id="' . $item->id . '">
                                        <div class="dd-handle dd3-handle"></div>
                                        <div class="dd3-content">
                                            ' . $item->name . '
                                            <div class="visible-md visible-lg hidden-sm hidden-xs float-right">
                                                <a class="btn btn-squared btn-xs btn-primary tooltips" data-original-title="Sửa" data-placement="top" href="' 
                                                    . $this->url->get(array('for' => 'category_edit_link', 'query' =>'?' . http_build_query($query))) . '">
                                                    <i class="fa fa-edit"></i>
                                                </a>
                                                <a class="btn btn-squared btn-xs btn-primary tooltips" data-original-title="Xóa" data-placement="top" href="' 
                                                    . $this->url->get(array('for' => 'category_delete_link', 'query' =>'?' . http_build_query($query))) . '"' . 'onclick="javascript:return confirm(\'Đồng ý xoá?\');"' . '>
                                                    <i class="fa fa-times fa fa-white"></i>
                                                </a>
                                            </div>
                                        </div>
                                    ';
                $params = array(
                    'conditions' => array(
                        'group_id' => $params['conditions']['group_id'],
                        'parent_id' => $item->id
                    )
                );
                $subLinkLayout .= $this->sub($params);
                $subLinkLayout .= '</li>';
            }
            
            $subLinkLayout .= '</ol>'; 
        } else {
            $subLinkLayout = '';
        }
        
        return $subLinkLayout;
    }
}
