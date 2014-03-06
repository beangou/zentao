<?php

$lang->plan->confirmed 				= '审核状态';
$lang->plan->account				= '负责人';
$lang->plan->status					= '自评情况';
$lang->plan->deadtime 				= '时限';



$lang->plan->common					= '计划管理';
$lang->plan->export 				= '导出';
$lang->plan->query					= '查询';
$lang->plan->add					= '新增';
$lang->plan->create       			= '新增';
$lang->plan->batchCreate  			= '批量添加';
$lang->plan->edit       			= '编辑';
$lang->plan->batchEdit  			= '批量添加';
$lang->plan->batchAction  			= '批量操作';
$lang->plan->update					= '更新';
$lang->plan->finish					= '审核';
$lang->plan->copy					= '查看';
$lang->plan->prodelete				= '项目组删除';
$lang->plan->deleteMemb				= '成员删除';
$lang->plan->submit					= '提交';
$lang->plan->delete					= '删除';
$lang->plan->queryPlan				= '计划查询';
$lang->plan->lastPlan				= '上周未完成';

/*left menu*/
$lang->plan->myplan					= '我的计划';
$lang->plan->deal					= '待我处理';
$lang->plan->teamPlan				= '团队计划查询';
$lang->plan->planrate				= '月计划完成率';

$lang->plan->weekly					= '周计划管理';
$lang->plan->monthly				= '月计划管理';
$lang->plan->handle					= '待我审核';
$lang->plan->member					= '成员计划';
$lang->plan->complete				= '月计划完成率';
$lang->plan->planlist				= '月计划明细';
$lang->plan->parameter				= '参数设定';
$lang->plan->membset				= '成员小组设定';
$lang->plan->proteam				= '项目组设定';
$lang->plan->auditorName			= '审核人';
$lang->plan->chargeName				= '负责人';

/*week plan*/
$lang->plan->startDate				= '预计开始';
$lang->plan->finishedDate			= '完成日期';
$lang->plan->type					= '类别';
// $lang->plan->types['']				= '';
$lang->plan->types['1']				= '正常工作';
$lang->plan->types['2']				= '能力培养';				
$lang->plan->sort					= 'ABC分类';
$lang->plan->matter					= '本周事项';
$lang->plan->plan					= '行动计划';
$lang->plan->limit					= '时限';
$lang->plan->appraise				= '自评情况';
$lang->plan->complete				= '终极审核';
$lang->plan->material				= '见证材料';
$lang->plan->desc					= '未完成原因及补救';
$lang->plan->charge					= '负责人';
$lang->plan->auditor				= '审核人';
// $lang->plan->status					= '审核状态';
$lang->plan->remark					= '备注';
$lang->plan->completed[0]			= '未评价';
$lang->plan->completed[1]			= '未完成';
$lang->plan->completed[2]			= '已完成';
$lang->plan->completed[3]			= '延期完成';
$lang->plan->completed[4]			= '经领导允许延期完成';
$lang->plan->abcSort['']			= '';
$lang->plan->abcSort['A1']			= 'A1';
$lang->plan->abcSort['A2']			= 'A2';
$lang->plan->abcSort['A3']			= 'A3';
$lang->plan->abcSort['A4']			= 'A4';
$lang->plan->abcSort['A5']			= 'A5';
$lang->plan->abcSort['A6']			= 'A6';
$lang->plan->abcSort['B1']			= 'B1';
$lang->plan->abcSort['B2']			= 'B2';
$lang->plan->abcSort['B3']			= 'B3';
$lang->plan->abcSort['B4']			= 'B4';
$lang->plan->abcSort['B5']			= 'B5';
$lang->plan->abcSort['B6']			= 'B6';
$lang->plan->abcSort['C1']			= 'C1';
$lang->plan->abcSort['C2']			= 'C2';
$lang->plan->abcSort['C3']			= 'C3';
$lang->plan->abcSort['C4']			= 'C4';
$lang->plan->abcSort['C5']			= 'C5';
$lang->plan->abcSort['C6']			= 'C6';
$lang->plan->statu					= '未审核';
$lang->plan->beginEnd				= '起止时间';
$lang->plan->planTitle				= '周计划';
$lang->plan->currentPlan			= '(当前周)';
$lang->plan->copyPlan				= '复制上周未完成计划';

$lang->plan->confirmDelete  		= "您确定要删除这个计划吗？";
$lang->plan->memberDelete  			= "您确定要删除这个成员吗？";
$lang->plan->proteamDelete  		= "您确定要删除这个小组吗？";
/*proteam settings*/
$lang->plan->team					= '项目组名称';
$lang->plan->leader					= '组长';
$lang->plan->auditor1				= '审核人一';
$lang->plan->auditor2				= '审核人二';
$lang->plan->error 					= new stdclass();
$lang->plan->error->team       		= "ID %s，项目名称不能为空";
$lang->plan->error->auditor			= "ID %s，审核人不能为空";

/*membset*/
$lang->plan->realname				= '姓名';
$lang->plan->dept					= '所属科室';
$lang->plan->choose					= '选择成员';
$lang->plan->select	  				= '选定';
/*handle*/
$lang->plan->notPass				= '(列出未通过计划)';
$lang->plan->memberWarn				= '(列出需我审核处理的计划,对于多人审核计划,如果已经审核过,可不作处理)';
// $lang->plan->handleStatus[0]		= '';
$lang->plan->handleStatus[1]		= '未审核';
$lang->plan->handleStatus[2]		= '未通过';
$lang->plan->handleStatus[3]		= '已通过';	
/*月计划完成率*/
$lang->plan->dept					= '所属科室';
$lang->plan->month					= '月份';
$lang->plan->rank					= '月排名';
$lang->plan->score					= '月计划完成率';

$lang->plan->planCheck				= '周计划审核';
$lang->plan->periods['myplan']      = '我的计划';//主要是新增计划可以查看
$lang->plan->periods['queryplan']   = '查询计划';//只可查询，不可操作
$lang->plan->periods['handle']      = '审核计划';

// $lang->plan->myplan      = '我的计划';//主要是新增计划可以查看
// $lang->plan->queryplan   = '查询计划';//只可查询，不可操作
// $lang->plan->handle      = '审核计划';


$lang->plan->searchplan				= '按日期查询';