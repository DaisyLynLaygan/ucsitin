    <!-- Footer -->
    <footer class="bg-white border-top py-4 mt-auto">
        <div class="container-fluid">
            <div class="row align-items-center">
                <div class="col-md-6 text-center text-md-start">
                    <span class="text-muted">&copy; <?= date('Y'); ?> CCS Sit-In Monitoring System. All rights reserved.</span>
                </div>
                <div class="col-md-6 text-center text-md-end">
                    <span class="text-muted">Version 1.0.0</span>
                </div>
            </div>
        </div>
    </footer>

    <!-- Sidebar Toggle Script -->
    <script>
        function toggleSidebar() {
            const sidebar = document.getElementById("sidebar");
            sidebar.classList.toggle("collapsed");
        }
    </script>

    </div> <!-- End of Main Content -->
</div> <!-- End of d-flex container -->
</body>
</html>
