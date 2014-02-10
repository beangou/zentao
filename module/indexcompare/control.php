<?php
/**
 * The control file of index module of ZenTaoPMS.
 *
 * When requests the root of a website, this index module will be called.
 *
 * @copyright   Copyright 2009-2013 青岛易软天创网络科技有限公司 (QingDao Nature Easy Soft Network Technology Co,LTD www.cnezsoft.com)
 * @license     LGPL (http://www.gnu.org/licenses/lgpl.html)
 * @author      Chunsheng Wang <chunsheng@cnezsoft.com>
 * @package     ZenTaoPMS
 * @version     $Id: control.php 5036 2013-07-06 05:26:44Z wyd621@gmail.com $
 * @link        http://www.zentao.net
 */
class indexcompare extends control
{
    /**
     * Construct function, load project, product.
     * 
     * @access public
     * @return void
     */
    public function __construct()
    {
        parent::__construct();
    }

    /**
     * The index page of whole zentao system.
     * 
     * @access public
     * @return void
     */
    public function index($orderBy = '')
    {
        //if($this->app->getViewType() == 'mhtml') $this->locate($this->createLink($this->config->locate->module, $this->config->locate->method, $this->config->locate->params));
        //$this->locate($this->createLink('my', 'index'));
    	if(!empty($_POST)) {
    		$proname = $this->post->proname;
    		$empname = $this->post->empname;
    	}
    	
    	if(!$orderBy) $orderBy = $this->cookie->projectTaskOrder ? $this->cookie->projectTaskOrder : 'status,id_desc';
    	setcookie('projectTaskOrder', $orderBy, $this->config->cookieLife, $this->config->webRoot);
    	
    	$selInfo = $this->indexcompare->getIndex($proname, $empname);
    	$this->view->selInfo = $selInfo;
    	
        $this->display();
    }
    
    public function stability($orderBy = '') {
    	if(!empty($_POST)) {
    		$proname = $this->post->proname;
    		$empname = $this->post->empname;
    	}
    	 
    	if(!$orderBy) $orderBy = $this->cookie->projectTaskOrder ? $this->cookie->projectTaskOrder : 'status,id_desc';
    	setcookie('projectTaskOrder', $orderBy, $this->config->cookieLife, $this->config->webRoot);
    	 
    	$selInfo = $this->indexcompare->getIndex($proname, $empname);
    	$this->view->selInfo = $selInfo;
    	 
    	$this->display();
    }
    
    public function completed($orderBy = '') {
    	if(!empty($_POST)) {
    		$proname = $this->post->proname;
    		$empname = $this->post->empname;
    	}
    
    	if(!$orderBy) $orderBy = $this->cookie->projectTaskOrder ? $this->cookie->projectTaskOrder : 'status,id_desc';
    	setcookie('projectTaskOrder', $orderBy, $this->config->cookieLife, $this->config->webRoot);
    
    	$selInfo = $this->indexcompare->getIndex($proname, $empname);
    	$this->view->selInfo = $selInfo;
    
    	$this->display();
    }
    
    public function productivity($orderBy = '') {
    	if(!empty($_POST)) {
    		$proname = $this->post->proname;
    		$empname = $this->post->empname;
    	}
    
    	if(!$orderBy) $orderBy = $this->cookie->projectTaskOrder ? $this->cookie->projectTaskOrder : 'status,id_desc';
    	setcookie('projectTaskOrder', $orderBy, $this->config->cookieLife, $this->config->webRoot);
    
    	$selInfo = $this->indexcompare->getIndex($proname, $empname);
    	$this->view->selInfo = $selInfo;
    
    	$this->display();
    }
    
    public function performance($orderBy = '') {
    	if(!empty($_POST)) {
    		$proname = $this->post->proname;
    		$empname = $this->post->empname;
    	}
    
    	if(!$orderBy) $orderBy = $this->cookie->projectTaskOrder ? $this->cookie->projectTaskOrder : 'status,id_desc';
    	setcookie('projectTaskOrder', $orderBy, $this->config->cookieLife, $this->config->webRoot);
    
    	$selInfo = $this->indexcompare->getIndex($proname, $empname);
    	$this->view->selInfo = $selInfo;
    
    	$this->display();
    }

}
