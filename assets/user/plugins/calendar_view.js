var panel_id = 0;
var graph_id = 0;
Date.prototype.getWeek = function () {
    var onejan = new Date(this.getFullYear(), 0, 1);
    return Math.ceil((((this - onejan) / 86400000) + onejan.getDay() + 1) / 7);
}

function minutesToTime(mins) {
    var total_mins = Number(mins * 60);
    var h = Math.floor(total_mins / 3600);
    var m = Math.floor(total_mins % 3600 / 60);


    var hDisplay = h > 0 ? h + (h == 1 ? "h. " : "h:") : "";
    var mDisplay = m > 0 ? m + (m == 1 ? "m. " : "m.") : "";
    return hDisplay + mDisplay;
}

var daily_chart;


function draw_chart_ards(data) {
    for (x in data) {
        for (var y = 0; y < data[x].length; y++) {
            var cardHeader = $('<div class="card-header card-header" />');
            var cardHeaderRow = $('<div class="row pt-2" />');
            var today = getTime();
            if (data[x][y].start_time != null) {
                var task_date = data[x][y].start_time.slice(0, 10);
                if (today != task_date) {
                    $(".alert-box").show();
                }
            }
            if (data[x][y].start_time == null) {
                cardHeaderRow.append(
                    '<div class="col-6 text-left"><span class="vertical-line"></span>Not yet started.</div>'
                );
            } else {
                cardHeaderRow.append(
                    '<div class="col-6 text-left"><span class="vertical-line"></span>' +
                    " " +
                    data[x][y].start_time +
                    "</div>"
                );
            }
            var stopCol = $(
                '<div class="col-6 text-right"  id="btn-stop' +
                data[x][y].id +
                '" />'
            );

            if (data[x][y].running_task == 0) {
                /*check whether task is ended or not*/
                var timeUsed = minutesToTime(data[x][y].t_minutes);
                stopCol.append('<i class="far fa-clock"></i> ' + timeUsed);
            } else {
                var id = data[x][y].id;
                if (data[x][y].start_time != null) {
                    var stopButton = $(
                        '<span class=""><i class="fa fa-hourglass-1"></i> Running</span>'
                    ).data("taskid", data[x][y].id);
                }
                stopCol.append(stopButton);
            }

            cardHeaderRow.append(stopCol);
            cardHeader.append(cardHeaderRow);

            var cardInner = $(
                "<div class='card card-style-1 animated fadeIn' />"
            );
            cardInner.append(cardHeader);

            var cardBody = $("<div class='card-body' />");
            cardBody.append(data[x][y].task_name);
            cardInner.append(cardBody);
            var cardFooter = $("<div class='card-footer card-footer'>");
            var footerRow = $('<div class="row" />');

            footerRow.append(((data[x][y].image_name !== null) ?
                "<div class='col-12'> <img src=" +
                data[x][y].image_name +
                " width='20px;'> " : '') +
                data[x][y].project +
                "</div>"
            );

            var footerRight = $(
                "<div class='card-actions' id='footer-right-" +
                data[x][y].id +
                "'>"
            );
            //action Edit
            var actionEdit = $(
                '<a href="#" class=" pl-2  text-white " id="action-edit"><i class="far fa-edit action-play " data-toggle="tooltip" data-placement="top" title="edit"></i></a>'
            );
            actionEdit.attr(
                "href",
                timeTrackerBaseURL +
                "index.php/user/load_edit_task?t_id=" +
                data[x][y].id
            );
            footerRight.append(actionEdit);
            cardFooter.append(footerRow);
            cardInner.append(cardFooter);

            //add a overlay layer
            var cardOverlay = $("<div class='card-overlay' />");
            cardInner.append(cardOverlay);

            //add action overlay
            var cardActions = $("<div class='card-action-overlay' />");
            cardActions.append(footerRight);
            cardInner.append(cardActions);

            var cardCol = $("<div class='col-lg-6 mb-4 card-col' />");
            cardCol.append(cardInner);

            $("#attachPanels").append(cardCol);

            var id = +data[x][y].id;
            cardCol.click(function () {
                $('.print-chart-row' + id).addClass('animated zoomIn');
            });
            if (data[x][y].running_task == 1 && data[x][y].start_time != null) {
                //change background of current running task entries.
                document.getElementsByClassName("title").innerText +=
                    data[x][y].task_name;
            }
        }
    }
}
function loadTask(type, date) {
    $("#attachPanels").empty().html('<div class="col text-center"><div class="spinner-border" role="status" aria-hidden="true"></div> Loading...</div>');
    $.ajax({
        type: 'GET',
        url: timeTrackerBaseURL + 'index.php/user/load_task_data',
        data: { 'chart_type': type, 'date': date },
        success: function (values) {
            if (values == "No activity in this date.") {
                $("#attachPanels").empty();
                $("#attachPanels").empty().html('<div class="col text-center"><div class="spinner-border" role="status" aria-hidden="true"></div> Loading...</div>');
            } else {
                var data = JSON.parse(values);
                $("#attachPanels").empty();
                draw_chart_ards(data);
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
        retrieveChartData('daily_chart', date);
    }
}

function loadWeeklyChart() {
    if (document.getElementById('weekly-chart')) {
        var weekControl = document.querySelector('input[type="week"]');
        var week = document.getElementById('weekly-chart').value;
        if (week == "" || week == " " || week == null) {
            var today = new Date(); // get current date
            var weekNumber = today.getWeek(); // Returns the week number as an integer
            if (weekNumber.toString().length == 1) {
                weekNumber = '0' + weekNumber;
            }
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
                    $("#attachPanels").empty();
                    $('#weekly').hide();
                }
                if (type == 'daily_chart') {
                    document.getElementById('daily-error').innerHTML = res['data'];
                    $("#attachPanels").empty();
                }
            } else {
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
                }
            }
        }
    });
}

