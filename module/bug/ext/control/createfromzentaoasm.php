<?php //0046a
include '../../control.php';

class mybug extends bug {
	
	// 读取配置文件的相关值
	public function get_config($file, $ini, $type="string"){
		if(!file_exists($file)) {
			echo 'file not exist';
			return false;
		}
		$str = file_get_contents($file);
		if ($type=="int"){
			$config = preg_match("/".preg_quote($ini)."=(.*);/", $str, $res);
			return $res[1];
		}
		else{
			$config = preg_match("/".preg_quote($ini)."=\"(.*)\";/", $str, $res);
			if($res[1]==null){
				$config = preg_match("/".preg_quote($ini)."='(.*)';/", $str, $res);
			}
			return $res[1];
		}
	}
	
	// 根据feedbackID获取问题的标题
	public function getBugTitle($feedbackId = '') {
		$requestTitleStr = '';
// 		$dbfile = 'D:/www/zentaoZtrack/config/ztrackDB.php';
		$dbfile = '../../../../config/ztrackConfig.php'; 
// 		$dbfile = '../../../../../config/ztrackDB.php';
// 		$con = @mysql_connect('10.152.89.206:3308', 'root', 'ICT-123456@ztdbr')
// 		or die("database access error.");
		$con = @mysql_connect($this->get_config($dbfile, 'ztrackDBhost'), $this->get_config($dbfile, 'ztrackDBuser'), $this->get_config($dbfile, 'ztrackDBpassword'))
		or die("database access error.". $this->get_config($dbfile, 'ztrackDBhost'). ','. $this->get_config($dbfile, 'ztrackDBuser'). ','. $this->get_config($dbfile, 'ztrackDBpassword'));
		
		@mysql_select_db($this->get_config($dbfile, 'ztrackDBname')) //选择数据库mydb
		or die("database not exists.");
		@mysql_query("set names 'utf8'");
		
		$requestTitle = @mysql_query("SELECT title FROM zt_request WHERE id = '". $feedbackId. "'");
		if ($rs= @mysql_fetch_array($requestTitle)) {
			$requestTitleStr = $rs[0];
		} 
		
		@mysql_close($con);
		return $requestTitleStr;
	}
	
	// 创建bug时，向zt_request表中插入zt_bug的id值，进行跟踪
	public function updateBugId($bugId = '', $feedbackId = '') {
// 		$con = @mysql_connect('10.152.89.206:3308', 'root', 'ICT-123456@ztdbr')
// 		or die("database access error.");
// 		@mysql_select_db('xirangcsm') //选择数据库mydb
// 		or die("database not exists.");
		
		$dbfile = '../../../../config/ztrackConfig.php';
		// 		$con = @mysql_connect('10.152.89.206:3308', 'root', 'ICT-123456@ztdbr')
		// 		or die("database access error.");
		
		$con = @mysql_connect($this->get_config($dbfile, 'ztrackDBhost'), $this->get_config($dbfile, 'ztrackDBuser'), $this->get_config($dbfile, 'ztrackDBpassword'))
		or die("database access error.". $this->get_config($dbfile, 'ztrackDBhost'). ','. $this->get_config($dbfile, 'ztrackDBuser'). ','. $this->get_config($dbfile, 'ztrackDBpassword'));
		
		@mysql_select_db($this->get_config($dbfile, 'ztrackDBname')) //选择数据库mydb
		or die("database not exists.");
		
		@mysql_query("set names 'utf8'");

		$requestTitle = @mysql_query("update zt_request set zentaoId='". $bugId. "', status='buged' WHERE id = '". $feedbackId. "'");
		
		@mysql_close($con);
	}
	
