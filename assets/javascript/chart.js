    var MONTHS = ['January', 'February', 'March', 'April', 'May', 'June', 'July', 'August', 'September', 'October', 'November', 'December'];
        var ctx = document.getElementById('main-chart').getContext('2d'); 
        gradient = ctx.createLinearGradient(0, 0, 0, 600);
        gradient.addColorStop(0, '#7077ff');
        gradient.addColorStop(0.5, '#e485fb');
        gradient.addColorStop(1, '#e484fb');
    var config = {
        type: 'line',
        data: {
            labels: ['January', 'February', 'March', 'April', 'May', 'June', 'July'],
            datasets: [{
                label: 'Projects',
                borderColor: "rgb(255, 99, 132)",
                backgroundColor: gradient,
                data: [25,20,55,15,25,40,2,10,75,10],
            }]
        },
        options: {
            responsive: true,
            title: {
                display: false
            },
            tooltips: {
                mode: 'index',
            },
            hover: {
                mode: 'index'
            },
            scales: {
                xAxes: [{
                    scaleLabel: {
                        display: false,
                        labelString: 'Month'
                    }
                }],
                yAxes: [{
                    stacked: false,
                    scaleLabel: {
                        display: false,
                        labelString: 'Value'
                    }
                }]
            }
        }
    };
    window.onload = function() {
        new Chart(ctx, config);
    };