function dateFromDay(year, day) {
    var date = new Date(year, 0); // initialize a date in year-01-01
    return new Date(date.setDate(day)); // add the number of days
}
function drawChart(type, res) {

    if (res == "No activity in this week.") {
        if (daily_chart) daily_chart.destroy();
        document.getElementById('week-error').innerHTML = "No activity in this week.";
        $("#attachPanels").empty();
    } else {
        var week_chart = document.getElementById('weekly').getContext('2d');
        gradient = week_chart.createLinearGradient(0, 0, 0, 600);

        gradient.addColorStop(0, '#7078ff');
        gradient.addColorStop(0.5, '#e58dfb');
        gradient.addColorStop(1, '#ffffff');

        var const_lable = ['Sun', 'Mon', 'Tue', 'Wed', 'Thu', 'Fri', 'Sat']
        var labels = [];
        var data = [];
        for (var i = 0, j = 0; i < const_lable.length; i++) {
            if (res.labels[j] == const_lable[i]) {
                labels[i] = res.labels[j];
                data[i] = res.data[j];
                j++;
            } else {
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
                                var day = getDay(item) - 2;
                                var year = parseInt(week_count.slice(0, 4));
                                var day_from_week = parseInt(week_count.slice(-2) - 1) * 7;
                                day_from_week = day_from_week + parseInt(day);
                                var new_day = dateFromDay(year, day_from_week).toString();
                                new_day = new_day.slice(11, 15) + '-' + getMonth(new_day.slice(4, 7)) + '-' + new_day.slice(8, 10)
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
                            display: true,
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
        }
    };
    if (daily_chart) daily_chart.destroy();
    daily_chart = new Chart(week_chart, config);
}

function draw_customized_chart(res) {
    var element = document.getElementById('daily');
    var pixel = [];
    var top = 25;
    var top1 = top;
    console.log(res)
    var window_width = $('.cust_daily_chart').width();
    var p_left = parseInt(window_width) / 12;
    if (res['data'] != "No activity in this date.") {

        for (var i = 0; i < res['data'][1].length; i++) {
            //color = parseInt(color) + 123456;

            var start_time = res['data'][1][i]["start_time"].slice(10, 16);
            var end_time = res['data'][1][i]["end_time"].slice(10, 16);

            var start_time_min = (start_time.slice(0, 3) * 60) + parseInt(start_time.slice(4, 6));
            var end_time_min = (end_time.slice(0, 3) * 60) + parseInt(end_time.slice(4, 6));
            //calculate width for the graph.

            var interval = res['data'][1][i]['total_minutes'];
            var task_name = res['data'][2][i];

            var width = ((interval / 60) * p_left);
            if (start_time_min < 480) // graph leess than 8 am is not shown
            {
                start_time_min = 480;
            }
            var start_time_pixel = (((start_time_min / 60) - 8) * p_left);
            var end_time_pixel = (((end_time_min / 60) - 8) * p_left);

            var v = 0;
            for (var k = 0; k < pixel.length; k++) {
                if ((start_time_pixel >= pixel[k][0]) && (start_time_pixel < pixel[k][1])) {
                    v = 25;
                    var pixel1 = pixel;
                    pixel1[k][0] = null;
                    pixel1[k][1] = null;
                    for (var e = 0; e < pixel1.length; e++) {
                        if ((start_time_pixel + width) >= window_width) {
                            width = window_width - (start_time_pixel);
                        }
                        if (((start_time_pixel >= pixel1[e][0]) && (start_time_pixel < pixel1[e][1]))) {
                            v = v + 25;
                            printChart(start_time_pixel, width, ((332) - v), task_name, res['data'][0][i]);
                            break;
                        } else {
                            printChart(start_time_pixel, width, ((332) - v), task_name, res['data'][0][i]);
                            break;
                        }
                    }
                }
            }
            if ((top1 == top) && (pixel.length != 0)) {
                if (v == 0) {
                    if ((start_time_pixel + width) >= window_width) {
                        width = window_width - (start_time_pixel);
                    }
                    printChart(start_time_pixel, width, (332), task_name, res['data'][0][i]);
                }
            }
            if (pixel.length == 0) {
                if ((start_time_pixel + width) >= window_width) {
                    width = window_width - (start_time_pixel);
                }
                printChart(start_time_pixel, width, (332), task_name, res['data'][0][i]);
            }
            pixel.push([start_time_pixel, end_time_pixel]);
        }
    }
    width = 0;
    start_time_pixel = 0;
}
var last_index;
var last_task_name = [];
var same_task = 0;
var color = "000000";
function printChart(start, width, top, task_name, id) {

    for (var i = 0; i < last_task_name.length; i++) {
        if (task_name == last_task_name[i]['task_name']) {

            color = last_task_name[i]['color'];
            break;
        }
        else {
            color = parseInt(color) + 123456;
        }
    }
    var row = $("<span class='positon-chart print-chart-row" + id + "'  data-toggle='tooltip' data-placement='top' title=" + task_name + " id='new-daily-chart" + graph_id + "'>.<input type = 'hidden' value = " + graph_id + "></span>");
    $(row).css("padding-left", start);
    $(row).css("position", "absolute");
    $(row).css("top", top);
    $(row).css("left", width);
    $(row).css("backgroundColor", '#' + color);
    $(row).css("color", '#' + color);
    $("#print-chart").append(row);
    $('.print-chart-row' + id).unbind().click(function () {
        var ele = document.getElementById(this.id);
        var index = ele.childNodes[1].value;
        if (last_index) {
            //$('.panel' + last_index).css("backgroundColor", "#ffffff");
        }
        last_index = id;
        //$('.panel' + id).css("backgroundColor", "#f5d0fe");
        var elmnt = document.getElementById('panel' + index);
        elmnt.scrollIntoView();
    });
    graph_id++;
    last_task_name.push({ task_name, color });
}
last_index = undefined;

function getMonth(month) {
    var month_no = 0;
    switch (month) {
        case "Jan":
            month_no = 1;
            break;
        case "Feb":
            month_no = 2;
            break;
        case "Mar":
            month_no = 3;
            break;
        case "Apr":
            month_no = 4;
            break;
        case "May":
            month_no = 5;
            break;
        case "Jun":
            month_no = 6;
            break;
        case "Jul":
            month_no = 7;
            break;
        case "Aug":
            month_no = 8;
            break;
        case "Sep":
            month_no = 9;
            break;
        case "Oct":
            month_no = 10;
            break;
        case "Nov":
            month_no = 11;
            break;
        case "Dec":
            month_no = 12;
            break;
    }
    return month_no;
}

function getDay(day) {
    var day_no = 0;
    switch (day) {
        case "Sun":
            day_no = 0;
            break;
        case "Mon":
            day_no = 1;
            break;
        case "Tue":
            day_no = 2;
            break;
        case "Wed":
            day_no = 3;
            break;
        case "Thu":
            day_no = 4;
            break;
        case "Fri":
            day_no = 5;
            break;
        case "Sat":
            day_no = 6;
            break;
    }
    return day_no;
}

function __get_date(w, y) {
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
    if (document.getElementById('calendar_basic')) {
        var dataTable = new google.visualization.DataTable();
        dataTable.addColumn({ type: 'date', id: 'Date' });
        dataTable.addColumn({ type: 'number', id: 'Won/Loss' });
        for (var k = 0; k < res["data"].length; k++) {
            var year = parseInt(res['data'][k][0].slice(0, 4).toString());
            var month = parseInt(res['data'][k][0].slice(5, 7)) - 1;
            var day = parseInt(res['data'][k][0].slice(8, 10));
            var value = res['data'][k][1];
            dataTable.addRows([
                [new Date(year, month, day), value],
            ]);
        }
    }
    var chart = new google.visualization.Calendar(document.getElementById('calendar_basic'));

    var chart_width = $('#calendar_basic').width();
    var cellsize = chart_width / 60;
    var options = {
        title: " ",
        calendar: {
            cellSize: cellsize,
            yearLabel: { fontSize: 1 }
        },
        noDataPattern: {
            backgroundColor: '#ebedf0',
        },
        monthOutlineColor: {
            stroke: 'white',
            strokeOpacity: 0.0,
            strokeWidth: 2,
        },
    };

    function selectHandler() {
        var selectedItem = chart.getSelection();
        if (selectedItem[0]['row'] != undefined) {
            var topping = dataTable.getValue(selectedItem[0].row, 0);
            var day_from_year = (topping.toString().slice(11, 15)) + '-' + getMonth(topping.toString().slice(4, 7)) + '-' + topping.toString().slice(8, 10);
            document.getElementById('daily-chart').value = day_from_year;
            $('#chart-navigation a[href="#daily-view"]').tab('show');
        } else {
            alert("No activities in this date");
        }
    }

    google.visualization.events.addListener(chart, 'select', selectHandler);
    chart.draw(dataTable, options);
}

$(document).ready(function () {
    //Tab Change
    var win_width = $('.cust_daily_chart').width();
    var p_l = parseInt(win_width) / 23;
    $('.cust_chart').css("padding-left", p_l);

    // var daily_value = document.getElementById('daily-chart').value;

    $("#daily-chart").change(function () {
        loadDailyChart();
    });
    $("#weekly-chart").change(function () {
        loadWeeklyChart();
    });
    $("#monthly-chart").change(function () {
        loadMonthlyChart();
    });

    /*daily_value.*/
    if (win_width < 400) {
        document.getElementById('chart-labels').remove();
        var new_lebels = $('<p class="cust_daily_chart" ><span class="">8AM</span><span class="cust_chart">10AM</span><span class="cust_chart">12AM</span><span class="cust_chart">2PM</span><span class="cust_chart">4PM</span><span class="cust_chart">6PM</span><span class="cust_chart">8PM</span></p>');
        $('#daily').append(new_lebels);
        var p_l = parseInt(win_width) / 24;
        $('.cust_chart').css("padding-left", p_l);
    }
    if ((win_width < 1000) && (win_width > 400)) {
        document.getElementById('chart-labels').css("display", "none");
        var new_lebels = $('<p class="cust_daily_chart"><span class="">8AM</span><span class="cust_chart">10AM</span><span class="cust_chart">12AM</span><span class="cust_chart">2PM</span><span class="cust_chart">4PM</span><span class="cust_chart">6PM</span><span class="cust_chart">8PM</span></p>');
        $('#daily').append(new_lebels);
        var p_l = parseInt(win_width) / 10;
        $('.cust_chart').css("padding-left", p_l);
    }

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

});

window.onload = function () {
    if (document.getElementById('daily-chart')) {
        loadDailyChart();
        var date = document.getElementById('daily-chart').value;
        loadMonthlyChart();
        loadTask('daily_chart', date);
    }
}