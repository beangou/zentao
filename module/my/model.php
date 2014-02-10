<?php
/**
 * The model file of dashboard module of ZenTaoPMS.
 *
 * @copyright   Copyright 2009-2013 青岛易软天创网络科技有限公司 (QingDao Nature Easy Soft Network Technology Co,LTD www.cnezsoft.com)
 * @license     LGPL (http://www.gnu.org/licenses/lgpl.html)
 * @author      Chunsheng Wang <chunsheng@cnezsoft.com>
 * @package     dashboard
 * @version     $Id: model.php 4129 2013-01-18 01:58:14Z wwccss $
 * @link        http://www.zentao.net
 */
?>
<?php
class myModel extends model
{
    /**
     * Set menu.
     * 
     * @access public
     * @return void
     */
    public function setMenu()
    {
    	
        $this->lang->my->menu->account = sprintf($this->lang->my->menu->account, $this->app->user->realname);

        /* Adjust the menu order according to the user role. */
        $role = $this->app->user->role;
        if($role == 'qa')
        {
            unset($this->lang->my->menuOrder[20]);
            $this->lang->my->menuOrder[32] = 'task';
        }
        elseif($role == 'po')
        {
            unset($this->lang->my->menuOrder[35]);
            unset($this->lang->my->menuOrder[20]);
            $this->lang->my->menuOrder[17] = 'story';
            $this->lang->my->menuOrder[42] = 'task';
        }
        elseif($role == 'pm')
        {
            unset($this->lang->my->menuOrder[40]);
            $this->lang->my->menuOrder[17] = 'myProject';
        } 
        
        
        /*二级导航条显示工时和bug数*/
        $account = $this->loadModel('user')->getById($this->app->user->id)->account;
        $role = $this->dao->select('*')->from(TABLE_ICTUSER)->where('account')->eq($account)->fetch();
        if ($role->role !==4){
        	$getDayHours = $this->dao->select('SUM(estimate) as sum')->from(TABLE_TASK)->where('finishedBy')
        	->eq($account)->andWhere('DATE_FORMAT(finishedDate,"%Y-%m")')->eq(date('Y-m',time()))
        	->andWhere('deleted')->eq('0')->fetch();
        	$getBug = $this->dao->select('count(resolvedBy) as count')->from(TABLE_BUG)->where('resolvedBy')
        	->eq($account)->andWhere('DATE_FORMAT(openedDate,"%Y-%m")')->eq(date('Y-m',time()))->andWhere('title')->notLike('%内测%')->groupBy('resolvedBy')->fetch();
        	if (!isset($getDayHours->sum))$hours = 0;
        	else $hours = $getDayHours->sum;
        	if (!isset($getBug->count))$bug = 0;
        	else $bug = $getBug->count;
        	if ($bug<=5)$bug = '<span style="color:green">'.$bug.'</span>';
        	else if (5<$bug && $bug<=10)$bug = '<span style="color:orange">'.$bug.'</span>';
        	else if ($bug > 10)$bug = '<span style="color:red">'.$bug.'</span>';
        	$this->lang->salary->menu->currentReport =
        	sprintf($this->lang->salary->menu->currentReport,'当月工时:<span style="color:#0066cc">'.
        			$hours.'</span>小时   Bug数:'.$bug.'个');
        }
        
        
    }
}
