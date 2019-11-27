function retrieveChartData(type, date) {
    $.ajax({
        type: "GET",
        url: timeTrackerBaseURL + 'index.php/user/activity_chart',
        data: { 'chart_type': type, 'date': date },
        dataType: 'json',
        success: function (res) {
            drawChart(type,res);
        }
    });
}

Date.prototype.getWeek = function () {
    var onejan = new Date(this.getFullYear(), 0, 1);
    return Math.ceil((((this - onejan) / 86400000) + onejan.getDay() + 1) / 7);
}

function loadDailyChart() {

    var date = document.getElementById('daily-chart').value;
    console.log("date", date);
    if (date == "" || date == " " || date == null) {
        var today = new Date();
        document.getElementById("daily-chart").value = today.getFullYear() + '-' + ('0' + (today.getMonth() + 1)).slice(-2) + '-' + ('0' + today.getDate()).slice(-2);

        date = today.getFullYear() + '-' + ('0' + (today.getMonth() + 1)).slice(-2) + '-' + ('0' + today.getDate()).slice(-2);
        document.getElementById("daily-chart").setAttribute("max", date);

        date = document.getElementById('daily-chart').value;
        retrieveChartData('daily_chart', date);
    } else { retrieveChartData('daily_chart', date); }

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

function drawChart(type,res) {
    console.log("res", res);
    if (type == 'daily_chart') {
        id = "daily";
    } else if (type == 'weekly_chart') {
        id = 'weekly';
    }

    var chart = document.getElementById(id).getContext('2d');
    gradient = chart.createLinearGradient(0, 0, 0, 600);

    gradient.addColorStop(0, '#7078ff');
    gradient.addColorStop(0.5, '#e58dfb');
    gradient.addColorStop(1, '#ffffff');

    if (id == 'weekly') {
        var config = {
            type: 'bar',
            data: {
                labels: res.labels, //['12AM', '3AM', '6AM', '9AM', '12PM', '3PM', '6PM', '9PM', '12AM'],
                datasets: [{
                    label: 'Time interval',
                    borderColor: "#7078ff",
                    backgroundColor: gradient,
                    data: res.data //[1, 1, 0, 1, 0, 0, 1]
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
                                /*window.location.href = 'daily_details.php?value=' + value;*/
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
                            drawBorder: false
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
    } else if (id == 'daily') {
        var config = {
            type: 'bar',
            data: {
                labels: res.labels, //['12AM', '3AM', '6AM', '9AM', '12PM', '3PM', '6PM', '9PM', '12AM'],
                datasets: [{
                    label: 'Time interval',
                    borderColor: "#7078ff",
                    backgroundColor: '#7078ff',
                    data: res.data //[1, 1, 0, 1, 0, 0, 1]
                }]
            },
            options: {
                responsive: true,
                title: {
                    display: false
                },
                tooltips: {
                    enabled: true,
                    cornerRadius: 10,
                    xPadding: 20,
                    titleFontSize: 10,
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
                            drawBorder: false
                        },
                        ticks: {
                            display: false,
                            beginAtZero: true
                        }

                    }]
                }
            }
        }
    }
    new Chart(chart, config);

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
        }
    });
}

function drawMonthlyChart(data) {

    console.log('data', data['data'][2]);

    console.log(typeof (data['data'][2]));
    var dataTable = new google.visualization.DataTable();
    console.log('data', data['data']);

    dataTable.addColumn({ type: 'date', id: 'Date' });
    dataTable.addColumn({ type: 'number', id: 'Won/Loss' });
    dataTable.addRows([
        [new Date(2019, 1, 13), 10],
        [new Date(2019, 3, 13), 4],
        [new Date(2019, 3, 14), 5],
        [new Date(2019, 3, 15), 7],
        [new Date(2019, 3, 16), 6],
        [new Date(2019, 3, 17), 8],
        [new Date(2019, 9, 17), 5],
        [new Date(2019, 4, 17), 8],
        [new Date(2019, 7, 17), 9]
    ]);



    var chart = new google.visualization.Calendar(document.getElementById('calendar_basic'));


    var options = {
        title: " ",
        height: 600,
    };
    chart.draw(dataTable, options);
}


$(document).ready(function () {

    //Tab Change
    $('#chart-navigation a').on('shown.bs.tab', function (event) {
        var x = $(event.target).attr("href"); // active tab
        var y = $(event.relatedTarget); // previous tab
        console.log('new tab', x);
        if (x == '#daily-view') {
            loadDailyChart();
        }
        if (x == '#weekly-view') {
            loadWeeklyChart();
        }
        if (x == '#monthly-view') {
            loadMonthlyChart();
        }
    });

});


window.onload = function () {
    if (document.getElementById('daily-chart')) {
        loadDailyChart();
    }
}
