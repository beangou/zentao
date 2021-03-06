<?php
    /**
     * Close a bug.
     * 扩展：不将assignedTo字段设为closed
     * @param  int    $bugID 
     * @access public
     * @return void
     */
    public function close($bugID)
    {
    	//->add('assignedTo',     'closed')
        $oldBug = $this->getById($bugID);
        $now = helper::now();
        $bug = fixer::input('post')
            ->add('assignedDate',   $now)
            ->add('status',         'closed')
            ->add('closedBy',       $this->app->user->account)
            ->add('closedDate',     $now)
            ->add('lastEditedBy',   $this->app->user->account)
            ->add('lastEditedDate', $now)
            ->add('confirmed',      1)
            ->remove('comment')
            ->get();

        $this->dao->update(TABLE_BUG)->data($bug)->autoCheck()->where('id')->eq((int)$bugID)->exec();
    }