    </main> <!-- Fin del contenido principal -->

    <!-- Overlay para cerrar el menú en móviles -->
    <div id="sidebar-overlay" class="fixed inset-0 bg-black opacity-50 z-30 hidden sm:hidden"></div>

    <script>
        document.addEventListener('DOMContentLoaded', function () {
            const sidebar = document.getElementById('sidebar');
            const toggleButton = document.getElementById('sidebar-toggle');
            const overlay = document.getElementById('sidebar-overlay');

            function toggleSidebar() {
                if (!sidebar) return;
                const isVisible = !sidebar.classList.contains('-translate-x-full');
                sidebar.classList.toggle('-translate-x-full');
                overlay.classList.toggle('hidden');
                toggleButton.setAttribute('aria-expanded', !isVisible);
            }

            if (toggleButton) {
                toggleButton.addEventListener('click', function (e) {
                    e.stopPropagation();
                    toggleSidebar();
                });
            }

            if (overlay) {
                overlay.addEventListener('click', function () {
                    toggleSidebar();
                });
            }

            if (sidebar) {
                const sidebarLinks = sidebar.querySelectorAll('a');
                sidebarLinks.forEach(link => {
                    link.addEventListener('click', function() {
                        if (window.innerWidth < 640) { // Tailwind's 'sm' breakpoint
                            if (!sidebar.classList.contains('-translate-x-full')) {
                                toggleSidebar();
                            }
                        }
                    });
                });
            }
        });
    </script>

</body>
</html>
