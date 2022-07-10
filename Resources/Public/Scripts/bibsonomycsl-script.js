function toggleSnippet(snippetBtn) {
    let btn = $(snippetBtn);
    let postId = '#' + btn.data('post');
    let snippetId = '#' + btn.data('snippet');
    $(postId + ' .bibsonomy-snippet:not(' + snippetId + ')').hide(0);
    $(snippetId).toggle(0);
}

function filterPosts() {
    let input = $('#bibsonomyInlineFilter');
    let search = input.val();

    if (search) {
        $('.bibsonomy-group-header').hide(0);
        $('.bibsonomy-post').each(function(index) {
            let postTxt = $(this).text().toLowerCase();
            if (postTxt.includes(search.toLowerCase())) {
                $(this).show(0);
            } else {
                $(this).hide(0);
            }
        });
    } else {
        $('.bibsonomy-group-header').show(0);
        $('.bibsonomy-post').show(0);
    }
}

function resetFilterPosts() {
    $('#bibsonomyInlineFilter').val('');
    $('.bibsonomy-group-header').show(0);
    $('.bibsonomy-post').show(0);
}