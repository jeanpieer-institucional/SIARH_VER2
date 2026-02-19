            </div>
        </main>
    </div>
    
    <script src="<?= APP_URL ?>/public/assets/js/main.js?v=<?= time() ?>"></script>
    <?php if (isset($includeCharts) && $includeCharts): ?>
    <script>
        document.addEventListener('DOMContentLoaded', function() {
            initCharts();
        });
    </script>
    <?php endif; ?>
</body>
</html>
