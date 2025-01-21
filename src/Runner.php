<?php

namespace chsxf\FolderWatcher;

final class Runner
{
    private readonly Crawler $crawler;
    private int $microsecondsRefreshInterval;
    private array $responders = [];

    public function __construct(string $rootPath, private array $excludedRegularExpressions = [], int $msRefreshInterval = 50)
    {
        $this->crawler = new Crawler($rootPath);
        $this->microsecondsRefreshInterval = $msRefreshInterval;
    }

    public function addResponder(IWatchResponder $watchResponder)
    {
        if (!in_array($watchResponder, $this->responders, true)) {
            $this->responders[] = $watchResponder;
        }
    }

    public function removeResponder(IWatchResponder $watchResponder)
    {
        $indexOf = array_search($watchResponder, $this->responders, true);
        if ($indexOf !== false) {
            array_splice($this->responders, $indexOf, 1);
        }
    }

    public function watch(IWatchResponder $watchResponder) {}
}
