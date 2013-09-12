<?php
/**
 * Mutagenesis
 *
 * LICENSE
 *
 * This source file is subject to the new BSD license that is bundled
 * with this package in the file LICENSE.txt.
 * It is also available through the world-wide-web at this URL:
 * http://github.com/padraic/mutateme/blob/rewrite/LICENSE
 * If you did not receive a copy of the license and are unable to
 * obtain it through the world-wide-web, please send an email
 * to padraic@php.net so we can send you a copy immediately.
 *
 * @category   Mutagenesis
 * @package    Mutagenesis
 * @subpackage UnitTests
 * @copyright  Copyright (c) 2010 Pádraic Brady (http://blog.astrumfutura.com)
 * @license    http://github.com/padraic/mutateme/blob/rewrite/LICENSE New BSD License
 */

namespace Mutagenesis\Mutation;

use Mutagenesis\Utility\Diff;

abstract class MutationAbstract
{

    /**
     * Array of original source code tokens prior to mutation
     *
     * @var array
     */
    protected $_tokensOriginal = array();

    /**
     * Array of source code tokens after a mutation has been applied
     *
     * @var array
     */
    protected $_tokensMutated = array();

    /**
     * Name and relative path of the file being mutated
     *
     * @var string
     */
    protected $_filename;

    /**
     * Diff provider instance.
     *
     * @var Diff\ProviderInterface
     */
    protected $_diffProvider;

    /**
     * Constructor; sets name and relative path of the file being mutated
     *
     * @param string $filename
     */
    public function __construct($filename = '')
    {
        $this->_filename = $filename;
    }

    /**
     * Return the diff provider.
     *
     * @return Diff\ProviderInterface
     */
    public function getDiffProvider()
    {
        if (!$this->_diffProvider) {
            $this->_diffProvider = new Diff\PhpUnit();
        }
        return $this->_diffProvider;
    }

    /**
     * Set the diff provider.
     *
     * @param Diff\ProviderInterface $provider
     * @return $this
     */
    public function setDiffProvider(Diff\ProviderInterface $provider)
    {
        $this->_diffProvider = $provider;
        return $this;
    }

    /**
     * Perform a mutation against the given original source code tokens for
     * a mutable element
     *
     * @param array $tokens
     * @param int $index
     * @return string
     */
    public function mutate($tokens, $index)
    {
        $this->_tokensOriginal = $tokens;
        $this->_tokensMutated = $this->getMutation($this->_tokensOriginal, $index);
        return $this->_reconstructFromTokens($this->_tokensMutated);
    }

    /**
     * Return the file path of the file which is currently being assessed for
     * mutations.
     *
     * @return string
     */
    public function getFilename()
    {
        return $this->_filename;
    }

    /**
     * Calculate the unified diff between the original source code and its
     * its mutated form
     *
     * @return string
     */
    public function getDiff()
    {
        $original = $this->_reconstructFromTokens($this->_tokensOriginal);
        $mutated = $this->_reconstructFromTokens($this->_tokensMutated);
        $difference = $this->getDiffProvider()->difference($original, $mutated);
        return $difference;
    }

    /**
     * Get a new mutation as an array of changed tokens
     *
     * @param array $tokens
     * @param int $index
     * @return array
     */
    abstract public function getMutation(array $tokens, $index);

    /**
     * Reconstruct a new mutation into a source code string based on the
     * returned tokens
     *
     * @param array $tokens
     * @return string
     */
    protected function _reconstructFromTokens(array $tokens)
    {
        $str = '';
        foreach ($tokens as $token) {
            if (is_string($token)) {
                $str .= $token;
            } else {
                $str .= $token[1];
            }
        }
        return $str;
    }

}
