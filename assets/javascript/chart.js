    var MONTHS = ['January', 'February', 'March', 'April', 'May', 'June', 'July', 'August', 'September', 'October', 'November', 'December'];
    var config = {
        type: 'line',
        data: {

            labels: ['January', 'February', 'March', 'April', 'May', 'June', 'July'],
            datasets: [{

                label: 'Projects',
                borderColor: "rgb(255, 99, 132)",
                backgroundColor: "#e7487b",
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
        var ctx = document.getElementById('canvas').getContext('2d'); 
        window.myLine = new Chart(ctx, config);

    };

 