(function($){
  $(document).ready(function(){
    // Initialize tables with 'datatable' class (new system)
    $('.datatable').each(function(){
      var $tbl = $(this);
      // Skip if already initialized
      if ($.fn.DataTable.isDataTable($tbl)) {
        return;
      }
      var serverSide = $tbl.data('server-side') === true || $tbl.data('server-side') === 'true';
      var ajaxSrc = $tbl.data('ajax');
      var options = {
        dom: 'Bfrtip',
        buttons: [
          {
            extend: 'copy',
            filename: 'musiccollege-data',
            exportOptions: {
              columns: ':not(:last-child)'
            }
          },
          {
            extend: 'csv',
            filename: 'musiccollege-data',
            exportOptions: {
              columns: ':not(:last-child)'
            }
          },
          {
            extend: 'excel',
            filename: 'musiccollege-data',
            exportOptions: {
              columns: ':not(:last-child)'
            }
          },
          {
            extend: 'pdf',
            filename: 'musiccollege-data',
            exportOptions: {
              columns: ':not(:last-child)'
            }
          },
          {
            extend: 'print',
            title: 'Music College Data',
            exportOptions: {
              columns: ':not(:last-child)'
            }
          }
        ],
        paging: true,
        searching: true,
        ordering: true,
        responsive: true
      };
      if (serverSide && ajaxSrc) {
        options.serverSide = true;
        options.ajax = { url: ajaxSrc, type: 'GET' };
      }
      $tbl.DataTable(options);
    });

    // Also initialize tables with 'datatables-demo' class if not already done (legacy support)
    $('.datatables-demo').each(function(){
      var $tbl = $(this);
      // Skip if already initialized
      if ($.fn.DataTable.isDataTable($tbl)) {
        return;
      }
      $tbl.DataTable({
        dom: 'Bfrtip',
        buttons: [
          {
            extend: 'copy',
            filename: 'musiccollege-data',
            exportOptions: {
              columns: ':not(:last-child)'
            }
          },
          {
            extend: 'csv',
            filename: 'musiccollege-data',
            exportOptions: {
              columns: ':not(:last-child)'
            }
          },
          {
            extend: 'excel',
            filename: 'musiccollege-data',
            exportOptions: {
              columns: ':not(:last-child)'
            }
          },
          {
            extend: 'pdf',
            filename: 'musiccollege-data',
            exportOptions: {
              columns: ':not(:last-child)'
            }
          },
          {
            extend: 'print',
            title: 'Music College Data',
            exportOptions: {
              columns: ':not(:last-child)'
            }
          }
        ],
        paging: true,
        searching: true,
        ordering: true,
        responsive: true
      });
    });
  });
})(jQuery);