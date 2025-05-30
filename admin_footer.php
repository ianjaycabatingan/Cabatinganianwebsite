                                </div> <!-- Close .main-content -->
            </main> <!-- Close main content area -->
        </div> <!-- Close .row -->
    </div> <!-- Close .container-fluid -->

    <!-- Bootstrap 5 JS Bundle -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    
    <!-- Admin Panel Custom Scripts -->
    <script>
        // Highlight active sidebar item
        document.addEventListener('DOMContentLoaded', function() {
            const currentPage = window.location.pathname.split('/').pop();
            const sidebarLinks = document.querySelectorAll('.admin-sidebar .list-group-item');
            
            sidebarLinks.forEach(link => {
                if (link.getAttribute('href') === currentPage) {
                    link.classList.add('active');
                }
            });
        });
    </script>
</body>
</html>
