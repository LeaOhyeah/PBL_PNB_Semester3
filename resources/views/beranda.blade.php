<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Beranda</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha1/dist/css/bootstrap.min.css" rel="stylesheet">
</head>

<body>

    <div class="container">
        <h1 class="mt-5">For You</h1>

        <div class="row">
            @if ($fyp->isEmpty())
                <p>No fyp available.</p>
            @else
                @foreach ($fyp as $f)
                    <div class="col-md-4 mb-4">
                        <div class="card h-100">
                            <div class="card-body">
                                <h5 class="card-title">{{ $f->content }}</h5>
                                <p class="card-text">{{ $f->user->name }}</p>
                                <p class="card-text">{{ Str::limit($f->description, 100) }}</p>
                                <a href="#" class="btn btn-primary">Read More</a>
                            </div>
                        </div>
                    </div>
                @endforeach
            @endif
        </div>

        <h1 class="mt-5">News</h1>
        <div class="row">
            @if ($news->isEmpty())
                <p>No news available.</p>
            @else
                @foreach ($news as $n)
                    <div class="col-md-4 mb-4">
                        <div class="card h-100">
                            <div class="card-body">
                                <h5 class="card-title">{{ $n->content }}</h5>
                                {{-- <a href="#" class="btn btn-primary">Read More</a> --}}
                            </div>
                        </div>
                    </div>
                @endforeach
            @endif
        </div>

        <h1 class="mt-5">Key</h1>
        <div class="row">
            @if (isset($keywords[0]))
                <p>No keywords available.</p>
            @else
                @foreach ($keywords as $keyword)
                    <div class="col-md-4 mb-4">
                        <div class="card h-100">
                            <div class="card-body">
                                <h5 class="card-title">{{ $keyword }}</h5>
                                {{-- <a href="#" class="btn btn-primary">Read More</a> --}}
                            </div>
                        </div>
                    </div>
                @endforeach
            @endif
        </div>
    </div>




    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha1/dist/js/bootstrap.bundle.min.js"></script>
</body>

</html>
