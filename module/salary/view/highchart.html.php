<?php if($extView = $this->getExtViewFile(__FILE__)){include $extView; return helper::cd();}?>
<?php
if($config->debug)
{
//     js::import($jsRoot . 'jquery/highchart/jquery.min.js');
	js::import($jsRoot . 'jquery/highchart/highcharts.js');
	js::import($jsRoot . 'jquery/highchart/modules/exporting.js');
	js::import($jsRoot . 'jquery/highchart/highcharts-more.js');
	js::import($jsRoot . 'jquery/highchart/themes/grid.js');
	js::import($jsRoot . 'jquery/highchart/modules/data.js');
}else {
// 	js::import('http://ajax.googleapis.com/ajax/libs/jquery/1.8.2/jquery.min.js');
    js::import($jsRoot . 'jquery/highchart/highcharts.js');
    js::import($jsRoot . 'jquery/highchart/modules/exporting.js');
    js::import($jsRoot . 'jquery/highchart/highcharts-more.js');
    js::import($jsRoot . 'jquery/highchart/themes/grid.js');
    js::import($jsRoot . 'jquery/highchart/modules/data.js');
}
?>
<style>
#colorbox, #cboxOverlay, #cboxWrapper{z-index:9999;}
</style>
<script> 
noResultsMatch = '<?php echo $lang->noResultsMatch;?>';
$(document).ready(function()
{
});
</script>