<?php

namespace chsxf\FolderWatcher;

enum FileChangeType
{
    case added;
    case modified;
    case removed;
}
