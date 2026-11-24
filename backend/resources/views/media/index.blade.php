<!DOCTYPE html>
<html>
<head>
    <title>My S3 Media Gallery</title>
</head>
<body>

<h2>My Images and Videos</h2>

<form action="{{ url('/media/upload') }}"
      method="POST"
      enctype="multipart/form-data">

    @csrf

    <input type="file"
           name="media"
           accept="image/*,video/*"
           required>

    <button type="submit">Upload Media</button>

</form>

@foreach($mediaFiles as $media)

    <div style="margin:20px;">

        @if(str_starts_with($media->file_type, 'image/'))

            <img src="{{ $media->url }}"
                 width="300"
                 alt="Uploaded image">

        @elseif(str_starts_with($media->file_type, 'video/'))

            <video width="400" controls>
                <source src="{{ $media->url }}"
                        type="{{ $media->file_type }}">
                Your browser does not support video.
            </video>

        @endif

    </div>

@endforeach

</body>
</html>