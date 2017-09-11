/* ------------------------------------------------------------------------------
 *
 *  # Echarts - columns and waterfalls
 *
 *  Columns and waterfalls chart configurations
 *
 *  Version: 1.0
 *  Latest update: August 1, 2015
 *
 * ---------------------------------------------------------------------------- */

function updateChart(item) {
    var url = item.attr("data-url");
    var id = item.attr("id");
    
    $.ajax({
        method: "GET",
        url: url,
    })
    .done(function( msg ) {
        showChart(id, JSON.parse(msg));
    });
}


var charts = new Object();

function showChart(id, data) {

    // Set paths
    // ------------------------------

    require.config({
        paths: {
            echarts: APP_URL + '/assets/js/plugins/visualization/echarts'
        }
    });


    // Configuration
    // ------------------------------

    require(
        [
            'echarts',
            'echarts/theme/custom_color',
            'echarts/chart/bar',
            'echarts/chart/line',
            'echarts/chart/pie',
            'echarts/chart/funnel',
        ],


        // Charts setup
        function (ec, acelle) {


            // Initialize charts
            // ------------------------------            
            
            charts[id] = ec.init(document.getElementById(id), acelle);

            // Charts setup
            // ------------------------------


            //
            // Basic columns options
            //
            
            if (data['pie'] == 1) {
                basic_columns_options = {
                    // Add title
                    title: {
                        text: data['title'],
                        // subtext: 'Open source information',
                        x: 'center'
                    },
    
                    // Add tooltip
                    tooltip: {
                        trigger: 'item',
                        formatter: "{a} <br/>{b}: {c} ({d}%)"
                    },
    
                    // Add legend
                    legend: {
                        orient: 'vertical',
                        x: 'left',
                        data: data["bar_names"]
                    },
    
                    // Display toolbox
                    toolbox: {
                        show: true,
                        orient: 'vertical',
                        feature: {                            
                            dataView: {
                                show: true,
                                readOnly: false,
                                title: 'View data',
                                lang: ['View chart data', 'Close', 'Update']
                            },
                            restore: {
                                show: true,
                                title: 'Restore'
                            },
                            saveAsImage: {
                                show: true,
                                title: 'Save as image',
                                lang: ['Save']
                            }
                        }
                    },
                    
                    // Add series
                    series: data["data"],
                }
            }
            else if (data['horizontal'] == 1) {
                basic_columns_options = {
    
                    // Setup grid
                    grid: {
                        x: 45,
                        x2: 10,
                        y: 85,
                        y2: 25
                    },
    
                    // Add tooltip
                    tooltip: {
                        trigger: 'axis',
                        axisPointer: {
                            type: 'shadow'
                        }
                    },
    
                    // Add legend
                    legend: {
                        data: data["bar_names"]
                    },
    
                    // Enable drag recalculate
                    calculable: true,
    
                    // Horizontal axis
                    yAxis: [{
                        type: 'category',
                        data: data["columns"]
                    }],
    
                    // Vertical axis
                    xAxis: [{
                        type: 'value',
                        boundaryGap: [0, 0.01]
                    }],
    
                    // Add series
                    series: data["data"],
    
                };
            } else {
                basic_columns_options = {
    
                    // Setup grid
                    grid: {
                        x: 40,
                        x2: 40,
                        y: 35,
                        y2: 25
                    },
    
                    // Add tooltip
                    tooltip: {
                        trigger: 'axis'
                    },
    
                    // Add legend
                    legend: {
                        data: data["bar_names"]
                    },
    
                    // Enable drag recalculate
                    calculable: true,
    
                    // Horizontal axis
                    xAxis: [{
                        type: 'category',
                        data: data["columns"]
                    }],
    
                    // Vertical axis
                    yAxis: [{
                        type: 'value'
                    }],
    
                    // Add series
                    series: data["data"],

                };
            }

            // Apply options
            // ------------------------------

            charts[id].setOption(basic_columns_options);


            // Resize charts
            // ------------------------------

            window.onresize = function () {
                setTimeout(function () {
                    for (var key in charts) {
                        charts[key].resize();
                    }
                    
                }, 200);
            }
        }
    );
}