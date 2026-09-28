    </main>

    <!-- Footer Component -->
    <footer class="bg-white border-top py-4 mt-auto">
        <div class="container text-center text-muted small">
            <div class="row align-items-center">
                <div class="col-md-6 text-md-start mb-2 mb-md-0">
                    <p class="mb-0">
                        &copy; <?php echo date('Y'); ?> <strong>University Academic Portal</strong>. All Rights Reserved.
                    </p>
                    <small class="text-secondary">Comprehensive Student Management System & Academic Curriculum</small>
                </div>
                <div class="col-md-6 text-md-end">
                    <span class="badge bg-secondary-subtle text-secondary border me-1">PHP 8.2+</span>
                    <span class="badge bg-secondary-subtle text-secondary border me-1">MySQL PDO</span>
                    <span class="badge bg-secondary-subtle text-secondary border">Bootstrap 5.3</span>
                </div>
            </div>
        </div>
    </footer>

    <!-- Bootstrap 5.3.3 JavaScript Bundle with Popper -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
    
    <!-- Custom Application JavaScript -->
    <script src="<?php echo e($assetPath ?? '../assets/'); ?>js/script.js"></script>
</body>
</html>
