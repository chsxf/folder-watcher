<?php

namespace chsxf\FolderWatcher;

final class Index
{
    private array $itemMap = [];
    private float $lastRefreshTime;

    public function __construct(public readonly string $path) {}

    public function refresh(): WatchResponse|null|false {}
}
