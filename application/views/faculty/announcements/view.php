<div class="layout-content">
    <div class="container-fluid flex-grow-1 container-p-y">
        <h4 class="font-weight-bold py-3 mb-0">Announcement Details</h4>
        <div class="text-muted small mt-0 mb-4 d-block breadcrumb">
            <ol class="breadcrumb">
                <li class="breadcrumb-item"><a href="<?php echo base_url($url.'/announcements'); ?>"><i class="feather icon-home"></i></a></li>
                <li class="breadcrumb-item"><a href="<?php echo base_url($url.'/announcements'); ?>">Announcements</a></li>
                <li class="breadcrumb-item">View</li>
            </ol>
        </div>

        <?php if ($this->session->flashdata('message')) { ?>
            <div class="alert alert-dark-<?= $this->session->flashdata('message')[0] ?> alert-dismissible fade show" id="alert">
                <button type="button" class="close" data-dismiss="alert">×</button>
                <span><?= $this->session->flashdata('message')[1] ?></span>
            </div>
        <?php } ?>

        <div class="row">
            <div class="col-md-10 mx-auto">
                <div class="card">
                    <div class="card-header">
                        <div class="d-flex justify-content-between align-items-center">
                            <div>
                                <h5 class="mb-0"><?php echo htmlspecialchars($announcement['title']); ?></h5>
                                <small class="text-muted">
                                    Posted by: <strong><?php echo htmlspecialchars($announcement['sender_name']); ?></strong>
                                    <?php if ($announcement['department_name']): ?>
                                        (<?php echo htmlspecialchars($announcement['department_name']); ?>)
                                    <?php endif; ?>
                                    • <?php echo date('F d, Y \a\t H:i', strtotime($announcement['created_at'])); ?>
                                </small>
                            </div>
                            <div>
                                <?php if ($announcement['visibility'] == 'all'): ?>
                                    <span class="badge badge-success badge-lg">Public Announcement</span>
                                <?php else: ?>
                                    <span class="badge badge-warning badge-lg">Department Announcement</span>
                                <?php endif; ?>
                                <?php if ($announcement['priority'] == 'high'): ?>
                                    <span class="badge badge-danger badge-lg ml-2">High Priority</span>
                                <?php endif; ?>
                            </div>
                        </div>
                    </div>
                    <div class="card-body">
                        <div class="announcement-content">
                            <?php echo nl2br(htmlspecialchars($announcement['message'])); ?>
                        </div>
                    </div>
                    <div class="card-footer">
                        <div class="d-flex justify-content-between align-items-center">
                            <div>
                                <small class="text-muted">
                                    Last updated: <?php echo date('F d, Y \a\t H:i', strtotime($announcement['updated_at'])); ?>
                                </small>
                            </div>
                            <div>
                                <a href="<?php echo base_url($url.'/announcements'); ?>" class="btn btn-secondary btn-sm">
                                    <i class="feather icon-arrow-left"></i> Back to Announcements
                                </a>
                                <?php if ($announcement['sender_id'] == $this->session->userdata($url)['id'] || $this->session->userdata($url)['role'] == ROLE_SUPERADMIN): ?>
                                    <a href="<?php echo base_url($url.'/announcements/edit/'.$announcement['id']); ?>" class="btn btn-primary btn-sm">
                                        <i class="feather icon-edit"></i> Edit
                                    </a>
                                    <button type="button" class="btn btn-danger btn-sm" onclick="confirmDelete(<?php echo $announcement['id']; ?>)">
                                        <i class="feather icon-trash"></i> Delete
                                    </button>
                                <?php endif; ?>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Additional Information -->
                <div class="card mt-4">
                    <div class="card-header">
                        <h6 class="mb-0">Announcement Information</h6>
                    </div>
                    <div class="card-body">
                        <div class="row">
                            <div class="col-md-6">
                                <div class="mb-3">
                                    <strong>Visibility:</strong>
                                    <?php if ($announcement['visibility'] == 'all'): ?>
                                        <span class="badge badge-success">All Users</span>
                                    <?php else: ?>
                                        <span class="badge badge-warning">Department Only</span>
                                        <?php if ($announcement['department_name']): ?>
                                            <br><small class="text-muted">Department: <?php echo htmlspecialchars($announcement['department_name']); ?></small>
                                        <?php endif; ?>
                                    <?php endif; ?>
                                </div>
                                <div class="mb-3">
                                    <strong>Priority:</strong>
                                    <?php if ($announcement['priority'] == 'high'): ?>
                                        <span class="badge badge-danger">High Priority</span>
                                    <?php else: ?>
                                        <span class="badge badge-secondary">Normal Priority</span>
                                    <?php endif; ?>
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="mb-3">
                                    <strong>Created:</strong><br>
                                    <small class="text-muted"><?php echo date('F d, Y \a\t H:i:s', strtotime($announcement['created_at'])); ?></small>
                                </div>
                                <div class="mb-3">
                                    <strong>Last Updated:</strong><br>
                                    <small class="text-muted"><?php echo date('F d, Y \a\t H:i:s', strtotime($announcement['updated_at'])); ?></small>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<script>
function confirmDelete(id) {
    if (confirm('Are you sure you want to delete this announcement? This action cannot be undone.')) {
        window.location.href = '<?php echo base_url($url.'/announcements/delete/'); ?>' + id;
    }
}
</script>









