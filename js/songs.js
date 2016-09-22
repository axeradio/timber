jQuery(document).ready(function() {
    jQuery('.editable').editable(ajaxurl + '?action=timber_edit_song', {
        id: 'ID',
        name: 'value'
    });
});
