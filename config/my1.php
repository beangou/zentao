<?php
$config->installed       = true;
$config->debug           = true;
$config->requestType     = 'GET';
$config->db->host        = '127.0.0.1';
$config->db->port        = '3306';
$config->db->name        = 'zentao';
$config->db->user        = 'root';
$config->db->password    = 'root';
$config->db->prefix      = 'cm_';
$config->webRoot         = getWebRoot();
$config->default->lang   = 'zh-cn';
$config->mysqldump       = 'D:\beanGou\mysql\bin\mysqldump.exe';