 $.ajax({
     type: 'GET',
     url: timeTrackerBaseURL + 'php/activity.php',
     data: { 'type': 'profile' },
     success: function(values) {
         //var data = JSON.parse(values);
     }
 });