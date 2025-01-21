<?php

namespace chsxf\FolderWatcher;

final class Runner
{
    private readonly array $rootIndices;
    private int $microsecondsRefreshInterval;
    private array $responders = [];

    public function __construct(array $rootPaths, int $msRefreshInterval = 50)
    {
        $indices = [];
        foreach ($rootPaths as $rootPath) {
            $indices[] = new Index($rootPath);
        }
        $this->rootIndices = $indices;
        $this->microsecondsRefreshInterval = $msRefreshInterval * 1_000;
    }

    public function addResponder(IWatchResponder|callable $watchResponder)
    {
        if (!in_array($watchResponder, $this->responders, true)) {
            $this->responders[] = $watchResponder;
        }
    }

    public function removeResponder(IWatchResponder|callable $watchResponder)
    {
        $indexOf = array_search($watchResponder, $this->responders, true);
        if ($indexOf !== false) {
            array_splice($this->responders, $indexOf, 1);
        }
    }

    public function watch(): false
    {
        foreach ($this->rootIndices as $index) {
            if (!$index->initializeItems()) {
                return false;
            }
        }

        $startNotified = false;

        while (true) {
            if (!$startNotified) {
                foreach ($this->responders as $responder) {
                    if ($responder instanceof IWatchResponder) {
                        $responder->notifyStartWatching();
                    }
                }
                $startNotified = true;
            }

            if ($this->microsecondsRefreshInterval > 1_000_000) {
                sleep($this->microsecondsRefreshInterval / 1_000_000);
            } else {
                usleep($this->microsecondsRefreshInterval);
            }

            $changes = [];
            foreach ($this->rootIndices as $index) {
                $indexChanges = $index->refresh();
                if ($indexChanges === false) {
                    return false;
                }
                $changes = array_merge($changes, $indexChanges);
            }
            if (!empty($changes)) {
                foreach ($this->responders as $responder) {
                    if ($responder instanceof IWatchResponder) {
                        $responder->processChanges($changes);
                    } else {
                        $responder($changes);
                    }
                }
                $startNotified = false;
            }
        }

        return false;
    }
}
