document.addEventListener('DOMContentLoaded', function () {

    // Save the original WP inline edit function
    const wp_inline_edit = inlineEditPost.edit;

    // Override with our custom version
    inlineEditPost.edit = function (post_id) {

        // Call original WP function
        wp_inline_edit.apply(this, arguments);
//post_id = parseInt(this.getId(post_id));
        // Get post ID if object
        if (typeof post_id === 'object') {
            post_id = parseInt(this.getId(post_id));
        }

        

        if (post_id > 0) {
            const edit_row = document.getElementById('edit-' + post_id);
            const post_row = document.getElementById('post-' + post_id);

            
            const bouting_el = post_row.querySelector('.bouting_status');
            const moonlighting_el = post_row.querySelector('.moonlighting_status');

            const bouting_status = bouting_el ? bouting_el.textContent.trim().toLowerCase() : '';
            const moonlighting_status = moonlighting_el ? moonlighting_el.textContent.trim().toLowerCase() : '';

            

            const bouting_select = edit_row.querySelector('select[name="bouting_status"]');
            const moonlighting_select = edit_row.querySelector('select[name="moonlighting_status"]');

            if (bouting_select) bouting_select.value = bouting_status;
            if (moonlighting_select) moonlighting_select.value = moonlighting_status;
        }
    };

    document.getElementById('bulk_edit').addEventListener('click', function (event) {
    const bulkRow = document.getElementById('bulk-edit');

    // Collect post IDs
    const postIds = [];
    bulkRow.querySelectorAll('#bulk-titles-list .ntdelbutton').forEach(function (el) {
        const id = el.getAttribute('id').replace(/^_/, '');
        postIds.push(parseInt(id));
    });

    // Get selected values from bulk edit dropdowns
    const boutingStatus = bulkRow.querySelector('select[name="bouting_status"]')?.value || '';
    const moonlightingStatus = bulkRow.querySelector('select[name="moonlighting_status"]')?.value || '';

    // Send AJAX request
    fetch(ajaxurl, {
        method: 'POST',
        credentials: 'same-origin',
        headers: { 'Content-Type': 'application/x-www-form-urlencoded' },
        body: new URLSearchParams({
            action: 'save_skater_bulk_edit',
            post_ids: JSON.stringify(postIds),
            bouting_status: boutingStatus,
            moonlighting_status: moonlightingStatus,
            _ajax_nonce: skater_bulk_edit.nonce // You need to localize this via wp_localize_script
        })
    }).then(response => response.text()).then(console.log).catch(console.error);
});


});
