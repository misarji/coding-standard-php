<?php
/**
 * Verifies that all variables are declared in the proper scope.
 *
 * @package DWS
 * @subpackage Sniffs
 */

namespace DWS\Sniffs\Scope;

use PHP_CodeSniffer\Sniffs\AbstractVariableSniff;
use PHP_CodeSniffer\Files\File;
use PHP_CodeSniffer\Util\Tokens;

/**
 * Verifies that all variables are declared in the proper scope.
 *
 * @package DWS
 * @subpackage Sniffs
 */
final class VariableScopeSniff extends AbstractVariableSniff
{
    /**
     * This stores the first scope level that a variable is encountered
     */
    private $_variableScopes = [];

    /**
     * Processes normal variables.
     *
     * @param PHP_CodeSniffer\Files\File $phpcsFile The file where this token was found.
     * @param int $stackPtr The position where the token was found.
     *
     * @return void
     */
    protected function processVariable(File $phpcsFile, $stackPtr)
    {
        $tokens = $phpcsFile->getTokens();
        $variableName = $tokens[$stackPtr]['content'];
        $scopeIdentifier = $phpcsFile->getFilename() . $variableName;
        $level = $tokens[$stackPtr]['level'];
        $functionIndex = $phpcsFile->findPrevious(T_FUNCTION, $stackPtr);
        $lastScopeOpen = $phpcsFile->findPrevious(Tokens::$scopeOpeners, $stackPtr);

        //Inline scope openers do not increment the level value
        $scopeOpenDistance = $tokens[$stackPtr]['line'] - $tokens[$lastScopeOpen]['line'];
        if (
            in_array($tokens[$lastScopeOpen]['code'], Tokens::$scopeOpeners) === true
            && ($scopeOpenDistance === 1 || $scopeOpenDistance === 0)//Include the variables in the condition
            && $tokens[$stackPtr]['level'] === $tokens[$lastScopeOpen]['level']
        ) {
            ++$level;
        }

        if (
            $functionIndex !== false
            && array_key_exists('scope_closer', $tokens[$functionIndex])
            && $tokens[$functionIndex]['scope_closer'] > $stackPtr
        ) {
            //Member variables are always ok
            if ($variableName === '$this') {
                return;
            }

            // find previous non-whitespace token. if it's a double colon, assume static class var
            $objOperator = $phpcsFile->findPrevious([T_WHITESPACE], ($stackPtr - 1), null, true);
            if ($tokens[$objOperator]['code'] === T_DOUBLE_COLON) {
                return;
            }

            $scopeIdentifier .= $tokens[$functionIndex]['scope_condition'];
        }

        //If this is the first time we've seen this variable in this file/function store the scope depth.
        if (array_key_exists($scopeIdentifier, $this->_variableScopes) === false) {
            $this->_variableScopes[$scopeIdentifier] = $level;
        } elseif ($this->_variableScopes[$scopeIdentifier] > $level) {
            //Verify that the variables we've seen are not appearing in higher scopes.
            $phpcsFile->addWarning("Variable '{$variableName}' is in the wrong scope.", $stackPtr, 'Found');
        }
    }

    /**
     * Processes the function tokens within the class.
     *
     * @param PHP_CodeSniffer\Files\File $phpcsFile The file where this token was found.
     * @param int $stackPtr The position where the token was found.
     *
     * @return void
     */
    protected function processMemberVar(File $phpcsFile, $stackPtr)
    {
        //Do Nothing
    }

    /**
     * Processes variables in double quoted strings.
     *
     * @param PHP_CodeSniffer\Files\File $phpcsFile The file where this token was found.
     * @param int $stackPtr The position where the token was found.
     *
     * @return void
     */
    protected function processVariableInString(File $phpcsFile, $stackPtr)
    {
        //Do Nothing
    }
}
