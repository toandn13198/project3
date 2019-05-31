<?php
/**
 * Created by PhpStorm.
 * User: dell
 * Date: 3/1/2019
 * Time: 12:46 AM
 */
//required bootstrap 4 ^
class Paginator
{
    public $total;
    public $page_num;
    public $limit;
    public $page_current;
    public $page_max = 8;
    public $url;
    public $params; //params in url that need add
    public function __construct($config)
    {
        $this->total = $config['total'];
        ($config['limit'] < 0) ? $this->limit = 0 : $this->limit = $config['limit'];
        $this->page_num = ceil($this->total / $this->limit);
        $this->page_current = ($config['page_current'] > $this->page_num) ? $this->page_num : $config['page_current'];
        if($this->page_current < 1){
            $this->page_current = 1;
        }
        $this->url = $config['url'];
        $this->params = (isset($config['params'])) ? $config['params']: null ;
    }

    public function getPage(){
        return $this->page_current;
    }

    public function getLimit(){
        return $this->limit;
    }

    public function getTotalRecord(){
        return $this->total;
    }

    public function getNextPage(){
        if($this->page_current < $this->page_num){
            return $this->page_current +1;
        }else{
            return $this->page_current;
        }
    }

    public function getPreviousPage(){
        if($this->page_current > 1){
            return $this->page_current - 1;
        }else{
            return $this->page_current;
        }
    }

    public function buildUrl($page){
        $this->params['page'] = $page;
        return $this->url . '?' . http_build_query($this->params);
    }

    public function createPage($page, $is_current){
        return [
            'page'  =>  $page,
            'is_current'    =>  $is_current,
            'url'   =>  $this->buildUrl($page)
        ];
    }

    public function createPrevPage(){
        return [
            'page'  =>  'Prev',
            'is_current'    =>  false,
            'url'   =>  $this->buildUrl($this->getPreviousPage())
        ];
    }

    public function createNextPage(){
        return [
            'page'  =>  'Next',
            'is_current'    =>  false,
            'url'   =>  $this->buildUrl($this->getNextPage())
        ];
    }

    public function createPageNoLink(){
        return [
            'page'  =>  '...',
            'is_current'    =>  false,
            'url'   =>  'javascript:void(0)'
        ];
    }

    public function getDataPages(){
        $data = [];
        if($this->page_num <= 1){
            return [$this->createPage( 1, true)];
        }

        if($this->page_num <= $this->page_max){
            for($i=1; $i<= $this->page_num; $i++){
                $data[] = $this->createPage($i, ($i == $this->page_current));
            }
        }else{
            $page_side =  floor(($this->page_max - 3) / 2);

            if ($this->page_current + $page_side > $this->page_num) {
                $start = $this->page_num - $this->page_max + 2;
            } else {
                $start = $this->page_current - $page_side;
            }
            if ($start < 2) $start = 2;
            $end = $start + $this->page_max - 3;

            if ($end >= $this->page_num) $end = $this->page_num - 1;
            if ($start > 2) $data[] = $this->createPrevPage();
            $data[] = $this->createPage(1, ($this->page_current == 1));
            if ($start > 2) $data[] = $this->createPageNoLink();

            for ($i = $start; $i <= $end; $i++) {
                $data[] = $this->createPage($i, ($i == $this->page_current));
            }

            if ($end < $this->page_num - 1) $data[] = $this->createPageNoLink();
            $data[] = $this->createPage($this->page_num, ($this->page_current == $this->page_num));
            if ($end < $this->page_num - 1) $data[] = $this->createNextPage();
        }
        return $data;

    }

    public function getPagination(){
        $html = "<nav aria-label=''>
                    <ul class='pagination justify-content-end' style='margin:20px 0'>
                    <li class='page-item disabled'><a class='page-link font-weight-bold border-0'>Page " . $this->page_current . '/' . $this->page_num .
                    "</a></li>";

        $data_pages = $this->getDataPages();
        foreach ($data_pages as $page){
            $html.="<li class='page-item " . (($page['is_current']) ? 'active' : '') ."'>
                      <a class='page-link' href='" . $page['url'] . "'>" . $page['page'] . "</a>
                    </li>";
        }

        $html.= "   </ul>
                </nav>";
        return $html;
    }

}