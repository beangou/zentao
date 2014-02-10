<?php
global $lang,$app;
$app->loadLang('salary');



$config->system = new stdclass();
$config->system->create = new stdClass();
$config->system->rewards = new stdClass();

$config->system->create->requiredFields = 'productId,project,deptCoeff,partNum,coefficient,DM,createDate';
$config->system->rewards->requiredFields= 'name,integratedBug,rewards,total';


$config->system->personnel = new stdClass();
$config->system->personnel->search['module']             = 'system';
$config->system->personnel->search['fields']['realname'] = $lang->user->realname;
$config->system->personnel->search['fields']['account'] = $lang->user->account;