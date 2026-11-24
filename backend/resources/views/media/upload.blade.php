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