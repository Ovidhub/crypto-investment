@php
    /**
     * Reusable responsive video embed.
     * Reads the admin-configured URL from $settings->send_receive_video.
     * Supports YouTube, Vimeo and direct video files (.mp4/.webm/.ogg).
     */
    $rawVideo = trim($settings->send_receive_video ?? '');
    $embedUrl = null;
    $isFile = false;

    if ($rawVideo !== '') {
        // Direct video file
        if (preg_match('/\.(mp4|webm|ogg)(\?.*)?$/i', $rawVideo)) {
            $isFile = true;
            $embedUrl = $rawVideo;
        }
        // YouTube
        elseif (preg_match('~(?:youtube\.com/(?:watch\?v=|embed/|shorts/)|youtu\.be/)([A-Za-z0-9_-]{6,})~i', $rawVideo, $m)) {
            $embedUrl = 'https://www.youtube.com/embed/' . $m[1];
        }
        // Vimeo
        elseif (preg_match('~vimeo\.com/(?:video/)?(\d+)~i', $rawVideo, $m)) {
            $embedUrl = 'https://player.vimeo.com/video/' . $m[1];
        }
        // Fallback: use as-is inside an iframe
        else {
            $embedUrl = $rawVideo;
        }
    }
@endphp

@if($embedUrl)
    <div class="relative w-full overflow-hidden rounded-xl border border-dark-200 dark:border-dark-200 border-light-300 bg-black" style="aspect-ratio:16/9;">
        @if($isFile)
            <video class="absolute inset-0 h-full w-full" controls preload="metadata" playsinline>
                <source src="{{ $embedUrl }}">
                Your browser does not support the video tag.
            </video>
        @else
            <iframe class="absolute inset-0 h-full w-full" src="{{ $embedUrl }}"
                    title="How to send and receive coins" loading="lazy"
                    frameborder="0" allow="accelerometer; autoplay; clipboard-write; encrypted-media; gyroscope; picture-in-picture"
                    allowfullscreen></iframe>
        @endif
    </div>
@else
    <div class="flex flex-col items-center justify-center gap-2 rounded-xl border border-dashed border-dark-200 dark:border-dark-200 border-light-300 bg-dark-100/40 dark:bg-dark-100/40 bg-light-100 p-10 text-center" style="aspect-ratio:16/9;">
        <i data-lucide="video-off" class="h-8 w-8 text-primary"></i>
        <p class="text-sm font-medium dark:text-white text-dark">Tutorial video coming soon</p>
        <p class="text-xs dark:text-gray-400 text-gray-500">An admin can add it under Settings → Website Information.</p>
    </div>
@endif
