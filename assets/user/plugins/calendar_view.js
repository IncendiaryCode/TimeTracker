
Date.prototype.getWeek = function () {
    var onejan = new Date(this.getFullYear(), 0, 1);
    return Math.ceil((((this - onejan) / 86400000) + onejan.getDay() + 1) / 7);
}


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
                        var cardHeader = $('<div class="card-header" />');
                        var cardHeaderRow = $('<div class="row pt-2" />');
                        var today = getTime();
                        if (data[x][y].start_time != null) {
                            var task_date = data[x][y].start_time.slice(0, 10);
                            if (today != task_date) {
                                $('.alert-box').show();
                            }
                        }
                        cardHeaderRow.append('<div class="col-6 text-left"><span class="vertical-line"></span>' + ' ' + data[x][y].start_time + '</div>');
                        var stopCol = $('<div class="col-6 text-right" />');
                        var timeUsed = minutesToTime(data[x][y].t_minutes);
                        stopCol.append('<i class="far fa-clock"></i>Total timeused=' + timeUsed);
                        cardHeaderRow.append(stopCol);
                        cardHeader.append(cardHeaderRow);

                        var cardInner = $("<div class='card card-style-1 animated fadeInUp'  />");
                        cardInner.append(cardHeader);

                        var cardBody = $("<div class='card-body' />");
                        cardBody.append(data[x][y].task_name);
                        cardInner.append(cardBody);
                        var cardFooter = $("<div class='card-footer'>");
                        var footerRow = $('<div class="row" />');
                        footerRow.append("<div class='col-6'> <i class='fab fa-twitter'></i> " + data[x][y].name + "</div>");

                        var footerRight = $("<div class='col-6 text-right card-actions'>");
                        //action Edit
                        var actionEdit = $('<a href="#" class="card-action action-edit text-success" id="action-edit"><i class="far fa-edit position_edit_icon animated fadeIn" data-toggle="tooltip" data-placement="top" title="edit"></i></a>');
                        actionEdit.attr('href', timeTrackerBaseURL + 'index.php/user/load_edit_task?t_id=' + data[x][y].id);

                        if (data[x][y].completed == 1) {
                        footerRight.append("<span class='text-success'>This task is completed.</span>");
                        }
                        footerRight.append(actionEdit);

                        footerRow.append(footerRight);
                        cardFooter.append(footerRow);
                        cardInner.append(cardFooter);
                        var cardCol = $("<div class='col-lg-6 mb-4 cardCol' />");
                        cardCol.append(cardInner);
                        $("#attachPanels").append(cardCol);
                    
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
    $.ajax({
        type: "GET",
        url: timeTrackerBaseURL + 'index.php/user/activity_chart',
        data: { 'chart_type': type, 'date': date },
        dataType: 'json',
        success: function (res) {
            if (res == "No activity in this week.") {
                if (type == 'weekly_chart') {
                document.getElementById('week-error').innerHTML = res;
                $('#weekly').hide();
                }
                if (type == 'daily_chart') {
                document.getElementById('daily-error').innerHTML = res;
                $('#daily').hide();
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
                $('#daily').show();
            }
            }
        }
    });
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
                                var value = week_count.slice(0, 4) + week_count.slice(-2) + item;
                                var date = __get_date(value.slice(4,6), value.slice(0,4)).toString();
                                var month = getMonth(date.slice(4,7));
                                var day1 = parseInt(getDay(date.slice(0,3)));
                                var day = day1+parseInt(date.slice(8,10));
                                var d = date.slice(11,15)+'-'+month+'-'+day;
                                document.getElementById('daily-chart').value=d;
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

  /*  else if (id == 'daily') {
        console.log(res);
        var length = res['data'][0].length;

        var arr = [];
        var totalMin = 0;
        for(var i=0; i< res.length; i++)
        {
            arr=arr+i;
        for(var j=0; j<res[i].length; j++)
        {
        var initialMin = 60;
        if (res[i][j] != null && res[i][j] != null ) {
            var x1 = res[0][i].slice(11,16);
            var x2 = res[1][i].slice(11,16);
            var startTimeMin = parseInt(x1.slice(0,2)*60)+parseInt(x1.slice(3,5));
            var endTimeMin = parseInt(x2.slice(0,2)*60)+parseInt(x2.slice(3,5));

            if ((initialMin < startTimeMin)) {
                totalMin += (endTimeMin - startTimeMin) % 60;
                }
                else
                {
            if ((initialMin <= startTimeMin) && (initialMin+120>endTimeMin )) {
               arr+i.push(60);     
            }else{arr+i.push(0);}
                arr+i.push(totalMin);
                    initialMin+=120;
                }
        }
            
        }
    }
    var barChartData = {
            labels: ['8Am', '9AM', '10AM','11AM','12AM','1PM','2PM','3PM','4PM','5PM','6PM','7PM','8AM'],
            datasets: [{
                label: 'task1',
                backgroundColor: "#8582fe",
                stack: 'Stack 0',
                data: [10,5,2,5]
            },{
                label: 'task2',
                backgroundColor: "#ca99fd",
                stack: 'Stack 1',
                data: [1,8,5,15]
            }, {
                label: 'task2',
                backgroundColor: "#eaa0fc",
                stack: 'Stack 1',
                data: [1,8,5,15]
            }]

        };

        var config = {
            type: 'bar',
                data: barChartData,
                options: {
                    title: {
                        display: true,
                        text: 'Daily details'
                    },
                    tooltips: {
                        mode: 'index',
                        intersect: false
                    },
                    responsive: true,
                    scales: {
                        xAxes: [{
                            
                            stacked: false,
                            beginAtZero: true,
                        }],
                        yAxes: [{
                            gridLines: {
                            display: false
                        },
                            stacked: false,
                            beginAtZero: true,
                        }]
                    }
                }
            }
            }
        */
    new Chart(chart, config);

}

function draw_customized_chart(res)
{
    var element = document.getElementById('daily');
    console.log(res);
    if (res['data'] != "No activity in this date.") {
    for(var i=0;i<res['data'][1][0].length; i++)
    {
        var start_time = res['data'][1][0][i].slice(10,16);
        var end_time = res['data'][1][1][i].slice(10,16);

        var start_time_min = (start_time.slice(0,3)*60)+ parseInt(start_time.slice(4,6));
        var end_time_min = (end_time.slice(0,3)*60)+ parseInt(end_time.slice(4,6));

        //calculate width for the graph.
        var interval = end_time_min - start_time_min;
        var x = 150;
        console.log(interval, start_time, end_time);
        console.log(x);
        var row1 = document.getElementsByClassName('cust_chart-row1');
        $(row1).css("width", x);
        $(row1).css("background-color", "#6d77ff");
        $(row1).css("color", "#6d77ff");
    }
    }
}

function printChart()
{
    
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
    console.log(year);
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
        dataTable.addRows([
        [new Date(year, month, day), value],
        
    ]);
    }
    var chart = new google.visualization.Calendar(document.getElementById('calendar_basic'));


    var options = {
        title: " ",
        calendar: { cellSize: 17 },
        height: 220,
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
        }
    });

});

window.onload = function () {
    if (document.getElementById('daily-chart')) {
        loadDailyChart();
        var date = document.getElementById('daily-chart').value;
        loadTask('daily_chart', date);
    }
}
