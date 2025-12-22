<div class="layout-content">
    <div class="container-fluid flex-grow-1 container-p-y">
        <h4 class="font-weight-bold py-3 mb-0">Issue Instrument</h4>
        <div class="text-muted small mt-0 mb-4 d-block breadcrumb">
            <ol class="breadcrumb">
                <li class="breadcrumb-item"><a href="<?php echo base_url($url.'/inventory'); ?>"><i class="feather icon-home"></i> Inventory</a></li>
                <li class="breadcrumb-item">Issue Instrument</li>
            </ol>
        </div>

        <?php if ($this->session->flashdata('message')) { ?>
            <div class="alert alert-dark-<?= $this->session->flashdata('message')[0] ?> alert-dismissible fade show" id="alert">
                <button type="button" class="close" data-dismiss="alert">×</button>
                <span><?= $this->session->flashdata('message')[1] ?></span>
            </div>
        <?php } ?>

        <!-- Instrument Details -->
        <div class="row mb-4">
            <div class="col-md-8">
                <div class="card">
                    <div class="card-header">
                        <h6 class="mb-0">Instrument Details</h6>
                    </div>
                    <div class="card-body">
                        <div class="row">
                            <div class="col-md-3">
                                <?php if (!empty($instrument['instrument_image'])): ?>
                                    <img src="<?php echo base_url($instrument['instrument_image']); ?>" class="img-fluid rounded" alt="Instrument Image">
                                <?php else: ?>
                                    <div class="text-center p-3 border rounded">
                                        <i class="feather icon-image" style="font-size: 3rem; color: #ccc;"></i>
                                        <p class="mb-0 mt-2 text-muted">No Image</p>
                                    </div>
                                <?php endif; ?>
                            </div>
                            <div class="col-md-9">
                                <h5 class="mb-3"><?php echo htmlspecialchars($instrument['name']); ?></h5>
                                <div class="row">
                                    <div class="col-md-6">
                                        <p class="mb-1"><strong>Serial No:</strong> <?php echo htmlspecialchars($instrument['serial_no']); ?></p>
                                        <p class="mb-1"><strong>Model:</strong> <?php echo htmlspecialchars($instrument['model'] ?? 'N/A'); ?></p>
                                        <p class="mb-1"><strong>Brand:</strong> <?php echo htmlspecialchars($instrument['brand'] ?? 'N/A'); ?></p>
                                    </div>
                                    <div class="col-md-6">
                                        <p class="mb-1"><strong>Category:</strong> <?php echo htmlspecialchars($categories[$instrument['category']] ?? $instrument['category']); ?></p>
                                        <p class="mb-1"><strong>Condition:</strong>
                                            <span class="badge badge-<?php
                                                echo $instrument['condition'] == INSTRUMENT_CONDITION_EXCELLENT ? 'success' :
                                                     ($instrument['condition'] == INSTRUMENT_CONDITION_GOOD ? 'primary' :
                                                     ($instrument['condition'] == INSTRUMENT_CONDITION_FAIR ? 'warning' :
                                                     ($instrument['condition'] == INSTRUMENT_CONDITION_POOR ? 'secondary' : 'danger')));
                                            ?>">
                                                <?php echo ucfirst($instrument['condition']); ?>
                                            </span>
                                        </p>
                                        <p class="mb-1"><strong>Price:</strong> <?php echo $instrument['instrument_price'] ? '₹' . number_format($instrument['instrument_price'], 2) : 'N/A'; ?></p>
                                    </div>
                                </div>
                                <?php if (!empty($instrument['description'])): ?>
                                    <div class="mt-3">
                                        <strong>Description:</strong><br>
                                        <p class="text-muted mb-0"><?php echo htmlspecialchars($instrument['description']); ?></p>
                                    </div>
                                <?php endif; ?>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            <div class="col-md-4">
                <div class="card">
                    <div class="card-header">
                        <h6 class="mb-0">Issue Status</h6>
                    </div>
                    <div class="card-body">
                        <div class="text-center">
                            <div class="mb-3">
                                <i class="feather icon-send text-warning" style="font-size: 3rem;"></i>
                            </div>
                            <h6 class="text-success mb-0">Available for Issue</h6>
                            <p class="text-muted small mt-1">This instrument can be issued to students or staff</p>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Issue Form -->
        <div class="card">
            <div class="card-header">
                <h6 class="mb-0">Issue Instrument Form</h6>
            </div>
            <div class="card-body">
                <form action="<?php echo base_url($url . '/inventory/issue'); ?>" method="POST">
                    <input type="hidden" name="instrument_id" value="<?php echo $instrument['id']; ?>">

                    <div class="row">
                        <div class="col-md-6 mb-3">
                            <label for="issued_to_type" class="form-label">Issue To Type *</label>
                            <select class="form-control select2" id="issued_to_type" name="issued_to_type" required>
                                <option value="">Select Type</option>
                                <option value="student">Student</option>
                                <option value="staff">Staff</option>
                            </select>
                        </div>
                        <div class="col-md-6 mb-3">
                            <label for="issued_to_id" class="form-label">Issue To *</label>
                            <select class="form-control select2" id="issued_to_id" name="issued_to_id" required disabled>
                                <option value="">Select Student/Staff</option>
                            </select>
                        </div>
                    </div>

                    <div class="row">
                        <div class="col-md-6 mb-3">
                            <label for="expected_return_date" class="form-label">Expected Return Date *</label>
                            <input type="date" class="form-control" id="expected_return_date" name="expected_return_date" required>
                        </div>
                        <div class="col-md-6 mb-3">
                            <label for="issue_date" class="form-label">Issue Date</label>
                            <input type="date" class="form-control" id="issue_date" name="issue_date" value="<?php echo date('Y-m-d'); ?>">
                        </div>
                    </div>

                    <div class="mb-3">
                        <label for="purpose" class="form-label">Purpose/Reason</label>
                        <textarea class="form-control" id="purpose" name="purpose" rows="3" placeholder="e.g., Practice session, Performance, etc."></textarea>
                    </div>

                    <div class="form-group">
                        <button type="submit" class="btn btn-primary">
                            <i class="feather icon-send"></i> Issue Instrument
                        </button>
                        <a href="<?php echo base_url($url.'/inventory'); ?>" class="btn btn-secondary ml-2">
                            <i class="feather icon-arrow-left"></i> Back to Inventory
                        </a>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>

