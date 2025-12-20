(function($){
  // Global variables for export modal
  var pendingExport = null;

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
            text: 'Copy',
            action: function(e, dt, button, config) {
              showExportModal('copy', dt, 'musiccollege-data-' + new Date().toISOString().split('T')[0]);
            }
          },
          {
            text: 'CSV',
            action: function(e, dt, button, config) {
              showExportModal('csv', dt, 'musiccollege-data-' + new Date().toISOString().split('T')[0]);
            }
          },
          {
            text: 'Excel',
            action: function(e, dt, button, config) {
              showExportModal('excel', dt, 'musiccollege-data-' + new Date().toISOString().split('T')[0]);
            }
          },
          {
            text: 'PDF',
            action: function(e, dt, button, config) {
              showExportModal('pdf', dt, 'musiccollege-data-' + new Date().toISOString().split('T')[0]);
            }
          },
          {
            text: 'Print',
            action: function(e, dt, button, config) {
              showExportModal('print', dt, 'Music College Data');
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
            text: 'Copy',
            action: function(e, dt, button, config) {
              showExportModal('copy', dt, 'musiccollege-data-' + new Date().toISOString().split('T')[0]);
            }
          },
          {
            text: 'CSV',
            action: function(e, dt, button, config) {
              showExportModal('csv', dt, 'musiccollege-data-' + new Date().toISOString().split('T')[0]);
            }
          },
          {
            text: 'Excel',
            action: function(e, dt, button, config) {
              showExportModal('excel', dt, 'musiccollege-data-' + new Date().toISOString().split('T')[0]);
            }
          },
          {
            text: 'PDF',
            action: function(e, dt, button, config) {
              showExportModal('pdf', dt, 'musiccollege-data-' + new Date().toISOString().split('T')[0]);
            }
          },
          {
            text: 'Print',
            action: function(e, dt, button, config) {
              showExportModal('print', dt, 'Music College Data');
            }
          }
        ],
        paging: true,
        searching: true,
        ordering: true,
        responsive: true
      });
    });

    // Handle export modal confirmation
    $('#confirmExportBtn').on('click', function() {
      if (!pendingExport) return;

      var value = $('#exportFilename').val().trim();
      if (pendingExport.type === 'print') {
        value = $('#exportTitle').val().trim();
      }

      // Use default if empty
      if (!value) {
        value = pendingExport.defaultValue;
      }

      // Close modal
      $('#exportFilenameModal').modal('hide');

      // Perform export by creating a temporary link/download
      try {
        if (pendingExport.type === 'copy') {
          // For copy, get data and use clipboard API
          var data = pendingExport.dt.buttons.exportData({
            columns: ':not(:last-child)'
          });

          // Convert to CSV format
          var csvContent = '';
          // Add headers
          csvContent += data.header.join(',') + '\n';
          // Add data rows
          data.body.forEach(function(row) {
            csvContent += row.join(',') + '\n';
          });

          // Copy to clipboard
          navigator.clipboard.writeText(csvContent).then(function() {
            alert('Data copied to clipboard successfully!');
          }).catch(function(err) {
            console.error('Failed to copy to clipboard:', err);
            alert('Failed to copy to clipboard. Please try again.');
          });

        } else {
          // For file downloads, create a blob and download link
          var data = pendingExport.dt.buttons.exportData({
            columns: ':not(:last-child)'
          });

          var content = '';
          var mimeType = '';
          var extension = '';

          if (pendingExport.type === 'csv') {
            mimeType = 'text/csv';
            extension = 'csv';
            // Add headers
            content += data.header.join(',') + '\n';
            // Add data rows
            data.body.forEach(function(row) {
              content += row.map(function(cell) {
                // Escape quotes and wrap in quotes if contains comma
                if (cell.indexOf(',') !== -1 || cell.indexOf('"') !== -1) {
                  return '"' + cell.replace(/"/g, '""') + '"';
                }
                return cell;
              }).join(',') + '\n';
            });

          } else if (pendingExport.type === 'excel') {
            mimeType = 'application/vnd.ms-excel';
            extension = 'xls';
            // Simple Excel format
            content += data.header.join('\t') + '\n';
            data.body.forEach(function(row) {
              content += row.join('\t') + '\n';
            });

          } else if (pendingExport.type === 'pdf') {
            // Try to use DataTables built-in PDF export first
            try {
              pendingExport.dt.buttons.exportFile('pdf', data, value);
              return; // Exit early if successful
            } catch (dtPdfError) {
              console.warn('DataTables PDF export failed, trying pdfMake directly:', dtPdfError);
              try {
                // Fallback: use pdfMake directly
                var tableBody = [data.header];

                data.body.forEach(function(row) {
                  tableBody.push(row.map(function(cell) {
                    return String(cell);
                  }));
                });

                var docDefinition = {
                  pageSize: 'A4',
                  pageOrientation: 'landscape',
                  content: [
                    { text: value, style: 'title', margin: [0, 0, 0, 10] },
                    {
                      table: {
                        headerRows: 1,
                        widths: data.header.map(function() { return '*'; }),
                        body: tableBody
                      }
                    }
                  ],
                  styles: {
                    title: {
                      fontSize: 14,
                      bold: true,
                      alignment: 'center'
                    }
                  }
                };

                if (typeof pdfMake !== 'undefined' && pdfMake.createPdf) {
                  pdfMake.createPdf(docDefinition).download(value + '.pdf');
                  return; // Exit early for successful PDF
                } else {
                  throw new Error('pdfMake not available');
                }
              } catch (pdfError) {
                console.error('PDF export failed completely:', pdfError);
                // Final fallback: create a text file with .pdf extension
                mimeType = 'text/plain';
                extension = 'pdf';
                content = value + '\n\nData Export\n\n';
                content += 'Note: PDF export not available, showing data as text\n\n';
                content += data.header.join('\t') + '\n';
                content += '='.repeat(50) + '\n';
                data.body.forEach(function(row) {
                  content += row.join('\t') + '\n';
                });
              }
            }

          } else if (pendingExport.type === 'print') {
            // For print, open a new window with formatted content
            var printWindow = window.open('', '_blank');
            var printContent = '<html><head><title>' + value + '</title>';
            printContent += '<style>body{font-family:Arial,sans-serif;} table{border-collapse:collapse;width:100%;} th,td{border:1px solid #ddd;padding:8px;text-align:left;} th{background-color:#f2f2f2;}</style>';
            printContent += '</head><body>';
            printContent += '<h1>' + value + '</h1>';
            printContent += '<table>';
            printContent += '<thead><tr>';
            data.header.forEach(function(header) {
              printContent += '<th>' + header + '</th>';
            });
            printContent += '</tr></thead><tbody>';
            data.body.forEach(function(row) {
              printContent += '<tr>';
              row.forEach(function(cell) {
                printContent += '<td>' + cell + '</td>';
              });
              printContent += '</tr>';
            });
            printContent += '</tbody></table></body></html>';

            printWindow.document.write(printContent);
            printWindow.document.close();
            printWindow.print();
            return; // Exit early for print
          }

          // Create download link for file exports
          var blob = new Blob([content], { type: mimeType });
          var url = window.URL.createObjectURL(blob);
          var a = document.createElement('a');
          a.style.display = 'none';
          a.href = url;
          a.download = value + '.' + extension;
          document.body.appendChild(a);
          a.click();
          window.URL.revokeObjectURL(url);
          document.body.removeChild(a);
        }

      } catch (error) {
        console.error('Export error:', error);
        alert('Export functionality encountered an error. Please try again.');
      }

      // Reset modal and pending export
      $('#exportFilename').val('');
      $('#exportTitle').val('');
      pendingExport = null;
    });

    // Reset modal when closed
    $('#exportFilenameModal').on('hidden.bs.modal', function() {
      $('#exportFilename').val('');
      $('#exportTitle').val('');
      pendingExport = null;
    });
  });

  // Helper function to show export modal
  function showExportModal(type, dt, defaultValue) {
    pendingExport = {
      type: type,
      dt: dt,
      defaultValue: defaultValue
    };

    // Update modal content
    $('#exportFilenameModalLabel').text('Export ' + type.toUpperCase() + ' Data');
    $('#exportFilename').val(defaultValue);

    if (type === 'print') {
      $('#exportTitleLabel').removeClass('d-none');
      $('#exportTitle').removeClass('d-none').val(defaultValue || 'Data Export');
      $('#exportFilename').closest('.form-group').addClass('d-none');
    } else {
      $('#exportTitleLabel').addClass('d-none');
      $('#exportTitle').addClass('d-none');
      $('#exportFilename').closest('.form-group').removeClass('d-none');
    }

    // Show modal
    $('#exportFilenameModal').modal('show');
  }

  // Make showExportModal globally available
  window.showExportModal = showExportModal;
})(jQuery);