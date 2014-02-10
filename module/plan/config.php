<?php
$config->plan = new stdclass();

$config->plan->create	= new stdClass();
$config->plan->create->requiredFields	= 'team,leader,auditor1,auditor2';

$config->plan->create->weekrequiredFields	= 'matter,limit';

$config->plan->batchCreate  = 10;
$config->plan->list = new stdclass();

$config->plan->list->exportFields = 'type,sort,matter,plan,
									 limit,appraise,complete,
									 chargeName,auditorName,status,
									 remark,startDate,finishedDate';

$config->plan->list->exportRate =	'realname,team,score,rank,finishedDate';