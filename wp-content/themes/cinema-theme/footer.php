
<footer class="bg-white rounded-lg shadow m-4 dark:bg-gray-800">
    <div class="w-full mx-auto p-4 md:flex md:items-center md:justify-between">
        <span class="text-sm text-gray-500 sm:text-center dark:text-gray-400">&copy; <?php echo date('Y'); ?> <?php bloginfo('name'); ?></a>. Tous droits réservés.</span>
        <ul class="flex flex-wrap items-center mt-3 text-sm font-medium text-gray-500 dark:text-gray-400 sm:mt-0">
            <li>
                <a href="#" class="hover:underline me-4 md:me-6">A propos de nous</a>
            </li>
            <li>
                <a href="#" class="hover:underline">Nous contacter</a>
            </li>
        </ul>

        <?php wp_footer(); ?>
    </div>
</footer>

</body>
</html>