var panel_id= 0;
var id=0;
Date.prototype.getWeek = function () {
    var onejan = new Date(this.getFullYear(), 0, 1);
    return Math.ceil((((this - onejan) / 86400000) + onejan.getDay() + 1) / 7);
}

var daily_chart;
function loadTask(type, date) {
    $("#attachPanels").empty().html('<div class="col text-center"><div class="spinner-border" role="status" aria-hidden="true"></div> Loading...</div>');
    $.ajax({
        type: 'GET',
        url: timeTrackerBaseURL + 'index.php/user/load_task_data',
        data: { 'chart_type': type, 'date': date },
        success: function (values) {
            var data = JSON.parse(values);
            $("#attachPanels").empty();
            var timerModal = timerStopModal();
            for (x in data) {
                for (var y = 0; y < data[x].length; y++) {
                    if (data[x][y].running_task == 0)
                    { 
                        var cardHeader = $('<div class="card-header panel'+panel_id+'"'+' />');
                        var cardHeaderRow = $('<div class="row pt-2" />');
                        var today = getTime();
                        cardHeaderRow.append('<div class="col-6 text-left"><span class="vertical-line"></span>' + ' ' + data[x][y].start_time + '</div>');
                        var stopCol = $('<div class="col-6 text-right" />');
                        var timeUsed = minutesToTime(data[x][y].t_minutes);
                        stopCol.append('<i class="far fa-clock"></i> ' + timeUsed);
                        cardHeaderRow.append(stopCol);
                        cardHeader.append(cardHeaderRow);

                        var cardInner = $("<div class='card card-style-1 animated fadeInUp panel"+panel_id+"' />");
                        cardInner.append(cardHeader);

                        var cardBody = $("<div class='card-body' />");
                        cardBody.append(data[x][y].task_name);
                        cardInner.append(cardBody);
                        var cardFooter = $("<div class='card-footer panel"+panel_id+"'>");
                        var footerRow = $('<div class="row" />');
                        footerRow.append("<div class='col-6'> <i class='fab fa-twitter'></i> " + data[x][y].name + "</div>");

                        var footerRight = $("<div class='col-6 text-right card-actions'>");
                        //action Edit
                        var actionEdit = $('<a href="#" class="card-action action-edit text-white" id="action-edit"><i class="far fa-edit position_edit_icon animated fadeIn" data-toggle="tooltip" data-placement="top" title="edit"></i></a>');
                        actionEdit.attr('href', timeTrackerBaseURL + 'index.php/user/load_edit_task?t_id=' + data[x][y].id);

                        if (data[x][y].completed == 1) {
                        footerRight.append("<span class='text-success'>This task is completed.</span>");
                        }
                        footerRight.append(actionEdit);

                        var mode = localStorage.getItem('dark_mode');
                        if (mode == "checked") {
                            cardInner.css("background", "#000000");
                            cardHeader.css("background", "#000000");
                            cardFooter.css("background", "#000000");
                        }
                        footerRow.append(footerRight);
                        cardFooter.append(footerRow);
                        cardInner.append(cardFooter);
                        var cardCol = $("<div class='col-lg-6 mb-4 cardCol' id='panel"+panel_id+"'></div>");
                        cardCol.append(cardInner);
                        $("#attachPanels").append(cardCol);
                        if(type == "daily_chart")
                        {
                        panel_id++;
                        }
                    }
                }
            }
        }
    });
}

function loadDailyChart() {
    var date = document.getElementById('daily-chart').value;
    if (date == "" || date == " " || date == null) {
        var today = new Date();
        document.getElementById("daily-chart").value = today.getFullYear() + '-' + ('0' + (today.getMonth() + 1)).slice(-2) + '-' + ('0' + today.getDate()).slice(-2);

        date = today.getFullYear() + '-' + ('0' + (today.getMonth() + 1)).slice(-2) + '-' + ('0' + today.getDate()).slice(-2);
        document.getElementById("daily-chart").setAttribute("max", date);

        date = document.getElementById('daily-chart').value;
        retrieveChartData('daily_chart', date);
    } else {
     retrieveChartData('daily_chart', date); }
}

