<?php

namespace chsxf\FolderWatcher;

final class WatchResponse
{
    public function __construct(public readonly string $path, public readonly float $time, public readonly FileChangeType $fileChangeType) {}
}
