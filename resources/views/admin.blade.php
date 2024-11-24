<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Admin Dashboard</title>
    <!-- Bootstrap CSS -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <script src="https://cdn.tiny.cloud/1/no-api-key/tinymce/6/tinymce.min.js" referrerpolicy="origin"></script>

</head>
<body>
<div class="container mt-5">
    <h1 class="text-center">Admin Dashboard</h1>

    <!-- Create Post Button -->
    <button class="btn btn-primary mb-3" id="createNewPost">Create New Post</button>

    <!-- Post Modal -->
    <div class="modal fade" id="postModal" tabindex="-1" role="dialog" aria-labelledby="postModalLabel" aria-hidden="true">
        <div class="modal-dialog" role="document">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="postModalLabel">Create New Post</h5>
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true">&times;</span>
                    </button>
                </div>
                <div class="modal-body">
                    <form id="postForm">
                        <input type="hidden" id="postId" name="id">
                        <div class="form-group">
                            <label for="name">Post Name</label>
                            <input type="text" class="form-control" id="name" name="name" required>
                        </div>
                        <div class="form-group">
                            <label for="author">Author</label>
                            <input type="text" class="form-control" id="author" name="author" required>
                        </div>
                        <div class="form-group">
                            <label for="date">Post Date</label>
                            <input type="date" class="form-control" id="date" name="date" required>
                        </div>
                        <div class="form-group">
                            <label for="content">Content</label>
                            <textarea class="form-control" id="content" name="content" rows="4" required></textarea>
                        </div>
                        <div class="form-group">
                            <label for="image">Post Image</label>
                            <input type="file" class="form-control" id="image" name="image">
                        </div>
                        <button type="submit" class="btn btn-success">Save Post</button>
                    </form>
                </div>
            </div>
        </div>
    </div>

    <!-- Posts Table -->

</div>

<!-- Bootstrap JS and jQuery -->
<script src="https://cdn.jsdelivr.net/npm/jquery@3.6.0/dist/jquery.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>

<script>
    $(document).ready(function () {
        // Set CSRF Token for AJAX requests
        $.ajaxSetup({
            headers: {
                'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
            }
        });

        // Open the modal to create a new post
        $('#createNewPost').click(function () {
            $('#postForm')[0].reset(); // Clear the form
            $('#postId').val(''); // Reset postId to empty
            $('#postModal').modal('show'); // Show the modal
        });

        // Submit the post form using AJAX
        $('#postForm').submit(function (e) {
            e.preventDefault(); // Prevent form submission
            var formData = new FormData(this); // Gather form data

            var postId = $('#postId').val();
            var url = postId ? '/posts/' + postId : '/posts';
            var method = postId ? 'PUT' : 'POST';

            $.ajax({
                url: url,
                type: method,
                data: formData,
                processData: false,
                contentType: false,
                success: function (response) {
                    $('#postModal').modal('hide'); // Close the modal
                    fetchPosts(); // Refresh the posts list
                    alert(response.success); // Display success message
                },
                error: function (xhr, status, error) {
                    alert('An error occurred: ' + error); // Display error message
                }
            });
        });

        // Fetch posts and display them in the table
        function fetchPosts() {
            $.ajax({
                url: '/posts/fetchAll', // Adjust this URL to match your controller's fetchAll route
                type: 'GET',
                success: function (response) {
                    var posts = response;
                    var html = '';
                    posts.forEach(function (post) {
                        html += `
                            <tr>
                                <td><img src="/storage/${post.image}" width="100" height="100" alt="${post.name}"></td>
                                <td>${post.name}</td>
                                <td>${post.author}</td>
                                <td>${post.date}</td>
                                <td>${post.content.substring(0, 100)}...</td>
                                <td>
                                    <button class="btn btn-warning editPost" data-id="${post.id}">Edit</button>
                                    <button class="btn btn-danger deletePost" data-id="${post.id}">Delete</button>
                                </td>
                            </tr>
                        `;
                    });
                    $('#postsContainer').html(html); // Update the posts table with new rows
                }
            });
        }

        // Edit post - Populate modal with existing post data
        $(document).on('click', '.editPost', function () {
            var postId = $(this).data('id');
            $.get('/posts/' + postId + '/edit', function (response) {
                $('#postId').val(response.id);
                $('#name').val(response.name);
                $('#author').val(response.author);
                $('#date').val(response.date);
                $('#content').val(response.content);
                $('#postModal').modal('show');
            });
        });

        // Delete post
        $(document).on('click', '.deletePost', function () {
            var postId = $(this).data('id');
            if (confirm('Are you sure you want to delete this post?')) {
                $.ajax({
                    url: '/posts/' + postId,
                    type: 'DELETE',
                    success: function (response) {
                        fetchPosts(); // Refresh the posts table
                        alert(response.success); // Show success message
                    }
                });
            }
        });

        // Initial fetch of posts on page load
        fetchPosts();
    });
</script>
</body>
</html>