function loadWeeklyChart() {
    if (document.getElementById('weekly-chart')) {
        var weekControl = document.querySelector('input[type="week"]');
        var week = document.getElementById('weekly-chart').value;
        if (week == "" || week == " " || week == null) {
            var today = new Date(); // get current date
            var weekNumber = today.getWeek(); // Returns the week number as an integer
            weekControl.value = today.getFullYear() + '-W' + weekNumber;
            week = today.getFullYear() + '-W' + weekNumber;
            document.getElementById("weekly-chart").setAttribute("max", week);
            retrieveChartData('weekly_chart', week);
        } else { retrieveChartData('weekly_chart', week); }

    }
}

function retrieveChartData(type, date) {
    $("#print-chart").empty();
    $.ajax({
        type: "GET",
        url: timeTrackerBaseURL + 'index.php/user/activity_chart',
        data: { 'chart_type': type, 'date': date },
        dataType: 'json',
        success: function (res) {
            if (res["data"] == "No activity in this date.") {
                if (type == 'weekly_chart') {
                document.getElementById('week-error').innerHTML = res['data'];
                $('#weekly').hide();
                }
                if (type == 'daily_chart') {
                document.getElementById('daily-error').innerHTML = res['data'];
                //$('#daily').hide();
                //$('#attachPanels').hide();
                }
            }
            else
            {
            if (type == 'weekly_chart') {   
                loadTask(type, date);
                document.getElementById('week-error').innerHTML = " ";
                drawChart(type, res);
                $('#weekly').show();
            }
            if (type == 'daily_chart') {   
                loadTask(type, date);
                document.getElementById('daily-error').innerHTML = " ";
                draw_customized_chart(res);
                //$('#daily').show();
                //$('#attachPanels').show();
            }
            }
        }
    });
}

function dateFromDay(year, day){
  var date = new Date(year, 0); // initialize a date in `year-01-01`
  return new Date(date.setDate(day)); // add the number of days
}

function drawChart(type, res) {
   
    var chart = document.getElementById('weekly').getContext('2d');
    gradient = chart.createLinearGradient(0, 0, 0, 600);

    gradient.addColorStop(0, '#7078ff');
    gradient.addColorStop(0.5, '#e58dfb');
    gradient.addColorStop(1, '#ffffff');

        var const_lable = ['Mon','Tue','Wed','Thu','Fri','Sat','Sun']
        var labels = [];
        var data = [];
        for(var i=0, j=0; i<const_lable.length; i++)
        {
            if (res.labels[j] == const_lable[i]) {
                labels[i] = res.labels[j];
                data[i] = res.data[j];
                j++;
            }
            else
            {
                labels[i] = const_lable[i];
                data[i] = 0;
            }
        }
        var config = {
            type: 'bar',
            data: {
                labels: labels,
                datasets: [{
                    label: 'Time interval',
                    borderColor: "#7078ff",
                    backgroundColor: gradient,
                    data: data,
                    
                }]
            },
            options: {
                responsive: true,
                title: {
                    display: false
                },
                tooltips: {
                    enabled: true,
                    callbacks: {
                        label: function (tooltipItem, data) {
                            var item = tooltipItem.xLabel;
                            var week_count = document.getElementById('weekly-chart').value;
                            weekly.onclick = function () {
                                var day = getDay(item)-1;
                                var year = parseInt(week_count.slice(0,4));
                                var day_from_week = parseInt(week_count.slice(-2)-1)*7;
                                day_from_week = day_from_week+parseInt(day);
                                var new_day = dateFromDay(year, day_from_week).toString();
                                new_day = new_day.slice(11,15)+'-'+getMonth(new_day.slice(4,7))+'-'+new_day.slice(8,10)
                                document.getElementById('daily-chart').value = new_day;
                                $('#chart-navigation a[href="#daily-view"]').tab('show');
                            }
                        }
                    }
                },
                hover: {
                    mode: 'index'
                },
                scales: {
                    xAxes: [{
                        gridLines: {
                            display: false
                        }
                    }],
                    yAxes: [{
                        gridLines: {
                            display: false,
                            drawBorder: true
                        },
                        ticks: {
                            display: true,
                            beginAtZero: true,
                            stacked: true
                        },
                        scaleLabel: {
                            display: true,
                            labelString: 'Time in hours',
                        }
                    }]
                }
            }
        };
        if(daily_chart) daily_chart.destroy();
        daily_chart = new Chart(chart, config);
}

