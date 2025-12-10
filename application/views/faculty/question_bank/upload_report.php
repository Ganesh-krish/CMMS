<main class="main-content position-relative max-height-vh-100 h-100 border-radius-lg">
    <!-- Navbar -->
    <nav class="navbar navbar-main navbar-expand-lg px-0 mx-4 shadow-none border-radius-xl" id="navbarBlur" navbar-scroll="true">
        <div class="container-fluid py-1 px-3">
            <nav aria-label="breadcrumb">
                <ol class="breadcrumb bg-transparent mb-0 pb-0 pt-1 px-0 me-sm-6 me-5">
                    <li class="breadcrumb-item text-sm"><a class="opacity-5 text-dark" href="javascript:;">Pages</a></li>
                    <li class="breadcrumb-item text-sm text-dark active" aria-current="page">Bulk Upload Report</li>
                </ol>
                <h6 class="font-weight-bolder mb-0">Bulk Upload Report</h6>
            </nav>
        </div>
    </nav>

    <div class="container-fluid py-4">
        <div class="row">
            <div class="col-12">
                <div class="card">
                    <div class="card-header">
                        <h5>Upload Summary</h5>
                    </div>
                    <div class="card-body">
                        <!-- Debug output -->
                        <?php if (ENVIRONMENT === 'development'): ?>
                        <div class="alert alert-info">
                            <h6>Debug Information:</h6>
                            <p>Total Rows: <?= $total_rows ?></p>
                            <p>Successful Inserts: <?= $successful_inserts ?></p>
                            <p>Failed Rows Count: <?= count($failed_rows) ?></p>
                            <p>Duplicate Rows Count: <?= count($duplicate_rows) ?></p>
                        </div>
                        <?php endif; ?>

                        <div class="row mb-4">
                            <div class="col-md-3">
                                <div class="card bg-gradient-success">
                                    <div class="card-body">
                                        <h6 class="text-black">Total Questions</h6>
                                        <h3 class="text-black"><?= $total_rows ?></h3>
                                    </div>
                                </div>
                            </div>
                            <div class="col-md-3">
                                <div class="card bg-gradient-info">
                                    <div class="card-body">
                                        <h6 class="text-black">Successfully Added</h6>
                                        <h3 class="text-black"><?= $successful_inserts ?></h3>
                                    </div>
                                </div>
                            </div>
                            <div class="col-md-3">
                                <div class="card bg-gradient-danger">
                                    <div class="card-body">
                                        <h6 class="text-black">Failed Uploads</h6>
                                        <h3 class="text-black"><?= count($failed_rows) ?></h3>
                                    </div>
                                </div>
                            </div>
                            <div class="col-md-3">
                                <div class="card bg-gradient-warning">
                                    <div class="card-body">
                                        <h6 class="text-black">Duplicate Questions</h6>
                                        <h3 class="text-black"><?= count($duplicate_rows) ?></h3>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <?php if (!empty($failed_rows)): ?>
                        <div class="mb-4">
                            <h5 class="text-danger">Failed Uploads</h5>
                            <div class="table-responsive">
                                <table class="table align-items-center mb-0" id="error-table">
                                    <thead>
                                        <tr>
                                            <th class="text-uppercase text-secondary text-xxs font-weight-bolder opacity-7">Row Number</th>
                                            <th class="text-uppercase text-secondary text-xxs font-weight-bolder opacity-7 ps-2">Question</th>
                                            <th class="text-uppercase text-secondary text-xxs font-weight-bolder opacity-7 ps-2">Error Message</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        <?php foreach ($failed_rows as $row): ?>
                                        <tr>
                                            <td>
                                                <span class="text-secondary text-xs font-weight-bold"><?= $row['row'] ?></span>
                                            </td>
                                            <td>
                                                <span class="text-secondary text-xs font-weight-bold"><?= $row['question'] ?></span>
                                            </td>
                                            <td>
                                                <span class="text-danger text-xs font-weight-bold"><?= $row['error'] ?></span>
                                            </td>
                                        </tr>
                                        <?php endforeach; ?>
                                    </tbody>
                                </table>
                            </div>
                        </div>
                        <?php endif; ?>

                        <?php if (!empty($duplicate_rows)): ?>
                        <div class="mb-4">
                            <h5 class="text-warning">Duplicate Questions</h5>
                            <div class="table-responsive">
                                <table class="table align-items-center mb-0" id="duplicate-table">
                                    <thead>
                                        <tr>
                                            <th class="text-uppercase text-secondary text-xxs font-weight-bolder opacity-7">Row Number</th>
                                            <th class="text-uppercase text-secondary text-xxs font-weight-bolder opacity-7 ps-2">Question</th>
                                            <th class="text-uppercase text-secondary text-xxs font-weight-bolder opacity-7 ps-2">Status</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        <?php foreach ($duplicate_rows as $row): ?>
                                        <tr>
                                            <td>
                                                <span class="text-secondary text-xs font-weight-bold"><?= $row['row'] ?></span>
                                            </td>
                                            <td>
                                                <span class="text-secondary text-xs font-weight-bold"><?= $row['question'] ?></span>
                                            </td>
                                            <td>
                                                <span class="text-warning text-xs font-weight-bold"><?= $row['error'] ?></span>
                                            </td>
                                        </tr>
                                        <?php endforeach; ?>
                                    </tbody>
                                </table>
                            </div>
                        </div>
                        <?php endif; ?>

                        <div class="mt-4">
                            <a href="<?= base_url($url.'/question') ?>" class="btn btn-primary">Back to Questions</a>
                            <?php if (!empty($failed_rows) || !empty($duplicate_rows)): ?>
                            <button class="btn btn-info" onclick="window.print()">Print Report</button>
                            <?php endif; ?>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</main>

<style>
@media print {
    .navbar, .btn, .no-print {
        display: none !important;
    }
    .card {
        border: none !important;
    }
    .card-body {
        padding: 0 !important;
    }
}
</style>

<script>
$(document).ready(function() {


    

    $('#error-table, #duplicate-table').DataTable({
        "pageLength": 25,
        "order": [[0, "asc"]],
        "language": {
            "paginate": {
                next: "Next →",
                previous: "← Previous"
            }
        }
    });
});
</script> 