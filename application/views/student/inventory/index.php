<div class="layout-content">
    <div class="container-fluid flex-grow-1 container-p-y">
        <h4 class="font-weight-bold py-3 mb-0">Music Inventory</h4>
        <div class="text-muted small mt-0 mb-4 d-block breadcrumb">
            <ol class="breadcrumb">
                <li class="breadcrumb-item"><a href="<?php echo base_url('student-portal/dashboard'); ?>">Dashboard</a></li>
                <li class="breadcrumb-item active">Music Inventory</li>
            </ol>
        </div>

        <?php if ($this->session->flashdata('message')) { ?>
            <div class="alert alert-dark-<?= $this->session->flashdata('message')[0] ?> alert-dismissible fade show" id="alert">
                <button type="button" class="close" data-dismiss="alert">×</button>
                <span><?= $this->session->flashdata('message')[1] ?></span>
            </div>
        <?php } ?>

        <!-- Instruments Grid -->
        <div class="row">
            <?php if (empty($instruments)): ?>
                <div class="col-md-12">
                    <div class="text-center py-5">
                        <i class="feather icon-music" style="font-size: 4rem; color: #ccc;"></i>
                        <h4 class="mt-3">No Instruments Available</h4>
                        <p class="text-muted">There are no musical instruments currently available.</p>
                        <a href="<?php echo base_url('student-portal/dashboard'); ?>" class="btn btn-primary">
                            Back to Dashboard
                        </a>
                    </div>
                </div>
            <?php else: ?>
                <?php foreach ($instruments as $instrument): ?>
                    <div class="col-md-6 col-lg-4 mb-4">
                        <div class="card h-100 instrument-card">
                            <div class="card-header bg-gradient-success text-white">
                                <div class="d-flex justify-content-between align-items-center">
                                    <span class="badge badge-light"><?php echo htmlspecialchars($instrument['serial_no']); ?></span>
                                    <span class="badge badge-light">Available</span>
                                </div>
                            </div>

                            <div class="card-body d-flex flex-column text-center">
                                <!-- Instrument Image -->
                                <?php if (!empty($instrument['instrument_image'])): ?>
                                    <div class="mb-3">
                                        <img src="<?php echo base_url($instrument['instrument_image']); ?>"
                                             alt="<?php echo htmlspecialchars($instrument['name']); ?>"
                                             class="img-fluid rounded" style="max-height: 150px; object-fit: cover;">
                                    </div>
                                <?php else: ?>
                                    <div class="mb-3">
                                        <i class="feather icon-music text-muted" style="font-size: 4rem;"></i>
                                    </div>
                                <?php endif; ?>

                                <!-- Instrument Details -->
                                <h5 class="card-title"><?php echo htmlspecialchars($instrument['name']); ?></h5>

                                <div class="mb-2">
                                    <span class="badge badge-secondary"><?php echo htmlspecialchars($instrument['category']); ?></span>
                                </div>

                                <div class="text-muted small mb-3">
                                    <?php if ($instrument['model']): ?>
                                        <div><strong>Model:</strong> <?php echo htmlspecialchars($instrument['model']); ?></div>
                                    <?php endif; ?>
                                    <?php if ($instrument['brand']): ?>
                                        <div><strong>Brand:</strong> <?php echo htmlspecialchars($instrument['brand']); ?></div>
                                    <?php endif; ?>
                                    <div><strong>Condition:</strong>
                                        <span class="badge badge-<?php
                                            echo $instrument['condition'] == 'excellent' ? 'success' :
                                                 ($instrument['condition'] == 'good' ? 'primary' :
                                                 ($instrument['condition'] == 'fair' ? 'warning' : 'danger'));
                                        ?>">
                                            <?php echo ucfirst($instrument['condition']); ?>
                                        </span>
                                    </div>
                                    <?php if ($instrument['instrument_price']): ?>
                                        <div><strong>Price:</strong> ₹<?php echo number_format($instrument['instrument_price'], 2); ?></div>
                                    <?php endif; ?>
                                </div>

                                <!-- Description -->
                                <?php if (!empty($instrument['description'])): ?>
                                    <p class="card-text text-muted small flex-grow-1">
                                        <?php echo htmlspecialchars(substr($instrument['description'], 0, 100)); ?>
                                        <?php echo strlen($instrument['description']) > 100 ? '...' : ''; ?>
                                    </p>
                                <?php endif; ?>

                                <!-- Action Button -->
                                <div class="mt-auto">
                                    <div class="d-grid">
                                        <button class="btn btn-success btn-sm" disabled title="Available for practice sessions">
                                            <i class="feather icon-check-circle"></i> Available
                                        </button>
                                        <small class="text-muted mt-1 d-block">
                                            Contact faculty for instrument requests
                                        </small>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                <?php endforeach; ?>
            <?php endif; ?>
        </div>
    </div>
</div>

<style>
.instrument-card {
    transition: transform 0.3s ease, box-shadow 0.3s ease;
    border: none;
    box-shadow: 0 2px 4px rgba(0,0,0,0.1);
}

.instrument-card:hover {
    transform: translateY(-5px);
    box-shadow: 0 4px 15px rgba(0,0,0,0.15);
}

.bg-gradient-success {
    background: linear-gradient(135deg, #11998e 0%, #38ef7d 100%);
}

.card-header {
    border-bottom: none;
}

.badge {
    font-size: 0.75em;
}

.instrument-card img {
    border-radius: 8px !important;
}
</style>
