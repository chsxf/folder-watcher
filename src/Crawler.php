<?php

namespace chsxf\FolderWatcher;

final class Crawler
{
    private readonly Index $index;

    public function __construct(string $rootPath)
    {
        $this->index = new Index($rootPath);
    }

    public function crawl() {}
}
