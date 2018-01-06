<!doctype html>
<html>
<head>
    @include('partials.head')
</head>
<body>

@include('partials.sidebar')
@yield('content')
@include('partials.javascript')
@yield('scripts')

</body>
</html>