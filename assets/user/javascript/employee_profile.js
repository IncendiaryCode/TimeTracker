 $(document).ready(function()
{
 $.ajax({
     type: 'GET',
     url: timeTrackerBaseURL + 'index.php/user/load_my_profile',
     
     success: function(values) {
         var data = JSON.parse(values);
console.log(data);
         var tableRow = $("<table><tr><th>Email</th><td>"+ data['email']+"</td></tr>");
         tableRow.append("<tr><th>Phone number</th><td>"+data['phone']+"</td></tr>");
         tableRow.append("<tr><th scope='row'>Type</th><td>"+ data['type']+"</td></tr></table>");

         $('#table-body').append(tableRow);
     }
 });
 });
