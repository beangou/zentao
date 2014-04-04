<?php
//require 'control.php';
//$planControl = new plan();
//$planControl->sendEmailNew("1289188692@qq.com");

	require_once('class.phpmailer.php'); //载入PHPMailer类

	// 汇总计划 入口
	function collectMyplan() {
		if ('0' == checkCollect()) {
			sendMsg();
		} else if ('0' == checkCollect()) {
			sendMsg();
		} else {
			generateExcl();
			sendEmail($email);
		}		 
	}
	
	//验证所有组长、技术经理的下周计划是否已审核，并且通过
	//有计划未审核，返回0
	//有计划未通过，返回1
	//验证通过，返回2
	function checkCollect() {
		$result = 0;
		global $config;
		
		$con = @mysql_connect($config->db->host, $config->db->user, $config->db->password)
		or die("数据库服务器连接失败");
		@mysql_select_db($config->db->name) //选择数据库mydb
		or die("数据库不存在或不可用");
		
		//查看各组长、技术经理的审核未通过计划
		$leaderPlans = @mysql_query("SELECT T1.account, T4.realname, T1.firstDayOfWeek, T1.lastDayOfWeek FROM ict_my_weekplan T1
						LEFT JOIN ict_membset T2 ON (T2.account = T1.account)
						LEFT JOIN ict_audit T3 ON (T3.id = T1.auditId)
						LEFT JOIN zt_user T4 ON (T4.account = T1.account)
						WHERE T2.leader != '0'
						AND T3.result = '不同意'
						GROUP BY T1.account, T1.firstDayOfWeek
						ORDER BY T1.account, T1.firstDayOfWeek") or die("组长情况 SQL语句执行失败");
		
		//如果有审核未通过， 即发送短息通知
		while($rs= @mysql_fetch_array($leaderPlans)){
			sendMsg(getContactStyle($rs[0])->mobile, '您好,'. $rs[1]. '!'. '您的禅道周计划('. $rs[2]. '~'. $rs[3]. ')审核未通过， 请尽快修改并提交.');
			$result = 1;
		}

		//查看科长审核组长、技术经理计划情况
		$chiefPlans = @mysql_query("SELECT T1.account, T4.realname FROM ict_my_weekplan T1
						LEFT JOIN ict_membset T2 ON (T2.account = T1.account)
						LEFT JOIN zt_user T4 ON (T4.account = T1.account)
						WHERE T2.leader != '0'
						AND T1.auditId IS NULL") or die("科长情况 SQL语句执行失败");
		
		if (!empty($chiefPlans)) {
			sendMsg(getContactStyle('chenxiaobo')->mobile, '您好，你还有周计划未审核， 请登录禅道至 我的地盘->我的计划->我的审核 进行审核');	
			$result = 1;
		}
		
		// 所有计划都OK， 进行汇总
		if ($result == 0) {
			generateExcl();
			sendEmail($email);
		}
		
		mysql_close($con);
		
	}
	
	//根据用户名，获取联系方式（邮箱和手机号码）
	function getContactStyle($account) {
		global $config;
		$styleArr = array();
		
		$con = @mysql_connect($config->db->host, $config->db->user, $config->db->password)
		or die("数据库服务器连接失败");
		@mysql_select_db($config->db->name) //选择数据库mydb
		or die("数据库不存在或不可用");
		
		//查看各组长、技术经理的审核未通过计划
		$leaderPlans = @mysql_query("SELECT email, mobile FROM zt_user where account = '". $account. "'") or die("组长情况 SQL语句执行失败");
		
		if ($rs= @mysql_fetch_array($leaderPlans)) {
			$styleArr->email = $rs[0];
			$styleArr->mobile = $rs[1];
		}
		
		mysql_close($con);
		return $styleArr;
	}
	
	//如果不通过：
	//(1)如果科长有的计划未审核，发送短信到科长
	//(2)如果有的组长、技术经理计划未通过，发送短信到组长、技术经理
	function sendMsg($phoneNo, $content) {
		
	}			
	
	
// 	sendEmail('liu.tongbin@ustcinfo.com');

	/**
	 * 如果所有组长均审核并通过  计划汇总 即生成excl 并发送到指定邮件
	 */
    function sendEmail($email)	
	{
		$mail = new PHPMailer(); //实例化
		$mail->IsSMTP(); // 启用SMTP
		$mail->Host = "smtp.163.com"; //SMTP服务器 以163邮箱为例子
		$mail->Port = 25;  //邮件发送端口
		$mail->SMTPAuth   = true;  //启用SMTP认证
		
		$mail->CharSet  = "utf-8"; //字符集
		$mail->Encoding = "base64"; //编码方式
		
		$mail->Username = "15955552919@163.com";  //你的邮箱
		$mail->Password = "abcde12345";  //你的密码
		$mail->Subject = "ict周计划汇总"; //邮件标题
		
		$mail->From = "15955552919@163.com";  //发件人地址（也就是你的邮箱）
		$mail->FromName = "ict禅道系统";  //发件人姓名
		
// 		$address = "15955552919@163.com";//收件人email
		$address = $email;
		$mail->AddAddress($address, "ict周计划审核人");//添加收件人（地址，昵称）
		
		$mail->AddAttachment('/opt/lampp/testCrontab/sendmail/control.xls','ict周计划汇总.xls'); // 添加附件,并指定名称
		$mail->IsHTML(true); //支持html格式内容
		
		$mail->Body = '您好! <br/>这是一封来自安徽移动ict禅道系统的邮件！<br/>'; //邮件主体内容
		
		// $mail->AddEmbeddedImage("logo.jpg", "my-attach", "logo.jpg"); //设置邮件中的图片
// 		$mail->Body = '你好, <b>朋友</b>! <br/>这是一封来自<a href="http://www.helloweba.com"
// 		target="_blank">helloweba.com</a>的邮件！<br/>
// 		<img alt="helloweba" src="cid:my-attach">'; //邮件主体内容
		
		//发送
		if(!$mail->Send()) {
// 			echo "Mailer Error: " . $mail->ErrorInfo;
			return $mail->ErrorInfo;
		} else {
// 			echo "Message sent!";
			return '<script>alert("发送成功!")</script>';
		}
    }
	
	
	
	/**
	 * 生成excl文件
	 */
	function generateExcl($nextWeekFirstDay, $thisWeekFirstDay) 
	{
		/**
		 * PHPExcel
		 *
		 * Copyright (C) 2006 - 2014 PHPExcel
		 *
		 * This library is free software; you can redistribute it and/or
		 * modify it under the terms of the GNU Lesser General Public
		 * License as published by the Free Software Foundation; either
		 * version 2.1 of the License, or (at your option) any later version.
		 *
		 * This library is distributed in the hope that it will be useful,
		 * but WITHOUT ANY WARRANTY; without even the implied warranty of
		 * MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the GNU
		 * Lesser General Public License for more details.
		 *
		 * You should have received a copy of the GNU Lesser General Public
		 * License along with this library; if not, write to the Free Software
		 * Foundation, Inc., 51 Franklin Street, Fifth Floor, Boston, MA  02110-1301  USA
		 *
		 * @category   PHPExcel
		 * @package    PHPExcel
		 * @copyright  Copyright (c) 2006 - 2014 PHPExcel (http://www.codeplex.com/PHPExcel)
		 * @license    http://www.gnu.org/licenses/old-licenses/lgpl-2.1.txt	LGPL
		 * @version    1.8.0, 2014-03-02
		 */
		
		/** Error reporting */
		error_reporting(E_ALL);
		ini_set('display_errors', TRUE);
		ini_set('display_startup_errors', TRUE);
		date_default_timezone_set('Europe/London');
		
		define('EOL',(PHP_SAPI == 'cli') ? PHP_EOL : '<br />');
		
		/** Include PHPExcel */
		require_once '../Classes/PHPExcel.php';
		
		
		// Create new PHPExcel object
// 		echo date('H:i:s') , " Create new PHPExcel object" , EOL;
		$objPHPExcel = new PHPExcel();
		
		// Set document properties
// 		echo date('H:i:s') , " Set document properties" , EOL;
		$objPHPExcel->getProperties()->setCreator("Maarten Balliauw")
		->setLastModifiedBy("Maarten Balliauw")
		->setTitle("PHPExcel Test Document")
		->setSubject("PHPExcel Test Document")
		->setDescription("Test document for PHPExcel, generated using PHP classes.")
		->setKeywords("office PHPExcel php")
		->setCategory("Test result file");
		
		
		// Add some data
// 		echo date('H:i:s') , " Add some data" , EOL;
		$objPHPExcel->setActiveSheetIndex(0)
		->setCellValue('A1', '时间')
		->setCellValue('B1', '负责人')
		->setCellValue('C1', '按ABC分类')
		->setCellValue('D1', '本周事项')
		->setCellValue('E1', '行动计划')
		->setCellValue('F1', '完成时限')
		->setCellValue('G1', '完成情况')
		->setCellValue('H1', '见证性材料')
		->setCellValue('I1', '未完成原因说明及如何补救');
		
		
		$account = $this->app->user->account;
		$myplanList = $this->plan->queryPassedPlan($account, $thisWeekFirstDay, $nextWeekFirstDay);
// 		$this->dealArrForRowspan($myplan[0], 'firstDayOfWeek');
// 		$this->view->checkPlan		= $myplan[0];
// 		$this->view->uncheckedPlan  = $myplan[1];
		
		
		$i = 1;
		foreach ($myplanList as $myplan)
		{
			$i++;
			$objPHPExcel->setActiveSheetIndex(0)
			->setCellValue('A'. $i, $myplan->firstDayOfWeek. ' ~ '. $myplan->lastDayOfWeek)
			->setCellValue('B'. $i, $myplan->accountname)
			->setCellValue('C'. $i, $myplan->type)
			->setCellValue('D'. $i, $myplan->matter)
			->setCellValue('E'. $i, $myplan->plan)
			->setCellValue('F'. $i, $myplan->deadtime)
			->setCellValue('G'. $i, $myplan->status)
			->setCellValue('H'. $i, $myplan->evidence)
			->setCellValue('I'. $i, $myplan->courseAndSolution);
		}
		// Miscellaneous glyphs, UTF-8
// 		$objPHPExcel->setActiveSheetIndex(0)
// 		->setCellValue('A4', 'Miscellaneous glyphs')
// 		->setCellValue('A5', '江山代有才人出，各领风骚数百年');
		
		
// 		$objPHPExcel->getActiveSheet()->setCellValue('A8',"Hello\nWorld");
// 		$objPHPExcel->getActiveSheet()->getRowDimension(8)->setRowHeight(-1);
// 		$objPHPExcel->getActiveSheet()->getStyle('A8')->getAlignment()->setWrapText(true);
		
		
		// Rename worksheet
// 		echo date('H:i:s') , " Rename worksheet" , EOL;
		$objPHPExcel->getActiveSheet()->setTitle('Simple');
		
		
		// Set active sheet index to the first sheet, so Excel opens this as the first sheet
		$objPHPExcel->setActiveSheetIndex(0);
		
		
		// Save Excel 2007 file
// 		echo date('H:i:s') , " Write to Excel2007 format" , EOL;
// 		$callStartTime = microtime(true);
		
// 		$objWriter = PHPExcel_IOFactory::createWriter($objPHPExcel, 'Excel2007');
// 		$objWriter->save(str_replace('.php', '.xlsx', __FILE__));
// 		$callEndTime = microtime(true);
// 		$callTime = $callEndTime - $callStartTime;
		
// 		echo date('H:i:s') , " File written to " , str_replace('.php', '.xlsx', pathinfo(__FILE__, PATHINFO_BASENAME)) , EOL;
// 		echo 'Call time to write Workbook was ' , sprintf('%.4f',$callTime) , " seconds" , EOL;
// 		// Echo memory usage
// 		echo date('H:i:s') , ' Current memory usage: ' , (memory_get_usage(true) / 1024 / 1024) , " MB" , EOL;
		
		
// 		// Save Excel 95 file
// 		echo date('H:i:s') , " Write to Excel5 format" , EOL;
		$callStartTime = microtime(true);
		
		$objWriter = PHPExcel_IOFactory::createWriter($objPHPExcel, 'Excel5');
		$objWriter->save(str_replace('.php', '.xls', __FILE__));
		$callEndTime = microtime(true);
		$callTime = $callEndTime - $callStartTime;
		
		return $myplanList;
		
// 		echo date('H:i:s') , " File written to " , str_replace('.php', '.xls', pathinfo(__FILE__, PATHINFO_BASENAME)) , EOL;
// 		echo 'Call time to write Workbook was ' , sprintf('%.4f',$callTime) , " seconds" , EOL;
// 		// Echo memory usage
// 		echo date('H:i:s') , ' Current memory usage: ' , (memory_get_usage(true) / 1024 / 1024) , " MB" , EOL;
		
		
// 		// Echo memory peak usage
// 		echo date('H:i:s') , " Peak memory usage: " , (memory_get_peak_usage(true) / 1024 / 1024) , " MB" , EOL;
		
// 		// Echo done
// 		echo date('H:i:s') , " Done writing files" , EOL;
// 		echo 'Files have been created in ' , getcwd() , EOL;
	}
	
