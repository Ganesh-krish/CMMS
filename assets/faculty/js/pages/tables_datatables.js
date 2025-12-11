$(function() {
  $('.datatables-demo').dataTable({
    dom: 'Bfrtip',
    buttons: [
        'copy', 'csv', 'excel', 'pdf', 'print'
    ]
  });
});
