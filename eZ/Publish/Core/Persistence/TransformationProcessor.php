<?php
/**
 * File containing the TransformationProcessor abstract class
 *
 * @copyright Copyright (C) 1999-2014 eZ Systems AS. All rights reserved.
 * @license http://www.gnu.org/licenses/gpl-2.0.txt GNU General Public License v2
 * @version //autogentag//
 */

namespace eZ\Publish\Core\Persistence;

use eZ\Publish\Core\Persistence\TransformationProcessor\PcreCompiler;

/**
 * Interface for processing a set of transformations on a string
 */
abstract class TransformationProcessor
{
    const T_COMMENT = 1;
    const T_WHITESPACE = 2;
    const T_SECTION = 10;
    const T_MAP = 11;
    const T_REPLACE = 12;
    const T_TRANSPOSE = 13;
    const T_TRANSPOSE_MODULO = 14;

    /**
     * Parsed rule files
     *
     * @var array
     */
    protected $ruleFiles = array();

    /**
     * Compiled rules, which can directly be applied to the input strings
     *
     * @var array
     */
    protected $compiledRules = null;

    /**
     * Transformation compiler
     *
     * @var \eZ\Publish\Core\Persistence\TransformationProcessor\PcreCompiler
     */
    protected $compiler = null;

    /**
     * Construct instance of TransformationProcessor
     *
     * Through the $ruleFiles array, a list of files with full text
     * transformation rules is given.
     *
     * @param \eZ\Publish\Core\Persistence\TransformationProcessor\PcreCompiler $compiler
     * @param array $ruleFiles
     */
    public function __construct( PcreCompiler $compiler, array $ruleFiles = array() )
    {
        $this->ruleFiles = $ruleFiles;
        $this->compiler = $compiler;
    }

    /**
     * Loads rules
     *
     * @return array
     */
    abstract protected function getRules();

    /**
     * Transform the given string
     *
     * Transform the given string using the given rules. If no rules are
     * specified, all available rules will be used for the transformation.
     *
     * @param string $string
     * @param array $ruleNames
     *
     * @return string
     */
    public function transform( $string, array $ruleNames = array() )
    {
        $rules = $this->getRules();

        foreach ( $ruleNames ?: array_keys( $rules ) as $ruleName )
        {
            if ( !isset( $rules[$ruleName] ) )
            {
                // Just continue on unknown rules, or should we throw an error
                // here?
                continue;
            }

            foreach ( $rules[$ruleName] as $rule )
            {
                $string = preg_replace_callback(
                    $rule['regexp'],
                    $rule['callback'],
                    $string
                );
            }
        }

        return $string;
    }

    /**
     * Transform the given string by group
     *
     * Transform the given string using a rule group.
     *
     * @param string $string
     * @param string $ruleGroup
     *
     * @return string
     */
    public function transformByGroup( $string, $ruleGroup )
    {
        $rules = $this->getRules();

        foreach ( array_keys( $rules ) as $ruleName )
        {
            if ( strpos( $ruleName, $ruleGroup ) === false )
            {
                continue;
            }

            foreach ( $rules[$ruleName] as $rule )
            {
                $string = preg_replace_callback(
                    $rule['regexp'],
                    $rule['callback'],
                    $string
                );
            }
        }

        return $string;
    }
}
