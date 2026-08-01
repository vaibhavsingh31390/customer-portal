@foreach ($messages as $message)
    @include('complaint._threadMessage', ['message' => $message])
@endforeach
