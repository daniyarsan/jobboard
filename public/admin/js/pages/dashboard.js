//[Dashboard Javascript]

//Project:	Aries Admin - Responsive Admin Template
//Last change:  25/11/2017
//Primary use:   Used only for the main dashboard (index.html)


$(function () {

  'use strict';

  // Make the dashboard widgets sortable Using jquery UI
  $('.connectedSortable').sortable({
    placeholder         : 'sort-highlight',
    connectWith         : '.connectedSortable',
    handle              : '.box-header, .nav-tabs',
    forcePlaceholderSize: true,
    zIndex              : 999999
  });
  $('.connectedSortable .box-header, .connectedSortable .nav-tabs-custom').css('cursor', 'move');

	
$(document).ready(function() {
    
   var sparklineLogin = function() { 
        $('#sparklinedash').sparkline([ 0, 5, 6, 10, 9, 12, 4, 9, 12, 10, 9], {
            type: 'bar',
            height: '30',
            barWidth: '4',
            resize: true,
            barSpacing: '10',
            barColor: '#06d79c'
        });
         $('#sparklinedash2').sparkline([ 0, 5, 6, 10, 9, 12, 4, 9, 12, 10, 9], {
            type: 'bar',
            height: '30',
            barWidth: '4',
            resize: true,
            barSpacing: '10',
            barColor: '#398bf7'
        });
          $('#sparklinedash3').sparkline([ 0, 5, 6, 10, 9, 12, 4, 9, 12, 10, 9], {
            type: 'bar',
            height: '30',
            barWidth: '4',
            resize: true,
            barSpacing: '10',
            barColor: '#f96868'
        });
           $('#sparklinedash4').sparkline([ 0, 5, 6, 10, 9, 12, 4, 9, 12, 10, 9], {
            type: 'bar',
            height: '30',
            barWidth: '4',
            resize: true,
            barSpacing: '10',
            barColor: '#e9ab2e'
        });
	   $("#linearea").sparkline([1,3,5,4,6,8,7,9,7,8,10,16,14,10], {
			type: 'line',
			width: '100%',
			height: '80',
			lineColor: '#06d79c',
			fillColor: '#06d79c',
			lineWidth: 2,
		});
        
   }
    var sparkResize;
 
        $(window).resize(function(e) {
            clearTimeout(sparkResize);
            sparkResize = setTimeout(sparklineLogin, 500);
        });
        sparklineLogin();

});
 // Morris-chart

  Morris.Area({
        element: 'morris-area-chart',
        data: [{
                    period: '2001',
                    Mobile: 0,
                    Leptop: 0,
                    TV: 0
                }, {
                    period: '2004',
                    Mobile: 80,
                    Leptop: 190,
                    TV: 5
                }, {
                    period: '2006',
                    Mobile: 140,
                    Leptop: 10,
                    TV: 65
                }, {
                    period: '2008',
                    Mobile: 90,
                    Leptop: 80,
                    TV: 7
                }, {
                    period: '2012',
                    Mobile: 142,
                    Leptop: 124,
                    TV: 120
                }, {
                    period: '2014',
                    Mobile: 18,
                    Leptop: 10,
                    TV: 40
                }, {
                    period: '2017',
                    Mobile: 169,
                    Leptop: 90,
                    TV: 10
                }


                ],
                lineColors: ['#f96868', '#398bf7', '#06d79c'],
                xkey: 'period',
                ykeys: ['Mobile', 'Leptop', 'TV'],
                labels: ['Site A', 'Site B', 'Site C'],
                pointSize: 0,
                lineWidth: 0,
                resize:true,
                fillOpacity: 0.5,
                behaveLikeLine: true,
                gridLineColor: '#e0e0e0',
                hideHover: 'auto'
        
    });
	
	Morris.Area({
        element: 'morris-area-chart2',
        data: [{
            period: '2011',
            SiteA: 0,
            SiteB: 0,
            
        }, {
            period: '2012',
            SiteA: 102,
            SiteB: 99,
            
        }, {
            period: '2013',
            SiteA: 180,
            SiteB: 160,
            
        }, {
            period: '2014',
            SiteA: 170,
            SiteB: 20,
            
        }, {
            period: '2015',
            SiteA: 80,
            SiteB: 150,
            
        }, {
            period: '2016',
            SiteA: 50,
            SiteB: 190,
            
        },
         {
            period: '2017',
            SiteA: 250,
            SiteB: 100,
           
        }],
        xkey: 'period',
        ykeys: ['SiteA', 'SiteB'],
        labels: ['Site A', 'Site B'],
        pointSize: 0,
        fillOpacity: 0.7,
        pointStrokeColors:['#48b0f7', '#06d79c'],
        behaveLikeLine: true,
        gridLineColor: '#e0e0e0',
        lineWidth: 0,
        smooth: false,
        hideHover: 'auto',
        lineColors: ['#48b0f7', '#f96197'],
        resize: true
        
    });
	
	
WeatherIcon.add('icon1'	, WeatherIcon.SLEET , {stroke:false , shadow:false , animated:true } );

	

}); // End of use strict