<script>
$(document).ready(function() {
    // Initialize Select2 for category dropdowns (if any)
    $('#issued_to_type, #issued_to_id').select2({
        placeholder: function() {
            return $(this).data('placeholder') || "Select an option";
        },
        allowClear: true
    });

    // Set minimum date for expected return date
    const today = new Date().toISOString().split('T')[0];
    if (document.getElementById('expected_return_date')) {
        document.getElementById('expected_return_date').setAttribute('min', today);
    }

    // Bind change event for issue type selection
    $('#issued_to_type').on('change', function() {
        toggleIssueFields();
    });
});

function toggleIssueFields() {
    const type = document.getElementById('issued_to_type').value;
    const idField = document.getElementById('issued_to_id');
    const idLabel = document.querySelector('label[for="issued_to_id"]');

    if (type === 'student') {
        idLabel.textContent = 'Issue To Student *';
        loadStudents();
    } else if (type === 'staff') {
        idLabel.textContent = 'Issue To Staff *';
        loadStaff();
    } else {
        idField.innerHTML = '<option value="">Select Student/Staff</option>';
        idField.disabled = true;
        // Reset select2
        if ($('#issued_to_id').hasClass('select2-hidden-accessible')) {
            $('#issued_to_id').select2('destroy');
            $('#issued_to_id').select2({
                placeholder: "Select Student/Staff",
                allowClear: true
            });
        }
    }
}

function loadStudents() {
    const selectField = document.getElementById('issued_to_id');
    selectField.disabled = true;
    selectField.innerHTML = '<option value="">Loading...</option>';

    fetch('<?php echo base_url($url.'/api/get_students'); ?>')
        .then(response => response.json())
        .then(data => {
            if (data.status === 'success') {
                let options = '<option value="">Select Student</option>';
                data.data.forEach(function(student) {
                    var studentId = student.id || '';
                    var studentRollNo = student.roll_no || '';
                    var studentName = student.name || '';
                    // Escape quotes in the values
                    studentId = studentId.toString().replace(/"/g, '&quot;');
                    studentRollNo = studentRollNo.toString().replace(/"/g, '&quot;');
                    studentName = studentName.toString().replace(/"/g, '&quot;');
                    options += '<option value="' + studentId + '">' + studentRollNo + ' - ' + studentName + '</option>';
                });
                selectField.innerHTML = options;
                selectField.disabled = false;

                // Reinitialize select2
                if ($('#issued_to_id').hasClass('select2-hidden-accessible')) {
                    $('#issued_to_id').select2('destroy');
                }
                $('#issued_to_id').select2({
                    placeholder: "Select Student",
                    allowClear: true
                });
            } else {
                selectField.innerHTML = '<option value="">Error loading students</option>';
            }
        })
        .catch(error => {
            console.error('Error loading students:', error);
            selectField.innerHTML = '<option value="">Error loading students</option>';
        });
}

function loadStaff() {
    const selectField = document.getElementById('issued_to_id');
    selectField.disabled = true;
    selectField.innerHTML = '<option value="">Loading...</option>';

    fetch('<?php echo base_url($url.'/api/get_staff'); ?>')
        .then(response => response.json())
        .then(data => {
            if (data.status === 'success') {
                let options = '<option value="">Select Staff</option>';
                data.data.forEach(function(staff) {
                    var staffId = staff.id || '';
                    var staffName = staff.name || '';
                    var staffDesignation = staff.designation || 'Staff';
                    // Escape quotes in the values
                    staffId = staffId.toString().replace(/"/g, '&quot;');
                    staffName = staffName.toString().replace(/"/g, '&quot;');
                    staffDesignation = staffDesignation.toString().replace(/"/g, '&quot;');
                    options += '<option value="' + staffId + '">' + staffName + ' (' + staffDesignation + ')' + '</option>';
                });
                selectField.innerHTML = options;
                selectField.disabled = false;

                // Reinitialize select2
                if ($('#issued_to_id').hasClass('select2-hidden-accessible')) {
                    $('#issued_to_id').select2('destroy');
                }
                $('#issued_to_id').select2({
                    placeholder: "Select Staff",
                    allowClear: true
                });
            } else {
                selectField.innerHTML = '<option value="">Error loading staff</option>';
            }
        })
        .catch(error => {
            console.error('Error loading staff:', error);
            selectField.innerHTML = '<option value="">Error loading staff</option>';
        });
}
</script>