<div class="layout-content">
    <div class="container-fluid flex-grow-1 container-p-y">
        <h4 class="font-weight-bold py-3 mb-0">Assistant Administrator Management</h4>
        <div class="text-muted small mt-0 mb-4 d-block breadcrumb">
            <ol class="breadcrumb">
                <li class="breadcrumb-item"><a href="<?php echo base_url($url.'/dashboard'); ?>"><i class="feather icon-home"></i></a></li>
                <li class="breadcrumb-item"><a href="<?php echo base_url($url.'/management/vice_principal'); ?>">Management</a></li>
                <li class="breadcrumb-item active">Assistant Administrator</li>
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
                                <h6>Assistant Administrator Management</h6>
                                <p class="mb-0">Manage assistant administrators (Vice-Principals)</p>
                            </div>
                            <div class="col-md-6 text-right">
                                <a href="<?php echo base_url($url.'/management/vice_principal/add'); ?>" class="btn btn-primary">
                                    <i class="feather icon-plus"></i> Add Asst Administrator
                                </a>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Vice Principals List -->
        <div class="row">
            <div class="col-md-12">
                <div class="card">
                    <div class="card-body">
                        <?php if (empty($vice_principals)): ?>
                            <div class="text-center py-5">
                                <i class="feather icon-user-check" style="font-size: 4rem; color: #ccc;"></i>
                                <h4 class="mt-3">No Assistant Administrators</h4>
                                <p class="text-muted">There are no assistant administrators to display.</p>
                                <a href="<?php echo base_url($url.'/management/vice_principal/add'); ?>" class="btn btn-primary">
                                    Add First Asst Administrator
                                </a>
                            </div>
                        <?php else: ?>
                            <div class="table-responsive">
                                <table class="table table-hover">
                                    <thead>
                                        <tr>
                                            <th>ID</th>
                                            <th>Name</th>
                                            <th>Email</th>
                                            <th>Phone Number</th>
                                            <th>Department</th>
                                            <th>Status</th>
                                            <th>Created At</th>
                                            <th>Actions</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        <?php foreach ($vice_principals as $vp): ?>
                                            <tr>
                                                <td><?php echo $vp['id']; ?></td>
                                                <td><?php echo htmlspecialchars($vp['name']); ?></td>
                                                <td><?php echo htmlspecialchars($vp['email']); ?></td>
                                                <td><?php echo htmlspecialchars($vp['phone_number']); ?></td>
                                                <td>
                                                    <?php
                                                    if (isset($vp['department_id']) && $vp['department_id']) {
                                                        $dept = $this->db_model->get_row(TABLE_DEPARTMENT, ["id" => $vp['department_id']]);
                                                        echo $dept ? htmlspecialchars($dept['name']) : 'N/A';
                                                    } else {
                                                        echo 'N/A';
                                                    }
                                                    ?>
                                                </td>
                                                <td>
                                                    <span class="badge badge-<?php echo $vp['is_active'] ? 'success' : 'secondary'; ?>">
                                                        <?php echo $vp['is_active'] ? 'Active' : 'Inactive'; ?>
                                                    </span>
                                                </td>
                                                <td><?php echo date('d M Y', strtotime($vp['created_at'])); ?></td>
                                                <td>
                                                    <div class="dropdown">
                                                        <button class="btn btn-sm btn-outline-secondary dropdown-toggle" type="button" data-toggle="dropdown">
                                                            <i class="feather icon-more-vertical"></i>
                                                        </button>
                                                        <div class="dropdown-menu">
                                                            <a class="dropdown-item" href="<?php echo base_url($url.'/management/vice_principal/edit/'.$vp['id']); ?>">
                                                                <i class="feather icon-edit"></i> Edit
                                                            </a>
                                                            <a class="dropdown-item text-danger" href="#" onclick="confirmDelete(<?php echo $vp['id']; ?>, '<?php echo htmlspecialchars($vp['name']); ?>')">
                                                                <i class="feather icon-trash"></i> Delete
                                                            </a>
                                                        </div>
                                                    </div>
                                                </td>
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
    if (confirm('Are you sure you want to delete the assistant administrator "' + name + '"? This action cannot be undone.')) {
        window.location.href = '<?php echo base_url($url.'/management/vice_principal/delete/'); ?>' + id;
    }
}
</script>