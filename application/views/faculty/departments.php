<div class="layout-content">
    <div class="container-fluid flex-grow-1 container-p-y">
        <h4 class="font-weight-bold py-3 mb-0">Departments</h4>
        <div class="text-muted small mt-0 mb-4 d-block breadcrumb">
            <ol class="breadcrumb">
                <li class="breadcrumb-item"><a href="#"><i class="feather icon-home"></i></a></li>
                <li class="breadcrumb-item"><a href="<?php echo base_url($url.'/dashboard'); ?>">Dashboard</a></li>
                <li class="breadcrumb-item active">Departments</li>
            </ol>
        </div>

        <?php if ($this->session->flashdata('message')) { ?>
            <div class="alert alert-dark-<?= $this->session->flashdata('message')[0] ?> alert-dismissible fade show" id="alert">
                <button type="button" class="close" data-dismiss="alert">×</button>
                <span><?= $this->session->flashdata('message')[1] ?></span>
            </div>
        <?php } ?>

        <!-- Actions Bar -->
        <div class="row mb-4">
            <div class="col-md-12">
                <div class="card">
                    <div class="card-body">
                        <div class="row">
                            <div class="col-md-6">
                                <h6>Department Management</h6>
                                <p class="mb-0">View and manage department information</p>
                            </div>
                            <div class="col-md-6 text-right">
                                <?php if (isset($can_manage) && $can_manage): ?>
                                    <a href="<?php echo base_url($url.'/departments/add'); ?>" class="btn btn-success">
                                        <i class="feather icon-plus"></i> Add Department
                                    </a>
                                <?php endif; ?>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Departments List -->
        <div class="row">
            <div class="col-md-12">
                <div class="card">
                    <div class="card-body">
                        <?php if (empty($departments)): ?>
                            <div class="text-center py-5">
                                <i class="feather icon-home" style="font-size: 4rem; color: #ccc;"></i>
                                <h4 class="mt-3">No Departments</h4>
                                <p class="text-muted">There are no departments to display.</p>
                                <?php if (isset($can_manage) && $can_manage): ?>
                                    <a href="<?php echo base_url($url.'/departments/add'); ?>" class="btn btn-primary">
                                        Add First Department
                                    </a>
                                <?php endif; ?>
                            </div>
                        <?php else: ?>
                            <div class="table-responsive">
                                <table class="table table-hover">
                                    <thead>
                                        <tr>
                                            <th>ID</th>
                                            <th>Department Name</th>
                                            <th>Status</th>
                                            <?php if (isset($can_manage) && $can_manage): ?>
                                                <th>Actions</th>
                                            <?php endif; ?>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        <?php foreach ($departments as $dept): ?>
                                            <tr>
                                                <td><?php echo $dept['id']; ?></td>
                                                <td><?php echo htmlspecialchars($dept['name']); ?></td>
                                                <td>
                                                    <span class="badge badge-<?php echo $dept['is_active'] ? 'success' : 'secondary'; ?>">
                                                        <?php echo $dept['is_active'] ? 'Active' : 'Inactive'; ?>
                                                    </span>
                                                </td>
                                                <?php if (isset($can_manage) && $can_manage): ?>
                                                    <td>
                                                        <div class="dropdown">
                                                            <button class="btn btn-sm btn-outline-secondary dropdown-toggle" type="button" data-toggle="dropdown">
                                                                <i class="feather icon-more-vertical"></i>
                                                            </button>
                                                            <div class="dropdown-menu">
                                                                <a class="dropdown-item" href="<?php echo base_url($url.'/departments/edit/'.$dept['id']); ?>">
                                                                    <i class="feather icon-edit"></i> Edit
                                                                </a>
                                                                <a class="dropdown-item text-danger" href="#" onclick="confirmDelete(<?php echo $dept['id']; ?>, '<?php echo htmlspecialchars($dept['name']); ?>')">
                                                                    <i class="feather icon-trash"></i> Delete
                                                                </a>
                                                            </div>
                                                        </div>
                                                    </td>
                                                <?php endif; ?>
                                            </tr>
                                        <?php endforeach; ?>
                                    </tbody>
                                </table>
                            </div>
                        <?php endif; ?>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<script>
function confirmDelete(id, name) {
    if (confirm('Are you sure you want to delete the department "' + name + '"? This action cannot be undone.')) {
        window.location.href = '<?php echo base_url($url.'/departments/delete/'); ?>' + id;
    }
}
</script>