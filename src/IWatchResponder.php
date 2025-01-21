<?php

namespace chsxf\FolderWatcher;

interface IWatchResponder
{
    function notifyStartWatching(): void;
    function processChanges(array $watchChanges): void;
}