function draw_customized_chart(res)
{  
    var element = document.getElementById('daily');
    var pixel= [];
    var top = 25; 
    var top1 = top;
    var window_width = $('.cust_daily_chart').width();
    var p_left = parseInt(window_width)/13;
    if (res['data'] != "No activity in this date.") {
    var color = "000000";
    for(var i=0; i<res['data'][1][0].length; i++)
    {
        color= parseInt(color)+123456;

        var start_time = res['data'][1][0][i].slice(10,16);
        var end_time = res['data'][1][1][i].slice(10,16);

        var start_time_min = (start_time.slice(0,3)*60)+ parseInt(start_time.slice(4,6));
        var end_time_min = (end_time.slice(0,3)*60)+ parseInt(end_time.slice(4,6));

        //calculate width for the graph.
        var interval = res['data'][1][2][i];

        var width = ((interval/60)*p_left);
        var start_time_pixel = (((start_time_min/60)-8)*p_left);

        for(var k=0; k<pixel.length; k++)
        {
            var v = 0;
            if ((start_time_pixel >= pixel[k][0]) && (width < pixel[k][1])) {
             v = 25;
                if ((start_time_pixel+width) >= window_width ) {
                width = window_width -(start_time_pixel);
                }
                var pixel1 = pixel;
                pixel1[k][0] = null;
                pixel1[k][1] = null;
                for(var e=0; e<pixel1.length; e++)
                {
                    if ((start_time_pixel+width) >= window_width ) {
                        width = window_width -(start_time_pixel);
                        }
                    if (((start_time_pixel >= pixel1[e][0]) && (width < pixel1[e][1]))) {
                    v=v+25;
                    printChart(start_time_pixel, width, 300-v, color);
                    break;
                    }
                    else
                    {
                    printChart(start_time_pixel, width, 300-v, color);
                    break;
                    }
                }
            }
        }
    
        if ((top1==top) && (pixel.length != 0)) {
            if(v==0)
            {
            if ((start_time_pixel+width) >= window_width ) {
                    width = window_width -(start_time_pixel);
                    }
                printChart(start_time_pixel, width, 300, color);
            }
        }
     if (pixel.length == 0) {
        if ((start_time_pixel+width) >= window_width ) {
                width = window_width -(start_time_pixel);
                }
        printChart(start_time_pixel, width, 300, color);
     }
    pixel.push([start_time_pixel, width]); 
    }
    }
width = 0;
start_time_pixel = 0;
}

var last_index
function printChart(start, width, top, color)
{
    var row = $('<span class="print-chart-row1" id="new-daily-chart'+id+'">.<input type = "hidden" value = '+id+' ></span>');
    $(row).css("margin-left", start);
    $(row).css("top", top);
    $(row).css("width", width);
    $(row).css("backgroundColor", '#'+color);
    $(row).css("color", '#'+color);
    $("#print-chart").append(row);
    
    $('.print-chart-row1').unbind().click(function()
    {
        var ele = document.getElementById(this.id);
        var index = ele.childNodes[1].value;
        console.log(last_index);
        if(last_index)
        {
            $('.panel'+last_index).css("backgroundColor", "#ffffff");
        }
        last_index = index;
        $('.panel'+index).css("backgroundColor", "#f5d0fe");
        var elmnt = document.getElementById('panel'+index);
        elmnt.scrollIntoView();
    });
    id++;
}
function getMonth(month)
{   
    var month_no = 0;
    switch(month)
    {
        case "Jan": month_no = 1; break;
        case "Feb": month_no = 2; break;
        case "Mar": month_no = 3; break;
        case "Apr": month_no = 4; break;
        case "May": month_no = 5; break;
        case "Jun": month_no = 6; break;
        case "Jul": month_no = 7; break;
        case "Aug": month_no = 8; break;
        case "Sep": month_no = 9; break;
        case "Oct": month_no = 10; break;
        case "Nov": month_no = 11; break;
        case "Dec": month_no = 12; break;
    }
    return month_no;
}
function getDay(day)
{   
    var day_no = 0;
    switch(day)
    {
        case "Sun": day_no = 0; break;
        case "Mon": day_no = 1; break;
        case "Tue": day_no = 2; break;
        case "Wed": day_no = 3; break;
        case "Thu": day_no = 4; break;
        case "Fri": day_no = 5; break;
        case "Sat": day_no = 6; break;
    }
    return day_no;
}

