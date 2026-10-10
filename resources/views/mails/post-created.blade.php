<div>
    <h1>Hello {{ $post->user->name }}!</h1>
    <p>Your post was created on {{ $post->created_at }}.</p>
    <h3>{{ $post->title }}</h3>
    <p>{{ $post->short_content }}</p>
    <p><a href="{{ route('posts.show', $post) }}">Read it online</a></p>
    <strong>Thank you!</strong>
</div>
