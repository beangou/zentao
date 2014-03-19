<?php
$lang->feedback->common      = '反馈列表';
$lang->feedback->index       = '首页';
$lang->feedback->browse      = '问题列表';
$lang->feedback->view        = '问题详情';
$lang->feedback->reply       = '问题回复';
$lang->feedback->toBug       = '转化为BUG';
$lang->feedback->toStory     = '转化为需求';
$lang->feedback->setConfig   = '设置配置';
$lang->feedback->syncProduct = '同步产品';
$lang->feedback->syncUser    = '同步用户';

$lang->feedback->successful       = '操作成功';
$lang->feedback->fail             = '操作失败';
$lang->feedback->syncConfig       = '同步配置';
$lang->feedback->syncProduct      = '同步产品';
$lang->feedback->syncedProducts   = '已同步产品';
$lang->feedback->unsyncedProducts = '未同步产品';

$lang->feedback->account      = '帐号';
$lang->feedback->role         = 'ZenTaoASM角色';
$lang->feedback->apiRoot      = '要同步的ZenTaoASM地址';
$lang->feedback->key          = '验证密钥';
$lang->feedback->id           = '编号';
$lang->feedback->title        = '标题';
$lang->feedback->product      = '产品';
$lang->feedback->category     = '分类';
$lang->feedback->assignedTo   = '指派给';
$lang->feedback->status       = '状态';
$lang->feedback->addedDate    = '提问时间';
$lang->feedback->actions      = '操作';
$lang->feedback->reply        = '答复';
$lang->feedback->content      = '内容';
$lang->feedback->toBug        = '转化为Bug';
$lang->feedback->toStory      = '转化为需求';
$lang->feedback->selectAll    = "全选";
$lang->feedback->overrideSync = "覆盖ZenTaoASM用户资料";

$lang->feedback->nothing       = '没有反馈';
$lang->feedback->keyNote       = '注意：密钥必须与ZenTaoASM中配置的一致';
$lang->feedback->success       = '配置成功！';
$lang->feedback->errorWritable = "无法保存，请尝试执行 chmod -R 777 %s 后重新执行保存操作！";
$lang->feedback->errorConnect  = "获取失败!网络问题或者同步配置错误";
$lang->feedback->syncSuccess   = "同步成功";
$lang->feedback->errorSynced   = "为了数据的安全，要同步已经同步的用户，请勾选“覆盖ZenTaoASM用户资料”";

$lang->request->assignedToMe  = '指派给我';
$lang->request->all           = '所有';
$lang->request->repliedByMe   = '由我回复';
$lang->request->allowedClosed = '可关闭';
$lang->request->search        = '搜索';

$lang->request->statusList['transfered']    = '已转交';
$lang->request->statusList['wait']          = '未处理';
$lang->request->statusList['viewed']        = '已查阅';
$lang->request->statusList['replied']       = '已回复';
$lang->request->statusList['doubted']       = '追问中';
$lang->request->statusList['closed']        = '已关闭';
$lang->request->statusList['storied']       = '转为需求';
$lang->request->statusList['buged']         = '转为bug';

$lang->zentaoasm->roleList['servicer'] = '客服';
$lang->zentaoasm->roleList['manager']  = '客服经理';
$lang->zentaoasm->roleList['support']  = '技术人员';
$lang->zentaoasm->roleList['admin']    = '系统管理员';

$lang->feedback->noSync  = '未同步';
$lang->feedback->synced  = '已同步';

$lang->feedback->successSync = '同步成功！';
$lang->feedback->failSync    = '同步失败！';
$lang->feedback->syncError   = '请检查网络是否畅通';

$lang->request->id          = '编号';
$lang->request->title       = '标题';
$lang->request->product     = '产品';
$lang->request->category    = '分类';
$lang->request->customer    = '提问者';
$lang->request->addedDate   = '提问时间';
$lang->request->status      = '状态';
$lang->request->assignedTo  = '指派给';
$lang->request->repliedDate = '回复时间';

$lang->request->close          = '关闭';
$lang->request->reply          = '回复';
$lang->request->edit           = '编辑';
$lang->request->doubt          = '追问';
$lang->request->assign         = '指派';
$lang->request->transfer       = '转交产品';
$lang->request->transfered     = '已转交';
$lang->request->commentReply   = '点评';

$lang->request->valuate        = '评价';
$lang->request->valuateNotice  = '（评价后，问题将自动关闭）';
$lang->request->valuateResult  = '用户评价:';
$lang->request->valuateContent = '评价详情:';
$lang->request->subRating      = '评价';
$lang->request->file           = '附件';

$lang->request->valuates['good']        = '非常满意';
$lang->request->valuates['satisfied']   = '满意';
$lang->request->valuates['unsatisfied'] = '不满意';

$lang->request->productReply = "产品回复内容";

unset($lang->action->desc);
$lang->action->desc->created     = "%s, 由 <strong>%s</strong> 创建。";
$lang->action->desc->edited      = "%s, 由 <strong>%s</strong> 编辑。";
$lang->action->desc->closed      = "%s, 由 <strong>%s</strong> 关闭。";
$lang->action->desc->transfered  = "%s, 由 <strong>%s</strong> 转交产品。";
$lang->action->desc->valuated    = "%s, 由 <strong>%s</strong> 评价。";
$lang->action->desc->commented   = "%s, 由 <strong>%s</strong> 点价。";