function __get_date(w,y)
{
    var d = (1 + (w - 1) * 7); // 1st of January + 7 days for each week

    return new Date(y, 0, d);
}
function loadMonthlyChart() {
    var year = document.getElementById('monthly-chart').value;
    if (year == "" || year == " " || year == null) {
        var cur_year = parseInt(new Date().toString().slice(10, 15));
        document.getElementById('monthly-chart').value = cur_year;
        year = cur_year;
        document.getElementById("monthly-chart").setAttribute("max", cur_year);
    }
    $.ajax({
        type: "GET",
        url: timeTrackerBaseURL + 'index.php/user/activity_chart',
        data: { 'chart_type': "monthly_chart", 'date': year },
        dataType: 'json',
        success: function (res) {
            google.charts.load("current", { packages: ["calendar"] });
            google.setOnLoadCallback(drawMonthlyChart(res));
            loadTask("monthly_chart", year);
        }
    });
}

function drawMonthlyChart(res) {
    var dataTable = new google.visualization.DataTable();
    dataTable.addColumn({ type: 'date', id: 'Date' });
    dataTable.addColumn({ type: 'number', id: 'Won/Loss' });
    for(var k=0; k<res["data"].length; k++)
    {
        var year = parseInt(res['data'][k][0].slice(0,4).toString());
        var month = parseInt(res['data'][k][0].slice(5,7))-1;
        var day = parseInt(res['data'][k][0].slice(8,10));
        var value = parseInt(res['data'][k][1]);
        dataTable.addRows([[new Date(year, month, day), value],
    ]);
    }
    var chart = new google.visualization.Calendar(document.getElementById('calendar_basic'));

    var chart_width = $('#calendar_basic').width();
    var cellsize = chart_width/60;
    var options = {
        title: " ",
        calendar: { cellSize: cellsize,
        yearLabel: {fontSize: 1}
        },
        noDataPattern: {
           backgroundColor: '#ebedf0',
         },
         monthOutlineColor: {
            stroke: 'white',
            strokeOpacity: 0.0,
            strokeWidth: 2
      },
    };
    chart.draw(dataTable, options);
}

$(document).ready(function () {
    //Tab Change
    var win_width = $('.cust_daily_chart').width();
    var p_l = parseInt(win_width)/23;
    $('.cust_chart').css("padding-left",p_l);
    $('#chart-navigation a').on('shown.bs.tab', function (event) {
        var x = $(event.target).attr("href"); // active tab
        var y = $(event.relatedTarget); // previous tab
        if (x == '#daily-view') {
            loadDailyChart();
            var date = document.getElementById('monthly-chart').value;
            loadTask('weekly_chart', date);
        }
        if (x == '#weekly-view') {
            loadWeeklyChart();
            var date = document.getElementById('weekly-chart').value;
            loadTask('weekly_chart', date);
        }
        if (x == '#monthly-view') {
            loadMonthlyChart();
            var year = document.getElementById('monthly-chart').value;
        }
    });
    $('#new-daily-chart0').click(function()
    {
        alert("clicked");
    });

});

window.onload = function () {
    if (document.getElementById('daily-chart')) {
        loadDailyChart();
        var date = document.getElementById('daily-chart').value;
        loadMonthlyChart();
        loadTask('daily_chart', date);
    }
}
