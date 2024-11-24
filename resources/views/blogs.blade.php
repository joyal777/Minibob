<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>All Posts</title>
    <!-- Bootstrap CSS -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <style>
        .card-img-top {
            object-fit: cover;
            height: 200px;
        }
        .card-body {
            padding: 1rem;
        }
        .card {
            border: 1px solid #ddd;
            box-shadow: 0 4px 8px rgba(0, 0, 0, 0.1);
            transition: transform 0.3s ease-in-out;
        }
        .card:hover {
            transform: translateY(-5px);
            box-shadow: 0 6px 15px rgba(0, 0, 0, 0.2);
        }
        .modal-body {
            overflow-y: auto;
        }
    </style>
</head>
<body>

<div class="container mt-5">
    <div class="mb-5"> <a href="{{ route('admin') }}" class="btn btn-primary">Admin</a></div>
    <h1 class="text-center mb-4">All Blog Posts</h1>


    <div class="row">
        @foreach($posts as $post)
            <div class="col-md-3 mb-4">
                <div class="card">
                    <!-- Check if the image exists, otherwise use a default image -->
                    @if($post->image)
                        <img src="{{ asset($post->image) }}" class="card-img-top" alt="{{ $post->name }}" onerror="this.onerror=null;this.src='{{ asset('images/default-image.jpg') }}';">
                    @else
                        <img src="{{ asset('images/default-image.jpg') }}" class="card-img-top" alt="Default Image">
                    @endif
                    <div class="card-body">
                        <h5 class="card-title">{{ $post->name }}</h5>
                        <p class="card-text"><strong>Author:</strong> {{ $post->author }}</p>
                        <p class="card-text"><strong>Date:</strong> {{ \Carbon\Carbon::parse($post->date)->format('M d, Y') }}</p>
                        <p class="card-text">{{ \Str::limit($post->content, 100) }}</p>
                        <button class="btn btn-primary" data-bs-toggle="modal" data-bs-target="#postModal"
                                data-id="{{ $post->id }}"
                                data-image="{{ asset($post->image) }}"
                                data-name="{{ $post->name }}"
                                data-author="{{ $post->author }}"
                                data-date="{{ \Carbon\Carbon::parse($post->date)->format('M d, Y') }}"
                                data-content="{{ $post->content }}">
                            Details
                        </button>
                    </div>
                </div>
            </div>
        @endforeach
    </div>
</div>

<!-- Modal -->
<div class="modal fade" id="postModal" tabindex="-1" aria-labelledby="postModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="postModalLabel">Post Details</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                <div id="postDetails">
                    <!-- Dynamic content will be loaded here -->
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Bootstrap JS and jQuery -->
<script src="https://cdn.jsdelivr.net/npm/jquery@3.6.0/dist/jquery.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>

<script>
    $(document).ready(function () {
        // On Modal show event, fetch the post data from the button's data attributes
        $('#postModal').on('show.bs.modal', function (e) {
            var button = $(e.relatedTarget); // Get the button that triggered the modal
            var postId = button.data('id');
            var postImage = button.data('image');
            var postName = button.data('name');
            var postAuthor = button.data('author');
            var postDate = button.data('date');
            var postContent = button.data('content');

            // Set the modal content dynamically using the data attributes
            var modalContent = `
                <img src="${postImage}" class="img-fluid mb-4" alt="${postName}">
                <h3>${postName}</h3>
                <p><strong>Author:</strong> ${postAuthor}</p>
                <p><strong>Date:</strong> ${postDate}</p>
                <p><strong>Content:</strong> ${postContent}</p>
            `;
            $('#postDetails').html(modalContent);
        });
    });
</script>

</body>
</html>
