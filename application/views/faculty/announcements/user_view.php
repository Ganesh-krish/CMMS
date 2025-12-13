<!-- Announcements Section for Students/Staff -->
<div class="card mb-4">
    <div class="card-header d-flex justify-content-between align-items-center">
        <h6 class="mb-0">
            <i class="feather icon-bell"></i> Latest Announcements
        </h6>
        <a href="#" class="btn btn-sm btn-outline-primary" onclick="loadAllAnnouncements()">View All</a>
    </div>
    <div class="card-body" id="announcements-container">
        <div class="text-center py-3">
            <div class="spinner-border spinner-border-sm text-primary" role="status">
                <span class="sr-only">Loading...</span>
            </div>
            <p class="mt-2 mb-0">Loading announcements...</p>
        </div>
    </div>
</div>

<script>
function loadAnnouncements() {
    fetch('<?php echo base_url($url."/announcements/get_user_announcements"); ?>')
        .then(response => response.json())
        .then(data => {
            if (data.status === 'success') {
                displayAnnouncements(data.data.slice(0, 5)); // Show only latest 5
            } else {
                document.getElementById('announcements-container').innerHTML =
                    '<div class="text-center py-3"><p class="text-muted mb-0">No announcements available.</p></div>';
            }
        })
        .catch(error => {
            console.error('Error loading announcements:', error);
            document.getElementById('announcements-container').innerHTML =
                '<div class="text-center py-3"><p class="text-muted mb-0">Error loading announcements.</p></div>';
        });
}

function loadAllAnnouncements() {
    fetch('<?php echo base_url($url."/announcements/get_user_announcements"); ?>')
        .then(response => response.json())
        .then(data => {
            if (data.status === 'success') {
                displayAnnouncements(data.data);
            } else {
                document.getElementById('announcements-container').innerHTML =
                    '<div class="text-center py-3"><p class="text-muted mb-0">No announcements available.</p></div>';
            }
        })
        .catch(error => {
            console.error('Error loading announcements:', error);
            document.getElementById('announcements-container').innerHTML =
                '<div class="text-center py-3"><p class="text-muted mb-0">Error loading announcements.</p></div>';
        });
}

function displayAnnouncements(announcements) {
    if (announcements.length === 0) {
        document.getElementById('announcements-container').innerHTML =
            '<div class="text-center py-3"><p class="text-muted mb-0">No announcements available.</p></div>';
        return;
    }

    let html = '<div class="announcements-list">';
    announcements.forEach(announcement => {
        const visibilityBadge = announcement.visibility === 'all' ?
            '<span class="badge badge-success badge-sm">Public</span>' :
            '<span class="badge badge-warning badge-sm">Department</span>';

        const priorityClass = announcement.priority === 'high' ? 'border-left-danger' : '';

        html += `
            <div class="announcement-item ${priorityClass} border-left-primary border-left-4 pb-3 mb-3 pl-3">
                <div class="d-flex justify-content-between align-items-start">
                    <div class="flex-grow-1">
                        <h6 class="mb-1">
                            ${announcement.title}
                            ${visibilityBadge}
                            ${announcement.priority === 'high' ? '<span class="badge badge-danger badge-sm ml-1">High Priority</span>' : ''}
                        </h6>
                        <p class="text-muted small mb-2">
                            By: <strong>${announcement.sender_name}</strong>
                            ${announcement.department_name ? `(${announcement.department_name})` : ''}
                            • ${new Date(announcement.created_at).toLocaleDateString()}
                        </p>
                        <div class="announcement-content">
                            ${announcement.message.length > 150 ?
                                announcement.message.substring(0, 150) + '...' :
                                announcement.message
                            }
                        </div>
                    </div>
                </div>
            </div>
        `;
    });
    html += '</div>';

    document.getElementById('announcements-container').innerHTML = html;
}

// Load announcements when page loads
document.addEventListener('DOMContentLoaded', function() {
    loadAnnouncements();
});
</script>
