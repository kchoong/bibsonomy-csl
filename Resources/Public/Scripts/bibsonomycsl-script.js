function toggleSnippet(snippetBtn) {
    let btn = $(snippetBtn);
    let postId = '#' + btn.data('post');
    let snippetId = '#' + btn.data('snippet');
    $(postId + ' .bibsonomy-snippet:not(' + snippetId + ')').hide(0);
    $(snippetId).toggle(0);
}