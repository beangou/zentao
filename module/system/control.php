<?php 

class system extends control
{
	public function __construct()
	    {
	        parent::__construct();
	        $this->loadModel('user');
	        $this->loadModel('dept');
	    }
	    
	public function index()
	{
		$this->locate($this->createLink('system','personnel'));
		$this->display();
	}
	public function personnel($typeID = 2, $account = ''){
		$this->view->title		= $this->lang->system->common . $this->lang->colon . $this->lang->system->personnel;
		if (isset($_POST['typeID']))$typeID = $_POST['typeID'];
		if (isset($_POST['account'])){
			$account=$_POST['account'];
			$this->view->account = $_POST['account'];
		}
		if ($typeID == 2){
			$this->view->userInfo	= $this->system->getUserInfo($account);
			$this->view->ztUser		= $this->system->queryZtUser($account);
		}
		if (isset($_POST['accounts']))$this->system->sysnchronous();
		if (isset($_POST['account']))$this->view->singleInfo = $this->system->querySingleInfo($_POST['account']);
		if ($account){
			$this->view->singleInfo = $this->system->querySingleInfo($account);
		}
		if (isset($_POST['loginAccount'])){
			$this->system->setStandSalary();
			if(dao::isError()) die(js::error(dao::getError()));
		}
		$this->view->typeID	= $typeID;
		$this->display();
	}
	/****参数设定***/
	public function parameter($typeID=0)
	{
		$month = date('Y-m-d',time());
		if (isset($_POST['month'])){
			$month = $_POST['month'];
		}
		if (isset($_POST['typeID']))$typeID = $_POST['typeID'];
		$this->view->typeID	= $typeID;
		$this->view->month	= $month;
		if ($typeID==0 || $typeID==1){
			$DMGroups	= $this->system->queryDM();
			$productGroups	= $this->system->queryProduct();
			$DMList	= array('' => '');
			$productList = array('' => '');
			foreach ($DMGroups as $group)
			{
				$DMList[$group->account] = $group->realname;
			}
			foreach ($productGroups as $groups)
			{
				$productList[$groups->id] = $groups->name;
			}
			
			$projects = $this->loadModel('project')->getPairs();
			$projects = array($this->lang->project->select) + $projects;
			$this->view->projects = $projects;
			$this->view->DMList	= $DMList;
			$this->view->productList = $productList;
			$products = $this->loadModel('product')->getPairs();
			$products = array($this->lang->product->select) + $products;
			$this->view->products = $products;
			if (isset($_POST['productId'])){
				$this->system->saveIctProduct();
			}
			if (isset($_POST['ids']) && !isset($_POST['productID'])){
				$this->system->updateProduct();
			}
			if (isset($_POST['productID'])){
				$this->system->deleteProduct($_POST['productID']);
			}
			$this->view->productInfo = $this->system->queryProductInfo($month);
		}
// 		if ($productID>1){
// 			echo $productID;
// 		$projects = $this->dao->select('p.id,p.name FROM zt_project p,zt_product j,zt_projectproduct z')
// 			->where('p.id=z.project')->andWhere(' z.product=j.id')->andWhere('j.id')->eq($productID)->fetchAll();
// 		$pairs = array();
// 		foreach ($projects as $project){
// 			$pairs[$project->id] = $project->name;
// 		}
// 		}
		if (isset($_POST['increaseName'])){
			$this->system->saveIncrease($month);
		}
		if (isset($_POST['name'])){
			$this->system->saveRewards($month);
		}
			$this->view->rewards	= $this->system->queryBug($month);
			$this->view->increaseS	= $this->system->queryIncrease($month);
		$this->display();
	}
	
	public function ajaxGetProjects($productID)
	{
		$projects = $this->loadModel('project')->getPairs();
		$projects = array($this->lang->project->select) + $projects;
		$this->view->projects = $projects;
	}
	public function batchDelete(){
		a($_POST);
		if ($this->post->ids){
			$ids = $this->post->ids;
			unset($_POST['ids']);
			$this->loadModel('action');
			foreach($ids as $id)
			{
				$this->system->deleteProduct($id);
			}
		}
		die(js::reload('parent'));
	}
	
	/**
	 * Delete a user.
	 *
	 * @param string account
	 * @access public
	 * @return void
	 */
	public function delete($page = '', $account = '', $id = '')
	{
		if ($page == 'person'){
			$this->system->delete($account);
			die(js::locate($this->createLink('system','personnel'), 'parent'));
		}else if ($page == 'param'){
			 $this->system->deleteProduct($id);
			 die(js::locate($this->createLink('system','parameter'), 'parent'));
		}
	}

	/**
	 * Edit a user
	 * @param int typeID
	 * @param string account
	 * @access public
	 * @return void
	 */
	public function edit($typeID = 1, $account = ''){
		die(js::locate($this->createLink('system','personnel',"typeID=$typeID&account=$account"), 'parent'));
	}
	
}