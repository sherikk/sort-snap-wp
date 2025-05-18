
(function($) {
    // Document ready
    $(document).ready(function() {
        // Initialize sortable
        const $postList = $('#sortsnap-post-list');
        const $autoSave = $('#sortsnap-autosave');
        const $saveButton = $('#sortsnap-save');

        // Initialize sortable if post list exists
        if ($postList.length) {
            $postList.sortable({
                handle: '.sortsnap-post-handle',
                placeholder: 'sortsnap-placeholder',
                update: function(event, ui) {
                    // If autosave is enabled, save order
                    if ($autoSave.is(':checked')) {
                        saveOrder();
                    } else {
                        $saveButton.show();
                    }
                }
            });
        }

        // Toggle autosave
        $autoSave.on('change', function() {
            if ($(this).is(':checked')) {
                $saveButton.hide();
                saveOrder();
            } else {
                $saveButton.show();
            }
        });

        // Manual save button
        $saveButton.on('click', function() {
            saveOrder();
        });

        // Save order function
        function saveOrder() {
            // Get post IDs in order
            const postIds = [];
            $postList.find('.sortsnap-post-item').each(function() {
                postIds.push($(this).data('id'));
            });

            // Show loading
            $postList.addClass('sortsnap-loading');

            // Send AJAX request
            $.ajax({
                url: sortsnap.ajax_url,
                type: 'POST',
                data: {
                    action: 'sortsnap_save_order',
                    post_ids: postIds,
                    nonce: sortsnap.nonce
                },
                success: function(response) {
                    $postList.removeClass('sortsnap-loading');
                    
                    if (response.success) {
                        // Show success notification
                        const $notice = $('<div class="notice notice-success is-dismissible"><p>Post order saved successfully.</p></div>');
                        $postList.before($notice);
                        
                        // Automatically remove notice after 3 seconds
                        setTimeout(function() {
                            $notice.fadeOut(300, function() {
                                $(this).remove();
                            });
                        }, 3000);
                        
                        // Hide save button if showing
                        $saveButton.hide();
                    } else {
                        // Show error notification
                        const errorMessage = response.data || 'An error occurred while saving the order.';
                        const $notice = $('<div class="notice notice-error is-dismissible"><p>' + errorMessage + '</p></div>');
                        $postList.before($notice);
                    }
                },
                error: function() {
                    $postList.removeClass('sortsnap-loading');
                    
                    // Show error notification
                    const $notice = $('<div class="notice notice-error is-dismissible"><p>An error occurred while saving the order.</p></div>');
                    $postList.before($notice);
                }
            });
        }
    });
})(jQuery);

