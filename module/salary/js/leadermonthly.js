$(document).ready(function(){
	
	if(role==1 && type==1){
		$(function () {
			//薪酬增减
			$('#increase').highcharts({
				data: {
					table: document.getElementById('increasetable')
				},
				chart: {
					type: 'column'
				},
				title: {
					text: month+'月薪酬增减'
				},
				yAxis: {
					allowDecimals: false,
					title: {
						text: ''
					}
				},
				tooltip: {
					formatter: function() {
						return '<b>'+ this.series.name +'</b><br/>'+
						this.y +' '+ this.x.toLowerCase();
					}
				}
			});
			//当月人员数
			var obj = eval(""+personNum+"");
			$('#personNum').highcharts({
				chart: {
					plotBackgroundColor: null,
					plotBorderWidth: null,
					plotShadow: false
				},
				title: {
					text: '当月人员数'
				},
				tooltip: {
					pointFormat: '{series.name}: <b>{point.percentage}%</b>',
					percentageDecimals: 1
				},
				plotOptions: {
					pie: {
						allowPointSelect: true,
						cursor: 'pointer',
						dataLabels: {
							enabled: true,
							color: '#000000',
							connectorColor: '#000000',
							formatter: function() {
								return '<b>'+ this.point.name +'</b>: <br>'+ Highcharts.numberFormat(this.percentage, 2) +' %';
							}
						}
					}
				},
				
				series: [{
					type: 'pie',
					name: 'Browser share',
					data: [
					       ['ICT人员',   obj[0].num*1],
					       ['合署办公人员', obj[1].num*1],
					       {
					    	   name: '合作伙伴',
					    	   y: obj[2].num*1,
					    	   sliced: true,
					    	   selected: true
					       },
					       ]
				}]
			});
			//当月总工时和平均工时
			$('#hours').highcharts({
				data: {
					table: document.getElementById('datatable')
				},
				chart: {
					type: 'column'
				},
				title: {
					text: '当月总工时与平均工时'
				},
				yAxis: {
					allowDecimals: false,
					title: {
						text: ''
					}
				},
				tooltip: {
					formatter: function() {
						return '<b>'+ this.series.name +'</b><br/>'+
						this.y +' '+ this.x.toLowerCase();
					}
				}
			});
		});
	}
});