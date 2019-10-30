    $(document).ready(function() {

        var date = document.getElementById('daily-chart').value;

        if (date == "" || date == " " || date == null) {
            var today = new Date();
            document.getElementById("daily-chart").value = today.getFullYear() + '-' + ('0' + (today.getMonth() + 1)).slice(-2) + '-' + ('0' + today.getDate()).slice(-2);


            date = today.getFullYear() + '-' + ('0' + (today.getMonth() + 1)).slice(-2) + '-' + ('0' + today.getDate()).slice(-2);
            document.getElementById("daily-chart").setAttribute("max", date);

            date = document.getElementById('daily-chart').value;
            retrieveData(date);
        }
    });

    function dailyChart() {
        var date = document.getElementById('daily-chart').value;
        if (date == "" || date == " " || date == null) {
            var today = new Date();
            document.getElementById("daily-chart").value = today.getFullYear() + '-' + ('0' + (today.getMonth() + 1)).slice(-2) + '-' + ('0' + today.getDate()).slice(-2);
        }

        date = document.getElementById('daily-chart').value;
        document.getElementById("daily-chart").setAttribute("max", date);
        retrieveData(date)
    }

    function retrieveData(date) {
        $.ajax({
            type: "GET",
            url: timeTrackerBaseURL + 'php/activity.php',
            data: { 'user': "<?= $_SESSION['user'] ?>", 'date': date },
            dataType: 'json',
            success: function(res) {

            }
        });
    }

    var chart = document.getElementById('daily').getContext('2d');
    var MONTHS = ['January', 'February', 'March', 'April', 'May', 'June', 'July', 'August', 'September', 'October', 'November', 'December'];

    gradient = chart.createLinearGradient(0, 0, 0, 600);

    gradient.addColorStop(0, '#7078ff');
    gradient.addColorStop(0.5, '#e58dfb');
    gradient.addColorStop(1, '#ffffff');

    var config = {
        type: 'bar',
        data: {
            labels: ['12AM', '3AM', '6AM', '9AM', '12PM', '3PM', '6PM', '9PM', '12AM'],
            datasets: [{

                label: 'Time interval',
                borderColor: "#7078ff",
                backgroundColor: gradient,
                hoverbackground: gradient,
                data: [0.1, 0.1]
            }]
        },
        options: {
            responsive: true,
            title: {
                display: false
            },
            tooltips: {
                enabled: false,
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
                        display: false,
                        suggestedMin: 0,
                        suggestedMax: 1,
                        beginAtZero: true,
                        stacked: true
                    },
                    scaleLabel: {
                        display: false,
                        labelString: 'Value',
                    }
                }]
            }
        }
    };
    window.onload = function() {
        window.myLine = new Chart(chart, config);
    };