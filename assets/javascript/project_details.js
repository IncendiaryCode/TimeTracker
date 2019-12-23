

$(document).ready(function() {
if(document.getElementById('user-id') != null)
{
   $('#project-datatable').DataTable({
        "processing": true,
        "serverSide": true,
        "ajax": {
            "url": timeTrackerBaseURL + 'index.php/admin/project_table',
            "data": {'user_id': project_id }
        },
        "order": [[ 0, "asc" ]],
        "columnDefs": [{
            "targets": 0,
            "render": function ( data, type, row, meta ) {
                return row.project_name;
            }
        },{
            "targets": 1,
            "render": function ( data, type, row, meta ) {
                return row.descripiton;
            },
        },{
            "targets": 2,
            "render": function ( data, type, row, meta ) {
                return row.start_time;
            }
        },{
            "targets": 3,
            "render": function ( data, type, row, meta ) {
                return row.end_time;
            }
        },{
            "targets": 4,
            "render": function ( data, type, row, meta ) {
                return row.time_spent;
            }
        }]
    }).on( 'init.dt', function () {
    });
}
});
