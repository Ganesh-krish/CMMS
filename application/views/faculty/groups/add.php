<style>
    .choices {
        min-height: 38px !important;
        /* Same as Bootstrap form-control */
    }

    .choices__inner {
        min-height: 38px !important;
        padding: 6px 12px !important;
        background-color: #fff !important;
        /* Matches form input */
    }
</style>
<div class="layout-content">
    <div class="container-fluid flex-grow-1 container-p-y">
        <h4 id="Title" class="font-weight-bold py-3 mb-0">
            <?php echo $title ?>
        </h4>
        <div class="text-muted small mt-0 mb-4 d-block breadcrumb">
            <ol class="breadcrumb">
                <li class="breadcrumb-item"><a href="#"><i class="feather icon-home"></i></a></li>
                <li class="breadcrumb-item">Student</li>
            </ol>
        </div>

        <!-- Flash Message -->
        <?php if ($this->session->flashdata('message')) { ?>
            <div class="alert alert-dark-<?= $this->session->flashdata('message')[0] ?> alert-dismissible fade show" id="alert">
                <button type="button" class="close" data-dismiss="alert">&times;</button>
                <span><?= $this->session->flashdata('message')[1] ?></span>
            </div>
        <?php } ?>

        <div class="card p-4 shadow-sm rounded">
            <form action="<?= isset($groups) ? base_url($url . '/groups/edit/' . $groups['id']) : base_url($url . '/groups/add') ?>" method="post">
                <div class="row">
                    <div class="col-md-6">
                        <div class="form-group">
                            <label for="group_name">Group Name</label>
                            <input type="text" class="form-control" id="group_name" name="group_name" value="<?php if (isset($groups['group_name'])) {
                                                                                                                    echo $groups['group_name'];
                                                                                                                } ?>" required>
                        </div>
                    </div>
                    <!-- <div class="col-md-6">
                        <div class="form-group">
                            <label for="group_expiry">Group Expiry (in days)</label>
                            <input type="date" class="form-control" id="group_expiry" name="group_expiry" value="<?php if (isset($groups['group_expiry'])) {
                                                                                                                        echo $groups['group_expiry'];
                                                                                                                    } ?>" required>
                        </div>
                    </div> -->
                    <!-- <div class="col-md-6">
                        <div class="form-group">
                            <label for="group_members">Select Group Members</label>
                            <select class="form-control choices-multiple" id="group_members" name="group_members[]" multiple>
                                <?php if (!empty($staff)) {
                                    foreach ($staff as $row) {    
                                        $selected = (in_array($row['id'], $studentIds)) ? 'selected' : '';
                                        ?>
                                        <option value="<?php echo $row['id'] ?>" <?php echo $selected ?>>
                                            <?php echo $row['name']; ?>
                                        </option>
                                    <?php } 
                                } ?>
                            </select>
                        </div>
                    </div>
                    <div class="col-md-6">
                        <div class="form-group">
                            <label for="description">Description</label>
                            <textarea class="form-control" id="description" name="description" rows="3" required><?php
                                                                                                                    if (isset($groups['group_description'])) {
                                                                                                                        echo $groups['group_description'];
                                                                                                                    } ?></textarea>
                        </div>
                    </div> -->
                </div>
                <button type="submit" class="btn btn-primary"><?php echo isset($groups) ? 'Update' : 'Save Group' ?></button>
                <a href="<?= base_url($url . '/'.$designation. '/students') ?>" class="btn btn-secondary">Cancel</a>
            </form>
        </div>
    </div>
</div>
<script>
   document.addEventListener("DOMContentLoaded", function() {
    new Choices("#group_members", {
        removeItemButton: true,
        placeholder: true,
        placeholderValue: "Select Group Members",
        classNames: {
            containerOuter: 'choices', // Matches other inputs
        }
    });

    // Fix alignment issue
    document.querySelector(".choices__inner").style.padding = "8px";
});
</script>