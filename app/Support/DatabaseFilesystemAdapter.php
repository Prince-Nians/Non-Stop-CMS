<?php

namespace App\Support;

use Illuminate\Support\Facades\DB;
use League\Flysystem\Config;
use League\Flysystem\DirectoryAttributes;
use League\Flysystem\FileAttributes;
use League\Flysystem\FilesystemAdapter;
use League\Flysystem\UnableToCopyFile;
use League\Flysystem\UnableToMoveFile;
use League\Flysystem\UnableToReadFile;
use League\Flysystem\UnableToRetrieveMetadata;
use League\MimeTypeDetection\ExtensionMimeTypeDetector;
use League\MimeTypeDetection\FinfoMimeTypeDetector;

/**
 * Stores uploaded files in the `media_files` table (base64 text), so a demo needs no bucket or shared disk:
 * the CMS writes to the shared database and the backend serves the files from it (see MediaController).
 * Meant for demos and small sites; use PUBLIC_DISK_DRIVER=s3 for real traffic.
 */
class DatabaseFilesystemAdapter implements FilesystemAdapter
{
    public function __construct(private string $baseUrl = '', private string $table = 'media_files') {}

    private function db()
    {
        return DB::table($this->table);
    }

    private function norm(string $path): string
    {
        return ltrim(str_replace('\\', '/', $path), '/');
    }

    private function row(string $path, array $columns = ['path', 'mime', 'size', 'updated_at'])
    {
        return $this->db()->where('path', $this->norm($path))->first($columns);
    }

    public function getUrl(string $path): string
    {
        $segments = array_map('rawurlencode', explode('/', $this->norm($path)));

        return rtrim($this->baseUrl, '/').'/'.implode('/', $segments);
    }

    public function fileExists(string $path): bool
    {
        return $this->db()->where('path', $this->norm($path))->exists();
    }

    public function directoryExists(string $path): bool
    {
        $prefix = rtrim($this->norm($path), '/').'/';

        return $this->db()->where('path', 'like', str_replace(['%', '_'], ['\\%', '\\_'], $prefix).'%')->exists();
    }

    public function write(string $path, string $contents, Config $config): void
    {
        $path = $this->norm($path);
        $mime = (new ExtensionMimeTypeDetector())->detectMimeTypeFromPath($path)
            ?? (new FinfoMimeTypeDetector())->detectMimeTypeFromBuffer($contents)
            ?? 'application/octet-stream';
        $now = now();

        $this->db()->upsert(
            [['path' => $path, 'mime' => $mime, 'size' => strlen($contents), 'contents' => base64_encode($contents), 'created_at' => $now, 'updated_at' => $now]],
            ['path'],
            ['mime', 'size', 'contents', 'updated_at'],
        );
    }

    public function writeStream(string $path, $contents, Config $config): void
    {
        $this->write($path, (string) stream_get_contents($contents), $config);
    }

    public function read(string $path): string
    {
        $encoded = $this->db()->where('path', $this->norm($path))->value('contents');
        if ($encoded === null) {
            throw UnableToReadFile::fromLocation($path, 'File not found.');
        }

        return base64_decode($encoded);
    }

    public function readStream(string $path)
    {
        $stream = fopen('php://temp', 'r+');
        fwrite($stream, $this->read($path));
        rewind($stream);

        return $stream;
    }

    public function delete(string $path): void
    {
        $this->db()->where('path', $this->norm($path))->delete();
    }

    public function deleteDirectory(string $path): void
    {
        $prefix = rtrim($this->norm($path), '/').'/';
        $this->db()->where('path', 'like', str_replace(['%', '_'], ['\\%', '\\_'], $prefix).'%')->delete();
    }

    public function createDirectory(string $path, Config $config): void
    {
        // Directories are implicit (they are just path prefixes).
    }

    public function setVisibility(string $path, string $visibility): void
    {
        // Everything is served by MediaController; there is no per-file visibility.
    }

    public function visibility(string $path): FileAttributes
    {
        $this->requireRow($path, 'visibility');

        return new FileAttributes($this->norm($path), null, 'public');
    }

    public function mimeType(string $path): FileAttributes
    {
        return new FileAttributes($this->norm($path), null, null, null, $this->requireRow($path, 'mime_type')->mime);
    }

    public function lastModified(string $path): FileAttributes
    {
        return new FileAttributes($this->norm($path), null, null, strtotime($this->requireRow($path, 'last_modified')->updated_at));
    }

    public function fileSize(string $path): FileAttributes
    {
        return new FileAttributes($this->norm($path), (int) $this->requireRow($path, 'file_size')->size);
    }

    private function requireRow(string $path, string $attribute)
    {
        $row = $this->row($path);
        if (! $row) {
            throw UnableToRetrieveMetadata::create($path, $attribute, 'File not found.');
        }

        return $row;
    }

    public function listContents(string $path, bool $deep): iterable
    {
        $prefix = trim($this->norm($path), '/');
        $prefix = $prefix === '' ? '' : $prefix.'/';
        $query = $this->db()->select(['path', 'size', 'updated_at', 'mime']);
        if ($prefix !== '') {
            $query->where('path', 'like', str_replace(['%', '_'], ['\\%', '\\_'], $prefix).'%');
        }

        $dirs = [];
        foreach ($query->orderBy('path')->get() as $row) {
            $rest = substr($row->path, strlen($prefix));
            if (! $deep && str_contains($rest, '/')) {
                $dir = $prefix.strtok($rest, '/');
                if (! isset($dirs[$dir])) {
                    $dirs[$dir] = true;
                    yield new DirectoryAttributes($dir);
                }
                continue;
            }
            yield new FileAttributes($row->path, (int) $row->size, 'public', strtotime($row->updated_at), $row->mime);
        }
    }

    public function move(string $source, string $destination, Config $config): void
    {
        if (! $this->fileExists($source)) {
            throw UnableToMoveFile::fromLocationTo($source, $destination);
        }
        $this->db()->where('path', $this->norm($destination))->delete();
        $this->db()->where('path', $this->norm($source))->update(['path' => $this->norm($destination), 'updated_at' => now()]);
    }

    public function copy(string $source, string $destination, Config $config): void
    {
        if (! $this->fileExists($source)) {
            throw UnableToCopyFile::fromLocationTo($source, $destination);
        }
        $this->write($destination, $this->read($source), $config);
    }
}
