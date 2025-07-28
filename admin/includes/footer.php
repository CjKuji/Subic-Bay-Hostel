<!-- ✅ Load the Focus plugin FIRST -->
<script defer src="https://unpkg.com/@alpinejs/focus@3.x.x/dist/cdn.min.js"></script>

<!-- ✅ Then load Alpine.js -->
<script defer src="https://unpkg.com/alpinejs@3.x.x/dist/cdn.min.js"></script>

<!-- ✅ Register the Focus plugin -->
<script>
    document.addEventListener('alpine:init', () => {
        Alpine.plugin(window.AlpineFocus);
    });
</script>

<!-- ✅ Lucide Icons Init -->
<script>
    document.addEventListener('DOMContentLoaded', () => {
        if (window.lucide) lucide.createIcons();
    });
</script>

</body>

</html>