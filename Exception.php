<?php
namespace Snowcap\AdminBundle;

class Exception extends \ErrorException {
    const SECTION_INVALID = 10;
    const SECTION_UNKNOWN = 20;

    const GRID_INVALID = 110;
    const GRID_NOQUERYBUILDER = 120;
    const GRID_INVALIDQUERYBUILDER = 130;
}