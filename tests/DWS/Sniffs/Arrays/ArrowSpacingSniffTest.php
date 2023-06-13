<?php

final class DWS_Sniffs_Arrays_ArrowSpacingSniffTest extends AbstractSniffUnitTest
{
    public function getErrorList()
    {
        return [5 => 1, 6 => 1, 7 => 1, 8 => 1, 9 => 1, 10 => 1, 11 => 1, 12 => 1];
    }

    public function getWarningList()
    {
        return [];
    }

    protected function _getSniffName()
    {
        return 'DWS.Arrays.ArrowSpacing';
    }
}
