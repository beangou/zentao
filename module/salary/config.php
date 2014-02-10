<?php
$config->salary->create = new stdclass();
$config->salary->edit   = new stdclass();
$config->salary->list = new stdclass();
$config->salary->list->exportFields = 'realname,standsalary,
		count,manageCoeff,otherCoeff,measure,
		bug,bonus,finalSalary,year,
		deptRank,officeRank,allRank';
$config->salary->list->exportDetails = 'realname,standsalary,
		count,personnelCoeff,deptCoeff,measure,
		bug,bonus,finalSalary,year,
		deptName,allRank';

$config->salary->list->exportManage = 'realname,standsalary,
		count,manageCoeff,otherCoeff,measure,
		bug,bonus,finalSalary,year,
		deptName,allRank';
$config->salary->list->leaderStaff = 'realname,standsalary,
		count,personnelCoeff,deptCoeff,measure,
		bug,bonus,finalSalary,year,
		deptName,allRank';
