<x-layouts.main>
    <x-slot:title>
        Postni O'zgartirish #{{ $post->id }}
    </x-slot:title>

    <x-page-header>
        Postni O'zgartirish #{{ $post->id }}
    </x-page-header>

    <div class="container">
        <div class="w-60 py-5">
            <div class="contact-form" style="padding-left: 10%">
                <div id="success"></div>
                <form action="{{ route('posts.update',['post' => $post->id]) }}" method="POST"
                    enctype="multipart/form-data">
                    @method('PUT')
                    @csrf


                    <div class="control-group mb-4">
                        <input type="text" class="form-control p-4" id="subject" placeholder="Sarlavha" name="title"
                            value="{{ $post->title}}" />
                        @error('title')
                        <div class="alert text-danger">{{ $message }}</div>
                        @enderror
                    </div>

                    <div class="control-group mb-4">
                        <textarea class="form-control p-4" rows="3" name="short_content" id="message"
                            placeholder="Qisqacha Mazmuni">{{ $post->short_content }}</textarea>
                        @error('short_content')
                        <div class="alert text-danger">{{ $message }}</div>
                        @enderror
                    </div>

                    <div class="control-group mb-4">
                        <textarea class="form-control p-4" rows="6" id="message" name="content"
                            placeholder="Maqola">{{ $post->content}}</textarea>
                        @error('content')
                        <div class="alert text-danger">{{ $message }}</div>
                        @enderror
                    </div>

                    <div class="control-group mb-4">
                        <input type="file" name="photo" class="form-control" placeholder="Rasm" />

                        @error('photo')
                        <div class="alert text-danger">{{ $message }}</div>
                        @enderror

                    </div>

                    <div class="flex">
                        <button class="btn btn-success py-3 px-5" type="submit">Saqlash</button>

                        <a class="btn btn-danger py-3 px-5" style="margin-left: 55%"
                            href="{{ route('posts.show', [$post->id]) }}">
                            Bekor Qilish
                        </a>
                    </div>
                </form>
            </div>
        </div>

    </div>

</x-layouts.main>