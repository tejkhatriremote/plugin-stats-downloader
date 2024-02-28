<?php
/**
 * Plugin Name: Plugin Stats Downloader
 * Description: Download plugin statistics as CSV.
 * Version: 1.0
 * Author: Tejas Khatri
 */

// Enqueue jQuery
function enqueue_custom_jquery() {
    wp_enqueue_script('jquery');
}
add_action('admin_enqueue_scripts', 'enqueue_custom_jquery');

// Add custom JavaScript to the admin footer
function plugin_stats_downloader_script() {
    $current_screen = get_current_screen();

    // Check if the current screen is the plugins page
    if ($current_screen && $current_screen->base === 'plugins') {
        ?>
        <script>
            jQuery(document).ready(function ($) {
                // Create the anchor tag and append it to the plugins page
                var downloadLink = $('<a href="#" class="page-title-action download-plugin-stats">Download Plugin Stats</a>');
                $('.wrap h1').after(downloadLink);

                // Add click event to the anchor tag
                downloadLink.on('click', function (event) {
                    event.preventDefault();

                    var currentDate = new Date();
                    var formattedDate = `${currentDate.getFullYear()}_${currentDate.getMonth() + 1}_${currentDate.getDate()}_${currentDate.getHours()}_${currentDate.getMinutes()}_${currentDate.getSeconds()}`;

                    var csvData = `"Plugin Name",Version,Status,Update Available?\n`;

                    var pluginRows = $('table.plugins tr');

                    pluginRows.each(function () {
                        var pluginName = $(this).find('.plugin-title strong').text().trim();

                        if (pluginName === '') {
                            return;
                        }

                        var pluginVersion = 'V ' + $(this).find('.plugin-version-author-uri').text().trim().split('|')[0].replace('Version', '').trim();
                        var isPluginActive = !$(this).hasClass('inactive');
                        var hasUpdate = $(this).hasClass('update');

                        csvData += `"${pluginName}","${pluginVersion}","${isPluginActive ? 'Active' : 'Inactive'}","${hasUpdate ? 'Yes' : 'No'}"\n`;
                    });

                    var blob = new Blob([csvData], { type: 'text/csv;charset=utf-8' });
                    var link = document.createElement('a');
                    link.href = window.URL.createObjectURL(blob);
                    link.download = `plugin_info_${formattedDate}.csv`;

                    document.body.appendChild(link);
                    link.click();

                    document.body.removeChild(link);
                });
            });
        </script>
        <?php
    }
}
add_action('admin_footer', 'plugin_stats_downloader_script');
