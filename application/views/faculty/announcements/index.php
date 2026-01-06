<div class="layout-content">
    <div class="container-fluid flex-grow-1 container-p-y">
        <h4 class="font-weight-bold py-3 mb-0">Announcements</h4>
        <div class="text-muted small mt-0 mb-4 d-block breadcrumb">
            <ol class="breadcrumb">
                <li class="breadcrumb-item"><a href="#"><i class="feather icon-home"></i></a></li>
                <li class="breadcrumb-item">Announcements</li>
            </ol>
        </div>

        <?php if ($this->session->flashdata('message')) { ?>
            <div class="alert alert-dark-<?= $this->session->flashdata('message')[0] ?> alert-dismissible fade show" id="alert">
                <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                <span><?= $this->session->flashdata('message')[1] ?></span>
            </div>
        <?php } ?>

        <!-- Statistics Cards -->
        <div class="row mb-4">
            <div class="col-md-3">
                <div class="card">
                    <div class="card-body text-center">
                        <h4 class="text-primary"><?php echo $stats['total_announcements']; ?></h4>
                        <p class="mb-0">Total Announcements</p>
                    </div>
                </div>
            </div>
            <div class="col-md-3">
                <div class="card">
                    <div class="card-body text-center">
                        <h4 class="text-info"><?php echo $stats['this_month']; ?></h4>
                        <p class="mb-0">This Month</p>
                    </div>
                </div>
            </div>
            <div class="col-md-3">
                <div class="card">
                    <div class="card-body text-center">
                        <h4 class="text-success"><?php echo $stats['public_announcements']; ?></h4>
                        <p class="mb-0">Public Announcements</p>
                    </div>
                </div>
            </div>
            <div class="col-md-3">
                <div class="card">
                    <div class="card-body text-center">
                        <h4 class="text-warning"><?php echo $stats['department_announcements']; ?></h4>
                        <p class="mb-0">Department Announcements</p>
                    </div>
                </div>
            </div>
        </div>

        <!-- Filters and Actions -->
        <div class="row mb-4">
            <div class="col-md-12">
                <div class="card">
                    <div class="card-body">
                        <div class="row">
                            <div class="col-md-6">
                                <form method="get" class="d-flex">
                                    <input type="text" name="search" class="form-control" placeholder="Search announcements..." value="<?php echo $this->input->get('search'); ?>">
                                    <button type="submit" class="btn btn-primary ml-2">Search</button>
                                </form>
                            </div>
                            <div class="col-md-6 text-right">
                                <?php if ($permissions['can_create']): ?>
                                    <button type="button" class="btn btn-success" data-bs-toggle="modal" data-bs-target="#createAnnouncementModal">
                                        <i class="feather icon-plus"></i> Create Announcement
                                    </button>
                                <?php endif; ?>
                            </div>
                        </div>

                        <div class="row mt-3">
                            <div class="col-md-12">
                                <div class="btn-group" role="group">
                                    <a href="<?php echo base_url($url.'/announcements'); ?>" class="btn btn-outline-primary <?php echo !$this->input->get('visibility') ? 'active' : ''; ?>">All</a>
                                    <a href="<?php echo base_url($url.'/announcements?visibility=all'); ?>" class="btn btn-outline-success <?php echo $this->input->get('visibility') == 'all' ? 'active' : ''; ?>">Public</a>
                                    <a href="<?php echo base_url($url.'/announcements?visibility=department'); ?>" class="btn btn-outline-warning <?php echo $this->input->get('visibility') == 'department' ? 'active' : ''; ?>">Department</a>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Announcements List -->
        <div class="row">
            <div class="col-md-12">
                <div class="card">
                    <div class="card-body">
                        <?php if (empty($announcements)): ?>
                            <div class="text-center py-5">
                                <i class="feather icon-bell-off" style="font-size: 4rem; color: #ccc;"></i>
                                <h4 class="mt-3">No Announcements</h4>
                                <p class="text-muted">There are no announcements to display.</p>
                                <?php if ($permissions['can_create']): ?>
                                    <button type="button" class="btn btn-primary" data-bs-toggle="modal" data-bs-target="#createAnnouncementModal">
                                        Create First Announcement
                                    </button>
                                <?php endif; ?>
                            </div>
                        <?php else: ?>
                            <div class="announcements-list">
                                <?php foreach ($announcements as $announcement): ?>
                                    <div class="announcement-item border-bottom pb-3 mb-3">
                                        <div class="d-flex justify-content-between align-items-start">
                                            <div class="flex-grow-1">
                                                <h5 class="mb-1">
                                                    <?php echo htmlspecialchars($announcement['title']); ?>
                                                    <?php if ($announcement['visibility'] == 'all'): ?>
                                                        <span class="badge badge-success badge-pill">Public</span>
                                                    <?php else: ?>
                                                        <span class="badge badge-warning badge-pill">Department</span>
                                                    <?php endif; ?>
                                                    <?php if ($announcement['priority'] == 'high'): ?>
                                                        <span class="badge badge-danger badge-pill">High Priority</span>
                                                    <?php endif; ?>
                                                </h5>
                                                <p class="text-muted mb-2">
                                                    By: <strong><?php echo htmlspecialchars($announcement['sender_name']); ?></strong>
                                                    <?php if ($announcement['department_name']): ?>
                                                        (<?php echo htmlspecialchars($announcement['department_name']); ?>)
                                                    <?php endif; ?>
                                                    • <?php echo date('M d, Y H:i', strtotime($announcement['created_at'])); ?>
                                                </p>
                                                <div class="announcement-content">
                                                    <?php echo nl2br(htmlspecialchars(substr($announcement['message'], 0, 200))); ?>
                                                    <?php if (strlen($announcement['message']) > 200): ?>
                                                        <span class="text-muted">... <a href="<?php echo base_url($url.'/announcements/view/'.$announcement['id']); ?>" class="text-primary">Read more</a></span>
                                                    <?php endif; ?>
                                                </div>
                                            </div>
                                            <div class="ml-3">
                                                <div class="dropdown">
                                                    <button class="btn btn-sm btn-outline-secondary dropdown-toggle" type="button" data-bs-toggle="dropdown">
                                                        <i class="feather icon-more-vertical"></i>
                                                    </button>
                                                    <div class="dropdown-menu">
                                                        <a class="dropdown-item" href="<?php echo base_url($url.'/announcements/view/'.$announcement['id']); ?>">
                                                            <i class="feather icon-eye"></i> View
                                                        </a>
                                                        <?php
                                                        $can_edit_delete = false;
                                                        if ($announcement['sender_id'] == $current_user['id']) {
                                                            // User can always edit/delete their own announcements
                                                            $can_edit_delete = true;
                                                        } elseif ($current_user['role'] == ROLE_PRINCIPAL) {
                                                            // Principal can edit/delete all announcements
                                                            $can_edit_delete = true;
                                                        } elseif ($current_user['role'] == ROLE_VICE_PRINCIPAL && in_array($announcement['sender_role'], [ROLE_HOD, ROLE_STAFF, ROLE_CUSTODIAN])) {
                                                            // Vice Principal can edit/delete announcements from HOD, Staff, and Custodian
                                                            $can_edit_delete = true;
                                                        }
                                                        // HODs can only edit/delete their own announcements (covered by first condition)
                                                        if ($can_edit_delete):
                                                        ?>
                                                            <a class="dropdown-item" href="<?php echo base_url($url.'/announcements/edit/'.$announcement['id']); ?>">
                                                                <i class="feather icon-edit"></i> Edit
                                                            </a>
                                                            <a class="dropdown-item text-danger" href="#" onclick="confirmDelete(<?php echo $announcement['id']; ?>)">
                                                                <i class="feather icon-trash"></i> Delete
                                                            </a>
                                                        <?php endif; ?>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                <?php endforeach; ?>
                            </div>
                        <?php endif; ?>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<script>
function confirmDelete(id) {
    if (confirm('Are you sure you want to delete this announcement?')) {
        window.location.href = '<?php echo base_url($url.'/announcements/delete/'); ?>' + id;
    }
}
</script>




