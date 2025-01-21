<?php

namespace chsxf\FolderWatcher;

final class WatchChange
{
    public function __construct(
        public readonly bool $isFolder,
        public readonly string $path,
        public readonly float $time,
        public readonly WatchChangeType $fileChangeType
    ) {}
}
