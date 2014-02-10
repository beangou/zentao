function reloadTable()
{
	if($type==1 || $type==0)$("#accumulativeT").hide();
    else $("#personelT").hide();
}

if(role==1 || role==4){
	if (type==1){
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
	//				pointFormat: '{series.name}: <b>{point.percentage}%</b>',
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
					name: '当月人员数',
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
	
	else if(type==2){
		$(function () {
			$('#eachMonth').highcharts({
				data: {
					table: document.getElementById('eachMonthtable')
				},
				chart: {
					type: 'column'
				},
				title: {
					text: '各月总工时'
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
			
			$('#eachAverage').highcharts({
				data: {
					table: document.getElementById('eachAveragetable')
				},
				chart: {
					type: 'column'
				},
				title: {
					text: '各月平均工时'
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
			
			
			/*标准薪酬对比分析*/
			$('#salaryContrast').highcharts({
				data: {
					table: document.getElementById('salaryContrasttable')
				},
				chart: {
					type: 'column'
				},
				title: {
					text: '标准薪酬对比分析'
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
			
			//量化薪酬支出分析
				var data = eval(""+salaryPayAnalysis+"");
				if (data == null) {
				return;
				}
				var length = data.length;
				var options = {
				        chart: {
				            renderTo: 'salaryPayAnalysis',
				            type: 'line'
				        },
				        title: {
				            text: '公司量化薪酬支出分析'
				        },
				        xAxis: {
				        	title: {
				                text: ''
				            },
				            categories: []
			            },
			            yAxis: {
			                title: {
			                    text: 'Temperature (°C)'
			                }
			            },
			            tooltip: {
			                enabled: false,
			                formatter: function() {
			                    return '<b>'+ this.series.name +'</b><br/>'+
			                        this.x +': '+ this.y +'°C';
			                }
			            },
			            plotOptions: {
			                line: {
			                    dataLabels: {
			                        enabled: true
			                    },
			                    enableMouseTracking: false
			                }
			            },
			            series: []
				    };
				if(length > 0){
				for(var i=0;i<length; i++){
				//压入数据
				if(true){
					var series1 = {
							name: '薪资上涨人员总增幅',
							data: []
					};
					for(var i=0;i<length; i++){
						if(data[i].upTotal==0)series1.data.push(0);
						else series1.data.push(data[i].upTotal);
						//压入数据
						options.xAxis.categories.push(data[i].date);
					}
					options.series.push(series1);
				}
				if(true){
					var series1 = {
							name: '薪资下降人员总降幅',
							data: []
					};
					for(var i=0;i<length; i++){
						if(data[i].lowTotal==0)series1.data.push(0);
						else series1.data.push(data[i].lowTotal);
						//压入数据
						options.xAxis.categories.push(data[i].date);
					}
					options.series.push(series1);
				}
				if(true){
					var series1 = {
							name: '薪资总额变化',
							data: []
					};
					for(var i=0;i<length; i++){
						if(data[i].total==0)series1.data.push(0);
						else series1.data.push(data[i].total);
						//压入数据
						options.xAxis.categories.push(data[i].date);
					}
					options.series.push(series1);
				}
	
				}
				}
				// 创建图表
				new Highcharts.Chart(options);
			
			
			
		});
	}
}