window.addEventListener("DOMContentLoaded", function () {

});

function toggleSnippet(btn) {
    let postId = '#' + btn.dataset.post;
    let snippetId = '#' + btn.dataset.snippet;

    // Hide other snippets of the post
    let postSnippets = document.querySelectorAll(postId + ' .bibsonomy-snippet:not(' + snippetId + ')');
    postSnippets.forEach(function(postSnippet) {
        postSnippet.style.display = 'none';
    });

    // Toggle the selected snippet
    let selectedSnippet = document.querySelector(snippetId);
    if (!selectedSnippet.style.display || selectedSnippet.style.display === 'none') {
        selectedSnippet.style.display = 'block';
    } else {
        selectedSnippet.style.display = 'none';
    }
}

function filterPosts() {
    let input = document.querySelector('#bibsonomyInlineFilter');
    let search = input.value;

    if (search) {
        // Hide all headers
        document.querySelectorAll('.bibsonomy-group-header').forEach(function(header) {
            header.style.display = 'none';
        });

        // Only show matching posts
        document.querySelectorAll('.bibsonomy-post').forEach(function(post) {
            let postTxt = post.innerText.toLowerCase();
            if (postTxt.includes(search.toLowerCase())) {
                post.style.display = 'block';
            } else {
                post.style.display = 'none';
            }
        });
    } else {
        resetFilterPosts();
    }
}

function resetFilterPosts() {
    document.querySelector('#bibsonomyInlineFilter').value = '';

    // Show all headers
    document.querySelectorAll('.bibsonomy-group-header').forEach(function(header) {
        header.style.display = 'block';
    });

    // Show all posts
    document.querySelectorAll('.bibsonomy-post').forEach(function(post) {
        post.style.display = 'block';
    });
}