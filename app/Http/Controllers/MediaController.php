<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Symfony\Component\HttpFoundation\Response;

/** Serves files stored by DatabaseFilesystemAdapter (PUBLIC_DISK_DRIVER=database), with caching and Range support for video. */
class MediaController extends Controller
{
    public function show(Request $request, string $path): Response
    {
        $meta = DB::table('media_files')->where('path', $path)->first(['path', 'mime', 'size', 'updated_at']);
        abort_unless($meta, 404);

        $etag = '"'.md5($meta->path.'|'.$meta->size.'|'.$meta->updated_at).'"';
        $headers = [
            'Content-Type' => $meta->mime,
            'ETag' => $etag,
            'Accept-Ranges' => 'bytes',
            'Cache-Control' => 'public, max-age=86400',
            'X-Content-Type-Options' => 'nosniff',
            'Cross-Origin-Resource-Policy' => 'cross-origin',
            'Access-Control-Allow-Origin' => '*',   // public files; lets the CMS form read them for its preview
            'Access-Control-Expose-Headers' => 'Content-Length, Content-Range, Accept-Ranges',
        ];
        if ($meta->mime === 'image/svg+xml') {
            $headers['Content-Security-Policy'] = "default-src 'none'; style-src 'unsafe-inline'; sandbox";
        }

        if ($request->headers->get('If-None-Match') === $etag) {
            return response('', 304, $headers);
        }

        $data = base64_decode((string) DB::table('media_files')->where('path', $path)->value('contents'));
        $length = strlen($data);

        // Video players ask for byte ranges; answer them so playback and seeking work.
        if (preg_match('/^bytes=(\d*)-(\d*)$/', (string) $request->header('Range'), $m) && ($m[1] !== '' || $m[2] !== '')) {
            $start = $m[1] === '' ? max(0, $length - (int) $m[2]) : (int) $m[1];
            $end = ($m[1] === '' || $m[2] === '') ? $length - 1 : min((int) $m[2], $length - 1);
            if ($start > $end || $start >= $length) {
                return response('', 416, $headers + ['Content-Range' => "bytes */{$length}"]);
            }

            return response(substr($data, $start, $end - $start + 1), 206, $headers + [
                'Content-Range' => "bytes {$start}-{$end}/{$length}",
                'Content-Length' => $end - $start + 1,
            ]);
        }

        return response($data, 200, $headers + ['Content-Length' => $length]);
    }
}