	/**
	 * Create a bug.
	 *
	 * @param  int    $productID
	 * @param  string $extras       others params, forexample, projectID=10,moduleID=10
	 * @access public
	 * @return void
	 */
// 	public function createfromzentaoasm($productID, $extras = '', $requestID = '')
	public function createfromzentaoasm($productID = '', $feedbackID = '')
	{
		$this->view->users = $this->user->getPairs('nodeleted,devfirst');
		if(empty($this->products)) $this->locate($this->createLink('product', 'create'));
		$this->app->loadLang('release');

		if(!empty($_POST))
		{
			$response['result']  = 'success';
			$response['message'] = '';

			$bugID = $this->bug->create();
				
			//创建完bug后，再向ztrack反映、
// 			$this->sendZtrackData('aaaaaaaaaa');
				
			if(dao::isError())
			{
				$response['result']  = 'fail';
				$response['message'] = dao::getError();
				$this->send($response);
			}
			
			$this->updateBugId($bugID, $feedbackID);

			$actionID = $this->action->create('bug', $bugID, 'Opened');
			$this->sendmail($bugID, $actionID);

			$location = $this->createLink('bug', 'browse', "productID={$this->post->product}&type=byModule&param={$this->post->module}");
			$response['locate'] = isset($_SESSION['bugList']) ? $this->session->bugList : $location;
			$this->send($response);
				
		}

		/* Get product, then set menu. */
		$productID = $this->product->saveState($productID, $this->products);
		$this->bug->setMenu($this->products, $productID);

		/* Remove the unused types. */
		unset($this->lang->bug->typeList['designchange']);
		unset($this->lang->bug->typeList['newfeature']);
		unset($this->lang->bug->typeList['trackthings']);

		/* Init vars. */
		$moduleID   = 0;
		$projectID  = 0;
		$taskID     = 0;
		$storyID    = 0;
		$buildID    = 0;
		$caseID     = 0;
		$runID      = 0;
// 		$title      = '';
		$title      = $this->getBugTitle($feedbackID);
		$steps      = $this->lang->bug->tplStep . $this->lang->bug->tplResult . $this->lang->bug->tplExpect;
		$os         = '';
		$browser    = '';
		$assignedTo = '';
		$mailto     = '';
		$keywords   = '';
		$severity   = 3;
		$type       = 'codeerror';

		/* Parse the extras. */
		$extras = str_replace(array(',', ' '), array('&', ''), $extras);
		parse_str($extras);

		/* If set runID, get the last result info as the template. */
		if($runID > 0) $resultID = $this->dao->select('id')->from(TABLE_TESTRESULT)->where('run')->eq($runID)->orderBy('id desc')->limit(1)->fetch('id');
		if(isset($resultID) and $resultID > 0) extract($this->bug->getBugInfoFromResult($resultID));

		/* If set caseID and runID='', get the last result info as the template. */
		if($caseID > 0 && $runID == '')
		{
			$resultID = $this->dao->select('id')->from(TABLE_TESTRESULT)->where('`case`')->eq($caseID)->orderBy('date desc')->limit(1)->fetch('id');
			if(isset($resultID) and $resultID > 0) extract($this->bug->getBugInfoFromResult($resultID, $caseID, $version));
		}

		/* If bugID setted, use this bug as template. */
		if(isset($bugID))
		{
			$bug = $this->bug->getById($bugID);
			extract((array)$bug);
			$projectID = $bug->project;
			$moduleID  = $bug->module;
			$taskID    = $bug->task;
			$storyID   = $bug->story;
			$buildID   = $bug->openedBuild;
			$severity  = $bug->severity;
			$type      = $bug->type;
		}

		/* If projectID is setted, get builds and stories of this project. */
		if($projectID)
		{
			$builds  = $this->loadModel('build')->getProjectBuildPairs($projectID, $productID, 'noempty');
			$stories = $this->story->getProjectStoryPairs($projectID);
		}
		else
		{
			$builds  = $this->loadModel('build')->getProductBuildPairs($productID, 'noempty,release');
			$stories = $this->story->getProductStoryPairs($productID);
		}

		$this->view->title      = $this->products[$productID] . $this->lang->colon . $this->lang->bug->create;
		$this->view->position[] = html::a($this->createLink('bug', 'browse', "productID=$productID"), $this->products[$productID]);
		$this->view->position[] = $this->lang->bug->create;

		$this->view->productID        = $productID;
		$this->view->productName      = $this->products[$productID];
		$this->view->moduleOptionMenu = $this->tree->getOptionMenu($productID, $viewType = 'bug', $startModuleID = 0);
		$this->view->stories          = $stories;
		$this->view->projects         = $this->product->getProjectPairs($productID, $params = 'nodeleted');
		$this->view->builds           = $builds;
		$this->view->tasks            = $this->loadModel('task')->getProjectTaskPairs($projectID);
		$this->view->moduleID         = $moduleID;
		$this->view->projectID        = $projectID;
		$this->view->taskID           = $taskID;
		$this->view->storyID          = $storyID;
		$this->view->buildID          = $buildID;
		$this->view->caseID           = $caseID;
		$this->view->bugTitle         = $title;
		$this->view->steps            = htmlspecialchars($steps);
		$this->view->os               = $os;
		$this->view->browser          = $browser;
		$this->view->assignedTo       = $assignedTo;
		$this->view->mailto           = $mailto;
		$this->view->contactLists     = $this->user->getContactLists($this->app->user->account, 'withnote');
		$this->view->keywords         = $keywords;
		$this->view->severity         = $severity;
		$this->view->type             = $type;

		$this->display();
	}
	
}	