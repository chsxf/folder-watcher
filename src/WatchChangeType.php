<?php

namespace chsxf\FolderWatcher;

enum WatchChangeType
{
    case added;
    case modified;
    case removed;
}
