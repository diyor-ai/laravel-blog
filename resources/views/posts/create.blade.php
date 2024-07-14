<x-layouts.main>
    <x-slot:title>
        Post Yaratish
    </x-slot:title>

    <x-page-header>
        Yangi Post Yaratish
    </x-page-header>

    <div class="container">
        <div class="w-60 py-5">
            <div class="contact-form" style="padding-left: 10%">
                <div id="success"></div>

                {{-- @if ($errors->any())
                    <div class="alert alert-danger">
                        <ul>
                            @foreach ($errors->all() as $error)
                                <li>{{ $error }}</li>
                            @endforeach
                        </ul>
                    </div>
                @endif --}}

                <form action="{{ route('posts.store') }}" method="POST" enctype="multipart/form-data">
                    @csrf

                    <div class="control-group mb-4">
                        <input type="text" class="form-control p-4" id="subject" placeholder="Sarlavha"
                            name="title" value="{{ old('title') }}" />
                        @error('title')
                            <div class="alert text-danger">{{ $message }}</div>
                        @enderror
                    </div>

                    <div class="control-group mb-4">
                        <textarea class="form-control p-4" rows="3" name="short_content" id="message" placeholder="Qisqacha Mazmuni">{{ old('short_content') }}</textarea>
                        @error('short_content')
                            <div class="alert text-danger">{{ $message }}</div>
                        @enderror
                    </div>

                    <div class="control-group mb-4">
                        <textarea class="form-control p-4" rows="6" id="message" name="content" placeholder="Maqola">{{ old('content') }}</textarea>
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

                    <div>
                        <button class="btn btn-primary btn-block py-3 px-5" type="submit">Saqlash</button>
                    </div>
                </form>
            </div>
        </div>

    </div>
    </div>


</x-layouts.main>
