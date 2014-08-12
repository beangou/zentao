<?php
$config->installed       = true;
$config->debug           = false;
$config->requestType     = 'GET';
$config->db->host		 = '10.152.89.206:3308';
//$config->db->host		 = '127.0.0.1:3306';
$config->db->name		 = 'zentao';
$config->db->user        = 'root';
$config->db->password  = 'ICT-123456@ztdbr';
//$config->db->password    = '';
$config->db->prefix      = 'zt_';
$config->webRoot         = getWebRoot();
$config->default->lang   = 'zh-cn';
$config->mysqldump     = 'D:\beanGou\mysql\bin\mysqldump.exe';
//$config->mysqldump       = 'E:\work\mysql-5.6.19-winx64\mysql-5.6.19-winx64\bin\mysqldump.exe';	
