      google.charts.load("current", { packages: ["calendar"] });
      google.charts.setOnLoadCallback(drawChart);

      function drawChart() {
          var dataTable = new google.visualization.DataTable();
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
              height: 300,
          };

          chart.draw(dataTable, options);
      }