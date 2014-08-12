<?php
$config->plan = new stdclass();

$config->plan->create	= new stdClass();
$config->plan->create->requiredFields	= 'team,leader,auditor1,auditor2';

$config->plan->create->weekrequiredFields	= 'matter,limit';

$config->plan->batchCreate  = 10;
$config->plan->list = new stdclass();


$config->plan->editor = new stdclass();
$config->plan->editor->proteam  = array('id' => 'desc', 'tools' => 'simpleTools');
$config->plan->editor->edit     = array('id' => 'desc,comment', 'tools' => 'simpleTools');
$config->plan->editor->view     = array('id' => 'comment,lastComment', 'tools' => 'simpleTools');
$config->plan->editor->assignto = array('id' => 'comment', 'tools' => 'simpleTools');
$config->plan->editor->start    = array('id' => 'comment', 'tools' => 'simpleTools');
$config->plan->editor->finish   = array('id' => 'comment', 'tools' => 'simpleTools');
$config->plan->editor->close    = array('id' => 'comment', 'tools' => 'simpleTools');
$config->plan->editor->activate = array('id' => 'comment', 'tools' => 'simpleTools');
$config->plan->editor->cancel   = array('id' => 'comment', 'tools' => 'simpleTools');
$config->plan->editor->editproteaminfo  = array('id' => 'desc', 'tools' => 'simpleTools');

$config->plan->list->exportFields = 'type,sort,matter,plan,
									 limit,appraise,complete,
									 chargeName,auditorName,status,
									 remark,startDate,finishedDate';

$config->plan->list->exportRate =	'realname,team,score,rank,finishedDate';