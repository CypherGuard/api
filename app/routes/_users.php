<?php

app()->get('/users', function () {
    response()->json(['message' => 'Congrats!! You\'re on Leaf API']);
});

app()->get('/users/{id}', function ($id) {
    response()->json(['message' => 'Congrats!! You\'re on Leaf API']);
});

app()->post('/users', function () {
    response()->json(['message' => 'Congrats!! You\'re on Leaf API']);
});

app()->put('/users/{id}', function ($id) {
    response()->json(['message' => 'Congrats!! You\'re on Leaf API']);
});
