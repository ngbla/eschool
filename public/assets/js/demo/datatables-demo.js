// Call the dataTables jQuery plugin
$(document).ready(function() {

  $('#dataTable').DataTable();

  $('#dataTablenotes').DataTable( {

    buttons: [
        'csv', 'excel', 'pdf', 'print'
    ]
  } );


});
