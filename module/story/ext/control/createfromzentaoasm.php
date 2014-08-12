<?php //0046a
include '../../control.php';

class mystory extends story {
	
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
	public function getStoryTitle($feedbackId = '') {
		$requestTitleStr = ''; 
		
		$dbfile = '../../../../config/ztrackConfig.php';
		$con = @mysql_connect($this->get_config($dbfile, 'ztrackDBhost'), $this->get_config($dbfile, 'ztrackDBuser'), $this->get_config($dbfile, 'ztrackDBpassword'))
		or die("database access error.". $this->get_config($dbfile, 'ztrackDBhost'). ','. $this->get_config($dbfile, 'ztrackDBuser'). ','. $this->get_config($dbfile, 'ztrackDBpassword'));
		
		@mysql_select_db($this->get_config($dbfile, 'ztrackDBname')) //选择数据库mydb
		or die("database not exists.");
		
// 		$con = @mysql_connect('10.152.89.206:3308', 'root', 'ICT-123456@ztdbr')
// 		or die("database access error.");
// 		@mysql_select_db('xirangcsm') //选择数据库mydb
// 		or die("database not exists.");
		@mysql_query("set names 'utf8'");
		
		$requestTitle = @mysql_query("SELECT title FROM zt_request WHERE id = '". $feedbackId. "'");
		if ($rs= @mysql_fetch_array($requestTitle)) {
			$requestTitleStr = $rs[0];
		} 
		@mysql_close($con);
		return $requestTitleStr;
	}
	
	// 创建bug时，向zt_request表中插入zt_bug的id值，进行跟踪
	public function updateStroyId($storyId = '', $feedbackId = '') {
// 		$con = @mysql_connect('10.152.89.206:3308', 'root', 'ICT-123456@ztdbr')
// 		or die("database access error.");
// 		@mysql_select_db('xirangcsm') //选择数据库mydb
// 		or die("database not exists.");

		$dbfile = '../../../../config/ztrackConfig.php';
		$con = @mysql_connect($this->get_config($dbfile, 'ztrackDBhost'), $this->get_config($dbfile, 'ztrackDBuser'), $this->get_config($dbfile, 'ztrackDBpassword'))
		or die("database access error.". $this->get_config($dbfile, 'ztrackDBhost'). ','. $this->get_config($dbfile, 'ztrackDBuser'). ','. $this->get_config($dbfile, 'ztrackDBpassword'));
		
		@mysql_select_db($this->get_config($dbfile, 'ztrackDBname')) //选择数据库mydb
		or die("database not exists.");
		
		@mysql_query("set names 'utf8'");

		$requestTitle = @mysql_query("update zt_request set zentaoId='". $storyId. "', status='storied' WHERE id = '". $feedbackId. "'");
		
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
		$projectID = 0;
		$moduleID = 0;
		$bugID = 0;
		if(!empty($_POST))
        {
            $response['result']  = 'success';
            $response['message'] = '';

            $storyID = $this->story->create($projectID, $bugID);
            if(dao::isError())
            {
                $response['result']  = 'fail';
                $response['message'] = dao::getError();
                $this->send($response);
            }
            // 新需求插入到数据库后，更新ztrack相关数据库
            $this->updateStroyId($storyID, $feedbackID);
            if($bugID == 0)
            {
                $actionID = $this->action->create('story', $storyID, 'Opened', '');
            }
            else
            {
                $actionID = $this->action->create('story', $storyID, 'Frombug', '', $bugID);
            }
            $this->sendMail($storyID, $actionID);
            if($this->post->newStory)
            {
                $response['message'] = $this->lang->story->successSaved . $this->lang->story->newStory;
                $response['locate']  = $this->createLink('story', 'create', "productID=$productID&moduleID=$moduleID&story=0&projectID=$projectID&bugID=$bugID");
                $this->send($response);
            }
            if($projectID == 0)
            {
                $response['locate'] = $this->createLink('story', 'view', "storyID=$storyID");
                $this->send($response);
            }
            else
            {
                $response['locate'] = $this->createLink('project', 'story', "projectID=$projectID");
                $this->send($response);
            }
        }

        /* Set products, users and module. */
        if($productID != 0) 
        {
            $product  = $this->product->getById($productID);
            $products = $this->product->getPairs();
        }
        else
        {
            $products = $this->product->getProductsByProject($projectID); 
            foreach($products as $key => $title)
            {
                $product = $this->product->getById($key);
                break;
            }
        }
        $users = $this->user->getPairs('nodeleted|pdfirst|noclosed');
        $moduleOptionMenu = $this->tree->getOptionMenu($productID, $viewType = 'story');

        /* Set menu. */
        $this->product->setMenu($products, $product->id);

        /* Init vars. */
        $planID     = 0;
        $source     = '';
        $pri        = 0;
        $estimate   = '';
//      $title      = '';
        $title      = $this->getStoryTitle($feedbackID);
        $spec       = $title;
        $verify     = '';
        $keywords   = '';
        $mailto     = '';

        if($storyID > 0)
        {
            $story      = $this->story->getByID($storyID);
            $planID     = $story->plan;
            $source     = $story->source;
            $pri        = $story->pri;
            $productID  = $story->product;
            $moduleID   = $story->module;
            $estimate   = $story->estimate;
            $title      = $story->title;
            $spec       = htmlspecialchars($story->spec);
            $verify     = htmlspecialchars($story->verify);
            $keywords   = $story->keywords;
            $mailto     = $story->mailto;
        }

        if($bugID > 0)
        {
            $oldBug    = $this->loadModel('bug')->getById($bugID);
            $productID = $oldBug->product;
            $source    = 'bug';
            $title     = $oldBug->title;
            $keywords  = $oldBug->keywords;
            $spec      = $oldBug->steps;
            $pri       = $oldBug->pri;
            if(strpos($oldBug->mailto, $oldBug->openedBy) === false) 
            {
                $mailto = $oldBug->mailto . $oldBug->openedBy . ',';
            }
            else
            {
                $mailto = $oldBug->mailto;
            }
        }

        $this->view->title            = $product->name . $this->lang->colon . $this->lang->story->create;
        $this->view->position[]       = html::a($this->createLink('product', 'browse', "product=$productID"), $product->name);
        $this->view->position[]       = $this->lang->story->common;
        $this->view->position[]       = $this->lang->story->create;
        $this->view->products         = $products;
        $this->view->users            = $users;
        $this->view->contactLists     = $this->user->getContactLists($this->app->user->account, 'withnote');
        $this->view->moduleID         = $moduleID;
        $this->view->moduleOptionMenu = $moduleOptionMenu;
        $this->view->plans            = $this->loadModel('productplan')->getPairs($productID, 'unexpired');
        $this->view->planID           = $planID;
        $this->view->source           = $source;
        $this->view->pri              = $pri;
        $this->view->productID        = $productID;
        $this->view->estimate         = $estimate;
        $this->view->storyTitle       = $title;
        $this->view->spec             = $spec;
        $this->view->verify           = $verify;
        $this->view->keywords         = $keywords;
        $this->view->mailto           = $mailto;

        $this->display();
	}
	
}	