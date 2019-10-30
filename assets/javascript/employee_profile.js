 $.ajax({
     type: 'GET',
     url: timeTrackerBaseURL + 'php/my_profile.php',
     data: { 'type': 'profile' },
     success: function(values) {
         var data = JSON.parse(values);

         var tableRow = $("<table><tr><th>Email</th><td>"+ data['email']+"</td></tr>");
         tableRow.append("<tr><th>Phone number</th><td>"+data['phone']+"</td></tr>");
         tableRow.append("<tr><th scope='row'>Type</th><td>"+ data['type']+"</td></tr></table>");

         $('#table-body').append(tableRow);



     }
 });

