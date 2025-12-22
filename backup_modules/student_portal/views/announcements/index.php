<div class="layout-content">
    <div class="container-fluid flex-grow-1 container-p-y">
        <h4 class="font-weight-bold py-3 mb-0">Announcements</h4>
        <div class="text-muted small mt-0 mb-4 d-block breadcrumb">
            <ol class="breadcrumb">
                <li class="breadcrumb-item"><a href="<?php echo base_url('student-portal/dashboard'); ?>">Dashboard</a></li>
                <li class="breadcrumb-item active">Announcements</li>
            </ol>
        </div>

        <?php if ($this->session->flashdata('message')) { ?>
            <div class="alert alert-dark-<?= $this->session->flashdata('message')[0] ?> alert-dismissible fade show" id="alert">
                <button type="button" class="close" data-dismiss="alert">×</button>
                <span><?= $this->session->flashdata('message')[1] ?></span>
            </div>
        <?php } ?>

        <!-- Announcements List -->
        <div class="row">
            <?php if (empty($announcements)): ?>
                <div class="col-md-12">
                    <div class="text-center py-5">
                        <i class="feather icon-bell" style="font-size: 4rem; color: #ccc;"></i>
                        <h4 class="mt-3">No Announcements</h4>
                        <p class="text-muted">There are no announcements at this time.</p>
                        <a href="<?php echo base_url('student-portal/dashboard'); ?>" class="btn btn-primary">
                            Back to Dashboard
                        </a>
                    </div>
                </div>
            <?php else: ?>
                <?php foreach ($announcements as $announcement): ?>
                    <div class="col-md-12 mb-4">
                        <div class="card announcement-card">
                            <div class="card-header bg-gradient-warning text-white">
                                <div class="d-flex justify-content-between align-items-center">
                                    <h6 class="mb-0"><?php echo htmlspecialchars($announcement['title'] ?? 'Announcement'); ?></h6>
                                    <small><?php echo isset($announcement['created_at']) ? date('M d, Y', strtotime($announcement['created_at'])) : 'N/A'; ?></small>
                                </div>
                            </div>

                            <div class="card-body">
                                <!-- Announcement Meta Information -->
                                <div class="mb-3">
                                    <div class="row">
                                        <div class="col-md-6">
                                            <small class="text-muted">
                                                <i class="feather icon-user"></i>
                                                <?php echo isset($announcement['sender_name']) ? htmlspecialchars($announcement['sender_name']) : 'System'; ?>
                                            </small>
                                        </div>
                                        <div class="col-md-6 text-right">
                                            <?php if (isset($announcement['visibility']) && $announcement['visibility'] === 'department'): ?>
                                                <small class="text-muted">
                                                    <i class="feather icon-users"></i>
                                                    Department: <?php echo isset($announcement['department_name']) ? htmlspecialchars($announcement['department_name']) : 'N/A'; ?>
                                                </small>
                                            <?php else: ?>
                                                <small class="text-muted">
                                                    <i class="feather icon-globe"></i>
                                                    Public Announcement
                                                </small>
                                            <?php endif; ?>
                                        </div>
                                    </div>
                                </div>

                                <!-- Announcement Content -->
                                <div class="announcement-content">
                                    <?php echo isset($announcement['message']) ? nl2br(htmlspecialchars($announcement['message'])) : 'No content available.'; ?>
                                </div>

                                <!-- Priority Badge -->
                                <?php if (isset($announcement['priority']) && $announcement['priority'] === 'high'): ?>
                                    <div class="mt-3">
                                        <span class="badge badge-danger">
                                            <i class="feather icon-alert-triangle"></i> High Priority
                                        </span>
                                    </div>
                                <?php endif; ?>
                            </div>
                        </div>
                    </div>
                <?php endforeach; ?>
            <?php endif; ?>
        </div>
    </div>
</div>

<style>
.announcement-card {
    transition: box-shadow 0.3s ease;
    border: none;
    box-shadow: 0 2px 4px rgba(0,0,0,0.1);
}

.announcement-card:hover {
    box-shadow: 0 4px 15px rgba(0,0,0,0.15);
}

.bg-gradient-warning {
    background: linear-gradient(135deg, #f093fb 0%, #f5576c 100%);
}

.card-header {
    border-bottom: none;
}

.announcement-content {
    line-height: 1.6;
}
</style>
