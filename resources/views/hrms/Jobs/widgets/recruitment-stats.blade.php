<div class="card">
    <div class="card-header">
        <h5 class="card-title mb-0">
            <i class="fas fa-chart-pie me-2"></i>Recruitment Statistics
        </h5>
    </div>
    <div class="card-body">
        <div class="row" id="recruitmentStats">
            <div class="col-md-3 col-sm-6 mb-3">
                <div class="stat-card bg-primary text-white">
                    <div class="stat-content">
                        <h3 id="totalCandidates">0</h3>
                        <p class="mb-0">Total Candidates</p>
                    </div>
                </div>
            </div>
            <div class="col-md-3 col-sm-6 mb-3">
                <div class="stat-card bg-warning text-white">
                    <div class="stat-content">
                        <h3 id="interviewsScheduled">0</h3>
                        <p class="mb-0">Interviews Scheduled</p>
                    </div>
                </div>
            </div>
            <div class="col-md-3 col-sm-6 mb-3">
                <div class="stat-card bg-success text-white">
                    <div class="stat-content">
                        <h3 id="candidatesSelected">0</h3>
                        <p class="mb-0">Selected</p>
                    </div>
                </div>
            </div>
            <div class="col-md-3 col-sm-6 mb-3">
                <div class="stat-card bg-danger text-white">
                    <div class="stat-content">
                        <h3 id="candidatesRejected">0</h3>
                        <p class="mb-0">Rejected</p>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<script>
function loadRecruitmentStats() {
    fetch('/recruitment/dashboard-stats')
        .then(response => response.json())
        .then(data => {
            document.getElementById('totalCandidates').textContent = data.total_candidates || 0;
            document.getElementById('interviewsScheduled').textContent = data.interviews_scheduled || 0;
            document.getElementById('candidatesSelected').textContent = data.candidates_selected || 0;
            document.getElementById('candidatesRejected').textContent = data.candidates_rejected || 0;
        });
}
document.addEventListener('DOMContentLoaded', loadRecruitmentStats);
</script>
