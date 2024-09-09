// Basic JavaScript for adding interactivity

// Confirmation dialog for deleting WAF rules
function confirmDeleteRule(id) {
    var confirmDelete = confirm("Are you sure you want to delete this rule?");
    if (confirmDelete) {
        window.location.href = "delete_rule.php?id=" + id;
    }
}

// Toggle WAF status on the settings page
function toggleWAFStatus() {
    var confirmToggle = confirm("Do you want to toggle the WAF status?");
    if (confirmToggle) {
        window.location.href = "settings.php?action=toggle";
    }
}

// Handle export logs as JSON or CSV
function exportLogs(format) {
    window.location.href = "export_logs.php?format=" + format;
}
