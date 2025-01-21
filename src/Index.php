<?php

namespace chsxf\FolderWatcher;

final class Index
{
    private array $itemMap = [];
    private float $lastRefreshTime;

    public function __construct(public readonly string $path) {}

    public function initializeItems(): bool
    {
        $folderContents = $this->getFolderContents();
        if ($folderContents === false) {
            return false;
        }

        foreach ($folderContents as $folderItem) {
            $fullPath = "{$this->path}/{$folderItem}";
            if (!$this->initializeItem($folderItem, $fullPath)) {
                return false;
            }
        }

        $this->lastRefreshTime = microtime(true);
        return true;
    }

    private function initializeItem(string $itemName, string $fullPath): Index|float|false
    {
        if (is_dir($fullPath)) {
            $subFolderIndex = new Index($fullPath);
            if (!$subFolderIndex->initializeItems()) {
                return false;
            }
            $this->itemMap[$itemName] = $subFolderIndex;
        } else {
            if (($itemTime = filemtime($fullPath)) === false) {
                return false;
            }
            $this->itemMap[$itemName] = $itemTime;
        }
        return $this->itemMap[$itemName];
    }

    public function refresh(): array|false
    {
        $folderContents = $this->getFolderContents();
        if ($folderContents === false) {
            return false;
        }

        $watchChanges = [];

        foreach ($this->itemMap as $folderItem => $itemTime) {
            if (!in_array($folderItem, $folderContents)) {
                $isDir = $this->itemMap[$folderItem] instanceof Index;
                $watchChanges[] = new WatchChange($isDir, $folderItem, -1, WatchChangeType::removed);
                unset($this->itemMap[$folderItem]);
            }
        }

        foreach ($folderContents as $folderItem) {
            $fullPath = "{$this->path}/{$folderItem}";
            $isDir = is_dir($fullPath);

            $itemExistedBefore = array_key_exists($folderItem, $this->itemMap);
            if (!$itemExistedBefore) {
                if (!$this->initializeItem($folderItem, $fullPath)) {
                    return false;
                }
                $watchChanges[] = new WatchChange($isDir, $fullPath, $this->getItemTime($folderItem), WatchChangeType::added);
            } else {
                $wasADirectory = $this->itemMap[$folderItem] instanceof Index;
                if ($wasADirectory != $isDir) {
                    if (!$this->initializeItem($folderItem, $fullPath)) {
                        return false;
                    }
                    $watchChanges[] = new WatchChange($isDir, $fullPath, $this->getItemTime($folderItem), WatchChangeType::modified);
                } else if ($isDir) {
                    if (($subChanges = $this->itemMap[$folderItem]->refresh()) === false) {
                        return false;
                    }
                    $watchChanges = array_merge($watchChanges, $subChanges);
                } else {
                    if (($fileModificationTime = filemtime($fullPath)) === false) {
                        return false;
                    }
                    if ($fileModificationTime != $this->getItemTime($folderItem)) {
                        $this->itemMap[$folderItem] = $fileModificationTime;
                        $watchChanges[] = new WatchChange(false, $fullPath, $fileModificationTime, WatchChangeType::modified);
                    }
                }
            }
        }

        return $watchChanges;
    }

    private function getItemTime(string $itemName): float
    {
        $time = $this->itemMap[$itemName];
        if ($time instanceof Index) {
            $time = $time->lastRefreshTime;
        }
        return $time;
    }

    private function getFolderContents(): array|false
    {
        $folderContents = scandir($this->path, SCANDIR_SORT_NONE);
        if ($folderContents === false) {
            return false;
        }
        return array_filter($folderContents, fn($item) => !preg_match('/^\.{1,2}$/', $item));
    }
}
