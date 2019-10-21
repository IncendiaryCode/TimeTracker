 $.ajax({
     type: 'GET',
     url: timeTrackerBaseURL + 'php/my_profile.php',
     data: { 'type': 'profile' },
     success: function(values) {
         var data = JSON.parse(values);
         console.log(data.phone);
         $('#emp_type').append(data.type);
         $('#phone_no').append(data.phone);
     }
 });