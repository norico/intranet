jQuery(document).ready(function($) {
    var urlContainer = $('#urls-container');

    // Sortable initialization with more robust options
    urlContainer.sortable({
        handle: '.sort-handle',
        axis: 'y',
        containment: 'parent',
        tolerance: 'pointer',
        update: function(event, ui) {
            // Reindex after sorting
            urlContainer.find('.url-input-row').each(function(newIndex) {
                $(this).attr('data-index', newIndex);
                $(this).find('input').attr('name', '<?php echo OPTION_NAME; ?>[' + newIndex + ']');
            });
        }
    });

    // Add new URL button
    $('#add-url-button').on('click', function() {
        var newIndex = urlContainer.find('.url-input-row').length;
        var template = $('#url-input-template').html();

        // Careful replacement of index
        var newRowHtml = template.replace(/\{\{index\}\}/g, newIndex);

        urlContainer.append(newRowHtml);
    });

    // Remove URL button
    urlContainer.on('click', '.remove-url-button', function() {
        var rows = urlContainer.find('.url-input-row');

        if (rows.length > 1) {
            $(this).closest('.url-input-row').remove();

            // Reindex remaining rows
            urlContainer.find('.url-input-row').each(function(newIndex) {
                $(this).attr('data-index', newIndex);
                $(this).find('input').attr('name', '<?php echo OPTION_NAME; ?>[' + newIndex + ']');
            });
        }
    });

    // Ensure at least one field
    if (urlContainer.find('.url-input-row').length === 0) {
        $('#add-url-button').trigger('click');
    }
});