<?php
require_once __DIR__ . '/../models/Post.php';

class PostController {
    public function index() {
        $posts = Post::all();
        require __DIR__ . '/../views/posts/index.php';
    }

    public function show($id) {
        $post = Post::find($id);
        require __DIR__ . '/../views/posts/show.php';
    }
} 