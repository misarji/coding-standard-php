<?php
/**
 * A test to ensure that arrows in arrays are set with proper whitespace.
 *
 * @package DWS
 * @subpackage Sniffs
 */

namespace DWS\Sniffs\Arrays;

use PHP_CodeSniffer\Sniffs\Sniff;
use PHP_CodeSniffer\Files\File;

/**
 * A test to ensure that arrows in arrays are set with proper whitespace.
 *
 * @package DWS
 * @subpackage Sniffs
 */
final class ArrowSpacingSniff implements Sniff
{
    /**
     * Returns an array of tokens this test wants to listen for.
     *
     * @return array
     */
    public function register()
    {
        return [T_DOUBLE_ARROW];
    }

    /**
     * Processes this sniff, when one of its tokens is encountered.
     *
     * @param PHP_CodeSniffer\Files\File $phpcsFile The current file being checked.
     * @param int $stackPtr The position of the current token in the stack passed in $tokens.
     *
     * @return void
     */
    public function process(File $phpcsFile, $stackPtr)
    {
        $tokens = $phpcsFile->getTokens();

        $beforeToken = $tokens[$stackPtr - 1];
        if ($beforeToken['code'] !== T_WHITESPACE) {
            $phpcsFile->addError('Expected 1 space before =>, 0 found', $stackPtr, 'SpaceBeforeArrow');
        } elseif ($beforeToken['content'] !== ' ') {
            $phpcsFile->addError('Expected 1 space before =>, %s found', $stackPtr, 'SpaceBeforeArrow', [strlen($beforeToken['content'])]);
        }

        $afterToken = $tokens[$stackPtr + 1];
        if ($afterToken['code'] !== T_WHITESPACE) {
            $phpcsFile->addError('Expected 1 space after =>, 0 found', $stackPtr, 'SpaceAfterArrow');
        } elseif ($afterToken['content'] !== ' ') {
            $phpcsFile->addError('Expected 1 space after =>, %s found', $stackPtr, 'SpaceAfterArrow', [strlen($afterToken['content'])]);
        }
    }
}
