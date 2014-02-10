function sysnchronous()
{
	location.href	= createLink('system', 'personnel');
}
//function changeAction(formName, actionName, actionLink)
//{
//    $('#' + formName).attr('action', actionLink).submit();
//}
function batchCopy()
{
	$('#batchCopy').attr('action',createLink('system','personnel','typeID=2'));
}

function saveUserInfo()
{
	$('#userEdit').attr('action',createLink('system','personnel','typeID=2'));
}
//$("#a-hours input").keyup(function(){
//	master = document.getElementById('master').value*1;
//	creative = document.getElementById('creative').value*1;
//	patent = document.getElementById('patent').value*1;
//	report = document.getElementById('report').value*1;
//	codeQuality = document.getElementById('codeQuality').value*1;
//	document.getElementById('total').value = master+creative+patent+report+codeQuality;
//	});

function changeValue(id)
{
	var master = document.getElementById('master'+id).value*1;
	var creative = document.getElementById('creative'+id).value*1;
	var patent = document.getElementById('patent'+id).value*1;
	var report = document.getElementById('report'+id).value*1;
	var codeQuality = document.getElementById('codeQuality'+id).value*1;
	document.getElementById('total'+id).value = master+creative+patent+report+codeQuality;
}

function setRewards(id)
{
	var count = document.getElementById('integratedBug'+id).value*1;
	if(5<count && count<=10)count=count*(-20);
	else if(count>10)count=count*(-40);
	else count=0;
	var deliverBug = document.getElementById('deliverBug'+id).value*1;
	if(deliverBug>0)deliverBug=deliverBug*(-50);
	else deliverBug=0;
	var onlineBug = document.getElementById('onlineBug'+id).value*1;
	if(onlineBug>0)onlineBug=onlineBug*(-100);
	else onlineBug=0;
	var documentPunish = document.getElementById('documentPunish'+id).value*1;
	var rewards = document.getElementById('rewards'+id).value = count+deliverBug+onlineBug;
	var delay = document.getElementById('delay'+id).value*1;
	var bonus = document.getElementById('bonus'+id).value*1;
	document.getElementById('rTotal'+id).value = documentPunish+rewards+delay+bonus;
	
}


		/**
		 * Load all fields.
		 * 
		 * @param  int $productID 
		 * @access public
		 * @return void
		 */
		function loadAll(productID)
		{
			
			link = createLink('product', 'ajaxGetProjects', 'productID=' + productID + '&projectID=' + 0);
		    $('#projectIdBox').load(link);
		}

		/**
		 * Load module menu.
		 * 
		 * @param  int    $productID 
		 * @access public
		 * @return void
		 */
		function loadModuleMenu(productID)
		{
		    link = createLink('tree', 'ajaxGetOptionMenu', 'productID=' + productID + '&viewtype=bug');
		    $('#moduleIdBox').load(link);
		}

		/**
		 * Load product stories 
		 * 
		 * @param  int    $productID 
		 * @access public
		 * @return void
		 */
		function loadProductStories(productID)
		{
		    link = createLink('story', 'ajaxGetProductStories', 'productID=' + productID + '&moduleId=0&storyID=' + oldStoryID);
		    $('#storyIdBox').load(link, function(){$('#story').chosen({no_results_text:noResultsMatch});});
		}

		/**
		 * Load projects of product. 
		 * 
		 * @param  int    $productID 
		 * @access public
		 * @return void
		 */
		function loadProductProjects(productID)
		{
		    link = createLink('product', 'ajaxGetProjects', 'productID=' + productID + '&projectID=0');
		    $('#projectIdBox').load(link);
		}



		/**
		 * Load project tasks.
		 * 
		 * @param  projectID $projectID 
		 * @access public
		 * @return void
		 */
		function loadProjectTasks(projectID)
		{
		    link = createLink('task', 'ajaxGetProjectTasks', 'projectID=' + projectID + '&taskID=' + oldTaskID);
		    $('#taskIdBox').load(link, function(){$('#task').chosen({no_results_text:noResultsMatch});});
		}

