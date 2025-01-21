<?php

namespace chsxf\FolderWatcher;

interface IWatchResponder
{
    function fileChanged(string $filePath, FileChangeType $changeType);
}
