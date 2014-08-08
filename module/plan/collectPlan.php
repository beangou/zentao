<?php
	require_once('class.phpmailer.php'); //载入PHPMailer类

	collectMyplan();
	
	// 汇总计划 入口
	function collectMyplan() {
		checkCollect();
	}
	
	
	// 读取配置文件的相关值
	function get_config($file, $ini, $type="string"){
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
// 			$config = preg_match("/".preg_quote($ini)."=\"(.*)\";/", $str, $res);
			$config = preg_match("/".preg_quote($ini)."\s*=\s*\"(.*)\";/", $str, $res);
			if($res[1]==null){
				$config = preg_match("/".preg_quote($ini)."\s*=\s*\'(.*)\';/", $str, $res);
// 				$config = preg_match("/".preg_quote($ini)."=\'(.*)\';/", $str, $res);
			}
			return $res[1];
		}
	}
	
	
	//审核计划
	function checkCollect() {
		$result = 0;
		
		$dbfile = '/opt/lampp/zentao/config/my.php';
		
		$con = @mysql_connect(get_config($dbfile, 'config->db->host'), get_config($dbfile, 'config->db->user'), get_config($dbfile, 'config->db->password'))
		or die("database access error.");
		
		@mysql_select_db(get_config($dbfile, 'config->db->name')) //选择数据库mydb
		or die("database not exists.");
		
		@mysql_query("set names 'utf8'");
		
		//查看各组长、技术经理的审核未通过计划
		$leaderUnpassPlans = @mysql_query("SELECT T1.account, T4.realname, T1.firstDayOfWeek, T1.lastDayOfWeek, T5.team FROM ict_my_weekplan T1
											LEFT JOIN ict_membset T2 ON (T2.account = T1.account)
											LEFT JOIN zt_user T4 ON (T4.account = T1.account)
											LEFT JOIN ict_proteam T5 ON (T5.id = T2.proteam)
											WHERE T2.leader != '0'
											AND T1.auditPass = '0'
											GROUP BY T1.account, T1.firstDayOfWeek") or die("search plans for leader error .");
		
		$unpassTeam = array();
		//如果有审核未通过， 即发送短息通知
		while($rs= @mysql_fetch_array($leaderUnpassPlans)) {
			// 判断今天 是否 为 在员工请假时间段内， 如果是，跳过
			if (checkNotSendMessage($rs[0])) {
				continue;
			}
			$contactStyle = getContactStyle($rs[0]); 
			sendMsg($contactStyle[1], '您好,'. $rs[1]. '!'. '您的禅道周计划('. $rs[2]. '~'. $rs[3]. ')审核未通过,请尽快修改并提交.');
			$result = 1;
			array_push($unpassTeam, $rs[1]. '('. $rs[4]. ')');
		}

		$dateArr = getLastAndEndDayOfWeek();
		
		//查看下周周计划未提交的组长或技术经理
		$leaderUnsubmitPlans = @mysql_query("SELECT T4.team, T1.account, T2.realname FROM ict_membset T1 
											LEFT JOIN zt_user T2 ON (T2.account = T1.account)
											LEFT JOIN ict_proteam T4 ON (T4.id = T1.proteam)
											WHERE T1.account NOT IN (SELECT T1.account FROM ict_my_weekplan T3
															LEFT JOIN ict_membset T1 ON (T1.account = T3.account)
															WHERE T3.firstDayOfWeek = '". $dateArr[2]. "'
															AND T1.leader != '0'
														)
											AND T1.leader != '0'			
											GROUP BY T4.team, T1.account, T2.realname
											ORDER BY T1.account") or die("search plans for leader error .");
		
		//如果下周周计划审核未提交， 即发送短息通知
		while($rs= @mysql_fetch_array($leaderUnsubmitPlans)) {
			// 判断是否需要 发短信 提醒, 如果请假， 不需 发短信 提醒
			if (checkNotSendMessage($rs[1])) {
				continue;
			}
			$contactStyle = getContactStyle($rs[1]);
			sendMsg($contactStyle[1], '您好,'. $rs[2]. '!'. '您的禅道周计划('. $dateArr[2]. '~'. $dateArr[4]. ')未提交,请尽快填写并提交.');
			$result = 1;
			array_push($unpassTeam, $rs[2]. '('. $rs[0]. ')');
		}
		
		
		//查看科长审核组长、技术经理计划情况
		$chiefPlansLeader = @mysql_query("SELECT T4.team, T1.account, T2.realname, T3.firstDayOfWeek, T3.lastDayOfWeek FROM ict_my_weekplan T3
											LEFT JOIN ict_membset T1 ON (T1.account = T3.account)
											LEFT JOIN zt_user T2 ON (T2.account = T1.account)
											LEFT JOIN ict_proteam T4 ON (T4.leader = T3.account)
											WHERE T1.leader = '1'
											AND T3.auditPass = '2'")
							 or die("search leader plans for chief error .");
		
		$chiefPlansTechmanager = @mysql_query("SELECT T4.`team`, T1.`account`, T5.`realname`, T1.`firstDayOfWeek`, T1.`lastDayOfWeek` FROM ict_my_weekplan T1 
												LEFT JOIN ( SELECT * FROM ict_audit WHERE auditTime IN (SELECT MAX(auditTime) FROM ict_audit
														GROUP BY account, firstDayOfWeek) ORDER BY account, firstDayOfWeek) T2 
												ON (T2.account = T1.account AND T2.`firstDayOfWeek` = T1.`firstDayOfWeek`)
												LEFT JOIN ict_membset T3 ON (T3.`account` = T1.`account`)
												LEFT JOIN ict_proteam T4 ON (T4.`id` = T3.`proteam`)	
												LEFT JOIN zt_user T5 ON (T5.`account` = T1.`account`)
												WHERE T3.`leader` = '2' 
												AND (T1.`auditPass` = '2' OR (T2.`result` = '同意' AND T2.auditor != 'chenxiaobo'))")
								or die("search leader plans for chief error .");
		
		$teamArr = array();
		while($rs= @mysql_fetch_array($chiefPlansLeader)) {
			if (checkNotSendMessage($rs[1])) {
				continue;
			}
			array_push($teamArr, $rs[2]. '('. $rs[0]. ')');
		}
		
		while($rs= @mysql_fetch_array($chiefPlansTechmanager)) {
			if (checkNotSendMessage($rs[1])) {
				continue;
			}
			array_push($teamArr, $rs[2]. '('. $rs[0]. ')');
		}
		
		if (count($unpassTeam) > 0 || count($teamArr) > 0) {
			$contactStyle = getContactStyle('chenxiaobo');
			$message = '您好,';
			if (count($unpassTeam) > 0) {
				$message .= '周计划未提交或审核未通过的有:'. implode('、', array_flip(array_flip($unpassTeam)));
			} else {
				$message .= '各组长、技术经理周计划均已提交';
			}
			if (count($teamArr) > 0) {
				$message .= ',您还有如下周计划未审核:'. implode('、', array_flip(array_flip($teamArr))). ',请登录禅道至我的地盘->我的计划->我的审核进行审核.';
			} else {
				$message .= ',您暂无计划需要审核.';
			}
			sendMsg($contactStyle[1], $message);
			$result = 1;
		}
		
		// 所有计划都OK， 进行汇总
		if ($result == 0) {
			$emailSend = @mysql_query("SELECT * FROM ict_email_history WHERE sendtime = '". $dateArr[2]. "'");
			if (!$rs1= @mysql_fetch_array($emailSend)) {
				generateExcl();
				$contactStyle = getContactStyle('chenxiaobo');
				sendEmail($contactStyle[0]);
				@mysql_query("INSERT INTO ict_email_history (sendtime)
				VALUES ('". $dateArr[2]. "')");
			 }
 		}
		
		@mysql_close($con);
	}
	
	//根据用户名，获取联系方式（邮箱和手机号码）
	function getContactStyle($account) {
		$styleArr = array();
		
		//查看各组长、技术经理的审核未通过计划
		$leaderPlans = @mysql_query("SELECT email, mobile FROM zt_user T1 where T1.account = '". $account. "'") or die("组长情况 SQL语句执行失败");
		if ($rs=@mysql_fetch_array($leaderPlans)) {
			$styleArr[0] = $rs[0];
			$styleArr[1] = $rs[1];
		} else {
			$styleArr[0] = '';
			$styleArr[1] = '';
		}
		return $styleArr;
	}
	// 判断今天 是否 为 在员工请假时间段内， 在，则不发短信， 不在，则发短信
	function checkTodayIsLeave($account = '') {
		$anyValue = false;
		$today = date('Y-m-d', time());
		$queryResult = @mysql_query("SELECT * FROM ict_leave WHERE account='" + $account + "' AND startTime <= '" + $today + "' AND endTime >= '" + $today + "'") or die("判断今天 是否 为 在员工请假时间段内 SQL语句执行失败");
		if ($rs=@mysql_fetch_array($queryResult)) {
			$anyValue = true;
		}
		return $anyValue;
	}
	// 	如果今天为周六、或周日，如果请假的第一天 为下周一 或者 请假最后一天为本周五 则不发，其他情况都发
	// 返回true 不发； 返回false 就发
	function checkWeekend($account) {
		$result = false;
		// 获取 今天是星期几
		$today = date("w");
		// 获取下周一以及上周五 的日期
		$thisFriday = date('Y-m-d', time()+(5-$today)*24*3600);
		//下周一
		$nextMonday = date('Y-m-d', time()+(8-$today)*24*3600);
		// 6表示周六 0表示周日
		if ($today == 6 || $today == 0) {
			$queryResult = @mysql_query("SELECT * FROM ict_leave WHERE account='" + $account + "' AND (startTime = '" + $nextMonday + "' OR endTime = '" + $thisFriday + "')") or die("判断周末是否需要发短信");
			if ($rs=@mysql_fetch_array($queryResult)) {
				$result = true;
			}	
		} 
		return $result;
	} 
	
	// 判断是否需要发短信 有待思考(返回true， 则发短信)
	function checkNotSendMessage($account = '') {
		$result = false;
		// 判断今天 是否 为 在员工请假时间段内， 如果是，跳过
		if (checkTodayIsLeave($account) || checkWeekend($account)) {
			$result = true;
		}
		return $result;
	}
	
	//如果不通过：
	//(1)如果科长有的计划未审核，发送短信到科长
	//(2)如果有的组长、技术经理计划未通过，发送短信到组长、技术经理
	function sendMsg($phoneNo, $content) {
		$phoneNo = '15955552919';

		$url='http://120.209.138.191/smms/provider/full/sms?msg='. $content. '&phone='. $phoneNo. '&spid=f8510a293f61c826013f61d2abb50005&ospid=f8510a283f645140013f646cfe690014';
		$ch = curl_init();
		curl_setopt($ch, CURLOPT_PROXY, 'proxy.ah.cmcc:8080');
		curl_setopt($ch, CURLOPT_URL, $url);
		curl_setopt($ch, CURLOPT_HEADER, 1);
		curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
		curl_setopt($ch, CURLOPT_FOLLOWLOCATION, 1);
		curl_setopt($ch, CURLOPT_TIMEOUT, 120);
		$result = curl_exec($ch);
		curl_close($ch);
	}			
	
	
// 	sendEmail('liu.tongbin@ustcinfo.com');

	/**
	 * 如果所有组长均审核并通过  计划汇总 即生成excl 并发送到指定邮件
	 */
    function sendEmail($email)	
	{
		$email = '1289188692@qq.com';
		
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
		
		$mail->AddAttachment('/opt/lampp/zentao/module/plan/collectPlan.xls','ict周计划汇总.xls'); // 添加附件,并指定名称
// 		$mail->AddAttachment('collectPlan.xls','ict周计划汇总.xls'); // 添加附件,并指定名称
		$mail->IsHTML(true); //支持html格式内容
		
		$mail->Body = '您好! <br/>这是一封来自安徽移动ict禅道系统的邮件！<br/>'; //邮件主体内容
		
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
     * 处理有一定顺序的数组，是根据其中某个key设置rowspan以表格形式显示到页面上来,返回的数组中某些元素多了rowspanVal的值
     * @param  array $temp          the name of the select tag.
     */
    function dealArrForRowspan($temp = array(), $key = '')
    {
    	$rowspanIndex = 0;
    	$rowspanValue = 0;
    	for ($i=0; $i<count($temp); $i++){
    		if ($temp[$i]->$key == $temp[$rowspanIndex]->$key) {
    			if ($i > 0) {
    				$temp[$rowspanIndex]->plan .= $temp[$i]->plan;
    			}
    			$rowspanValue++;
    		} else {
    			$temp[$rowspanIndex]->rowspanVal = $rowspanValue;
    			$rowspanValue = 1;
    			$rowspanIndex = $i;
    		}
    	}
    
    	//这有当数组有数据时，才给rowspanVal赋值，否则，没意义，多一条没用的数据
    	if ($rowspanValue > 0) {
    		$temp[$rowspanIndex]->rowspanVal = $rowspanValue;
    	}
    
    	/* End. */
    	return $temp;
    }
	
	/**
	 * 生成excl文件
	 */
	function generateExcl() 
	{
		/** Error reporting */
		error_reporting(E_ALL);
		ini_set('display_errors', TRUE);
		ini_set('display_startup_errors', TRUE);
		date_default_timezone_set('Europe/London');
		
		define('EOL',(PHP_SAPI == 'cli') ? PHP_EOL : '<br />');
		
		/** Include PHPExcel */
// 		require_once 'D:\www\zentao\module\Classes\PHPExcel.php';
		
		require_once '/opt/lampp/zentao/module/Classes/PHPExcel.php';
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
		
		$dateArr = getLastAndEndDayOfWeek();
		// Add some data
		
		$objPHPExcel->setActiveSheetIndex(0)
		->setCellValue('A1', 'ict周计划汇总('. $dateArr[2]. '~'. $dateArr[4]. ')');
		
		$objStyleA5 = $objPHPExcel->setActiveSheetIndex(0)->getStyle('A1');
		
		$objFontA5 = $objStyleA5->getFont();
		$objFontA5->setName('Courier New');
		$objFontA5->setSize(15);
		$objFontA5->setBold(true);
		
		$objPHPExcel->setActiveSheetIndex(0)->mergeCells('A1:C1');
		
		$objPHPExcel->setActiveSheetIndex(0)
		->setCellValue('A2', '团队')
		->setCellValue('B2', '负责人')
		->setCellValue('C2', '计划及事项');
		
		$objStyleA5 = $objPHPExcel->setActiveSheetIndex(0)->getStyle('A2:C2');
		
		$objFontA5 = $objStyleA5->getFont();
		$objFontA5->setName('Courier New');
		$objFontA5->setSize(10);
		$objFontA5->setBold(true);
		
		$collectPlans = @mysql_query("SELECT T3.team, T2.realname, T1.type, T1.plan, T1.matter, T2.account FROM ict_my_weekplan T1
									LEFT JOIN zt_user T2 ON (T2.account = T1.account)
									LEFT JOIN ict_proteam T3 ON (T3.leader = T1.account)
									WHERE T3.team IS NOT NULL
									AND firstDayOfWeek = '". $dateArr[2]. "' 
									ORDER BY T2.realname, T1.type") or die("search collect plans for chief error .");
		
		$collectArr = array();
		
		//  同一个人的周计划放到一个单元格中
		$i = 2;
		while($rs = @mysql_fetch_array($collectPlans)) {
			// 如果请假， 就不用生成周计划了
			if (checkNotSendMessage($rs[5])) {continue;}
			$collectPlan->team = $rs[0];
			$collectPlan->realname = $rs[1];
			$collectPlan->plan = "\n". $rs[2]. ':'. $rs[4]. "\n(". $rs[3]. ")\n";
			array_push($collectArr, $collectPlan);
			$collectPlan = null;
		}
		
		$collectResult = dealArrForRowspan($collectArr, 'realname');
		
		foreach ($collectArr as $myplan) {
			if ($myplan->rowspanVal > 0) {
				$i++;
				$objPHPExcel->setActiveSheetIndex(0)
				->setCellValue('A'. $i, $myplan->team)
				->setCellValue('B'. $i, $myplan->realname)
				->setCellValue('C'. $i, $myplan->plan);

				$objPHPExcel->getActiveSheet()->getStyle('C'. $i)->getAlignment()->setWrapText(true);
				$objPHPExcel->setActiveSheetIndex(0)->getColumnDimension('A')->setWidth(20);
				$objPHPExcel->setActiveSheetIndex(0)->getColumnDimension('C')->setWidth(80);
			}
		}
		
		$objPHPExcel->getActiveSheet()->setTitle('周计划汇总');
		
		// Set active sheet index to the first sheet, so Excel opens this as the first sheet
		$objPHPExcel->setActiveSheetIndex(0);
		
		$callStartTime = microtime(true);
		
		$objWriter = PHPExcel_IOFactory::createWriter($objPHPExcel, 'Excel5');
		//$objWriter->save(str_replace('.php', '.xls', '/opt/lampp/zentao/module/plan/'. __FILE__));
		$objWriter->save('/opt/lampp/zentao/module/plan/collectPlan.xls');
// 		$objWriter->save('collectPlan.xls');
		$callEndTime = microtime(true);
		$callTime = $callEndTime - $callStartTime;
		
	}
	
	//获取本周六和下周五的日期
	  function getLastAndEndDayOfWeek() {
		$myDateArr = array();
		//今天是星期几
		$today = date("w");
		//如果今天是星期六，那么下个星期就是下个星期六，故
		if ($today == 6) {
			$today = -1;
		}
	
		//下周周计划第一天（本周六）
		$thisSaturday = date('Y-m-d', time()+(6-$today)*24*3600);
		//本周周计划第一天（上周六）
		$lastSaturday = date('Y-m-d', time()-(1+$today)*24*3600);
		//上周计划第一天（上上周六）
		$lastLastSaturday = date('Y-m-d', time()-(8+$today)*24*3600);
		//下周周计划最后一天（下周五）
		$nextFriday = date('Y-m-d', time()+(12-$today)*24*3600);
		//本周最后一天为本周五
		$thisFriday = date('Y-m-d', time()+(5-$today)*24*3600);
		//上周计划最后一天（上周五）
		$lastFriday = date('Y-m-d', time()-(1+$today)*24*3600);
	
		array_push($myDateArr, $thisSaturday);
		array_push($myDateArr, $nextFriday);
		array_push($myDateArr, $lastSaturday);
		array_push($myDateArr, $lastLastSaturday);
		array_push($myDateArr, $thisFriday);
		array_push($myDateArr, $lastFriday);
		return $myDateArr;
	}
	
