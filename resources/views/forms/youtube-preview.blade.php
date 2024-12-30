@if ($news = App\Models\News::where('id', $getRecord()->id ?? '')->first())
    @if ($news->content_url) <!-- Pastikan URL video ada -->
        <div style="position: relative; padding-bottom: 240px; ">
            <iframe 
                src="https://www.youtube.com/embed/{{ $news->content_url }}" 
                style="position: absolute; top: 0; left: 0; width: 100%; height: 100%;" 
                frameborder="0" 
                allowfullscreen>
            </iframe>
        </div>
    @else
        <p>URL video tidak tersedia.</p>
    @endif
@else
    <p>Data tidak ditemukan.</p>
@endif
