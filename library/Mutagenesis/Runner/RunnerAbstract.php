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

namespace Mutagenesis\Runner;

abstract class RunnerAbstract
{

    /**
     * Path to the base directory of the project being mutated
     *
     * @var string
     */
    protected $_baseDirectory = '';

    /**
     * Path to the source directory of the project being mutated
     *
     * @var string
     */
    protected $_sourceDirectory = '';

    /**
     * Path to the tests directory of the project being mutated
     *
     * @var string
     */
    protected $_testDirectory = '';

    /**
     * Path to a cache directory used by library to store working files
     * such as PHPUnit logs needed to analyse test case execution times.
     *
     * @var string
     */
    protected $_cacheDirectory = null;

    /**
     * Name of the test adapter, e.g. phpunit, to utilise
     *
     * @var string
     */
    protected $_adapterName = '';

    /**
     * String of adapter options appended to any call to the current adapter's
     * testing utility command (e.g. passing 'AllTests.php' to phpunit)
     *
     * @var string
     */
    protected $_adapterOptions = array();

    /**
     * Adapter constraint. Used in initial run to analyse what tests/cases/suites
     * are to be executed. For example, calling "phpunit MyTests MyTests.php" sets
     * a constraint of "MyTests MyTests.php". Used to narrow the focus of the current
     * mutation testing run if needed (e.g. spreading testing over time)
     *
     * @var string
     */
    protected $_adapterConstraint = '';

    /**
     * Instance of a suitable renderer used to format results into output
     *
     * @var \Mutagenesis\Renderer\Text
     */
    protected $_renderer = null;
    
    /**
     * Name of the renderer, e.g. text, to utilise
     *
     * @var string
     */
    protected $_rendererName = 'Text';

    /**
     * Instance of \Mutagenesis\Runkit used to apply and reverse mutations
     * on loaded source code within the same process dynamically
     *
     * @var \Mutagenesis\Utility\Runkit
     */
    protected $_runkit = null;

    /**
     * Instance of \Mutagenesis\Generator used to generate mutations from the
     * underlying source code
     *
     * @var \Mutagenesis\Generator
     */
    protected $_generator = null;

    /**
     * Instance of \Mutagenesis\Adapter\AdapterAbstract linking Mutagenesis to
     * to an underlying test framework to execute tests and parse the test
     * results
     *
     * @var \Mutagenesis\Adapter\AdapterAbstract
     */
    protected $_adapter = null;

    /**
     * Stored mutables each containing relevant mutations for the source code
     * being tested
     *
     * @var array
     */
    protected $_mutables = array();

    /**
     * Generic options, possibly of future use to other test frameworks
     *
     * @var array
     */
    protected $_options = array();
    
    /**
     * Timeout in seconds allowed per test execution.
     *
     * @var int
     */
    protected $_timeout = 60;
    
    /**
     * Test framework bootstrap
     *
     * @var string
     */
    protected $_bootstrap = null;
    
    /**
     * Flag to add detailed reports (including test results) about
     * the mutations which caused test failures (i.e. captured)
     *
     * @var bool
     */
    protected $_detailCaptures = false;
    
    /**
     * Execute the runner
     *
     * @return void
     */
    abstract public function execute();

    /**
     * Set the base directory of the project being mutated
     *
     * @param string $dir
     * @return $this
     * @throws \Mutagenesis\FUTException
     */
    public function setBaseDirectory($dir)
    {
        $dir = rtrim($dir, ' \\/');
        if (!is_dir($dir) || !is_readable($dir)) {
            throw new \Mutagenesis\FUTException('Invalid base directory: "'.$dir.'"');
        }
        $this->_baseDirectory = $dir;
        return $this;
    }

    /**
     * Get the base directory of the project being mutated
     *
     * @return string
     */
    public function getBaseDirectory()
    {
        return $this->_baseDirectory;
    }

    /**
     * Set the source directory of the project being mutated
     *
     * @param string $dir
     * @return $this
     * @throws \Mutagenesis\FUTException
     */
    public function setSourceDirectory($dir)
    {
        $dir = rtrim($dir, ' \\/');
        if (!is_dir($dir) || !is_readable($dir)) {
            throw new \Mutagenesis\FUTException('Invalid source directory: "'.$dir.'"');
        }
        $this->_sourceDirectory = $dir;
        return $this;
    }

    /**
     * Get the source directory of the project being mutated
     *
     * @return string
     */
    public function getSourceDirectory()
    {
        return $this->_sourceDirectory;
    }

    /**
     * Set the test directory of the project being mutated
     *
     * @param string $dir
     * @return $this
     * @throws \Mutagenesis\FUTException
     */
    public function setTestDirectory($dir)
    {
        $dir = rtrim($dir, ' \\/');
        if (!is_dir($dir) || !is_readable($dir)) {
            throw new \Mutagenesis\FUTException('Invalid test directory: "'.$dir.'"');
        }
        $this->_testDirectory = $dir;
        return $this;
    }

    /**
     * Get the test directory of the project being mutated
     *
     * @return string
     */
    public function getTestDirectory()
    {
        return $this->_testDirectory;
    }

    /**
     * Set the cache directory of the project being mutated
     *
     * @param string $dir
     * @return $this
     * @throws \Mutagenesis\FUTException
     */
    public function setCacheDirectory($dir)
    {
        $dir = rtrim($dir, ' \\/');
        if (!is_dir($dir) || !is_readable($dir)) {
            throw new \Mutagenesis\FUTException('Invalid cache directory: "'.$dir.'"');
        }
        $this->_cacheDirectory = $dir;
        return $this;
    }

    /**
     * Get the cache directory of the project being mutated
     *
     * @return string
     */
    public function getCacheDirectory()
    {
        if (is_null($this->_cacheDirectory)) {
            return sys_get_temp_dir();
        }
        return $this->_cacheDirectory;
    }

    /**
     * Set name of the test adapter to use
     *
     * @param string $constraint
     * @return $this
     */
    public function setAdapterConstraint($constraint)
    {
        $this->_adapterConstraint = $constraint;
        return $this;
    }

    /**
     * Get name of the test adapter to use
     *
     * @return string
     */
    public function getAdapterConstraint()
    {
        return $this->_adapterConstraint;
    }

    /**
     * Set name of the test adapter to use
     *
     * @param string $adapter
     * @return $this
     */
    public function setAdapterName($adapter)
    {
        $this->_adapterName = $adapter;
        return $this;
    }

    /**
     * Get name of the test adapter to use
     *
     * @return string
     */
    public function getAdapterName()
    {
        return $this->_adapterName;
    }

    /**
     * Options to pass to adapter's underlying command
     *
     * @param string $optionString
     * @return $this
     */
    public function setAdapterOption($optionString)
    {
        $this->_adapterOptions = array_merge(
            $this->_adapterOptions,
            explode(' ', $optionString)
        );
        return $this;
    }

    /**
     * Set many options for adapter's underlying cli command
     * @param array|string $options Array or serialized array of options
     * @return self
     */
    public function setAdapterOptions($options)
    {
        if (!is_array($options)) {
            $options = unserialize($options);
        }
        foreach ($options as $value) {
            $this->setAdapterOption($value);
        }
        return $this;
    }

    /**
     * Get a space delimited string of testing tool options
     *
     * @return string
     */
    public function getAdapterOptions()
    {
        return $this->_adapterOptions;
    }

    /**
     * Get a test framework adapter. Creates a new one based on the configured
     * adapter name passed on the CLI if not already set.
     *
     * @return \Mutagenesis\Adapter\AdapterAbstract
     * @throws \Mutagenesis\FUTException
     */
    public function getAdapter()
    {
        if (is_null($this->_adapter)) {
            $name = ucfirst(strtolower($this->getAdapterName()));
            $file = '/Adapter/' . $name . '.php';
            $class = 'Mutagenesis\\Adapter\\' . $name;
            if (!file_exists(dirname(dirname(__FILE__)) . $file)) {
                throw new \Mutagenesis\FUTException('Invalid Adapter name: ' . strtolower($name));
            }
            $this->_adapter = new $class;
        }
        return $this->_adapter;
    }

    /**
     * Set a test framework adapter.
     *
     * @param \Mutagenesis\Adapter\AdapterAbstract $adapter
     * @return $this
     */
    public function setAdapter(\Mutagenesis\Adapter\AdapterAbstract $adapter)
    {
        $this->_adapter = $adapter;
        return $this;
    }

    /**
     * Set name of the renderer to use
     *
     * @param string $rname
     * @return $this
     */
    public function setRendererName($rname)
    {
        $this->_rendererName = $rname;
        return $this;
    }

    /**
     * Get name of the renderer to use
     *
     * @return string
     */
    public function getRendererName()
    {
        return $this->_rendererName;
    }
    
    /**
     * Get a result renderer. Creates a new one based on the configured
     * renderer name passed on the CLI if not already set.
     *
     * @return \Mutagenesis\Renderer\RendererInterface
     * @throws \Mutagenesis\FUTException
     */
    public function getRenderer()
    {
        if (is_null($this->_renderer)) {
            $name = ucfirst(strtolower($this->getRendererName()));
            $class = 'Mutagenesis\\Renderer\\' . $name;
            if (!class_exists($class)) {
                throw new \Mutagenesis\FUTException('Invalid Renderer name: ' . strtolower($name));
            }
            $this->_renderer = new $class;
        }
        return $this->_renderer;
    }

    /**
     * Set a test framework adapter.
     *
     * @param \Mutagenesis\Renderer\RendererInterface $renderer
     * @return $this
     */
    public function setRenderer(\Mutagenesis\Renderer\RendererInterface $renderer)
    {
        $this->_renderer = $renderer;
        return $this;
    }

    /**
     * Set a custom runkit instance.
     *
     * @param \Mutagenesis\Utility\Runkit $runkit
     * @return $this
     */
    public function setRunkit(\Mutagenesis\Utility\Runkit $runkit)
    {
        $this->_runkit = $runkit;
        return $this;
    }

    /**
     * Creates and returns a new instance of \Mutagenesis\Runkit if not previously
     * loaded
     *
     * @return \Mutagenesis\Runkit
     */
    public function getRunkit()
    {
        if (is_null($this->_runkit)) {
            if(!in_array('runkit', get_loaded_extensions())) {
                throw new \Mutagenesis\FUTException(
                    'Runkit extension is not loaded. Unfortunately, runkit'
                    . ' is essential for Mutagenesis. Please see the manual or'
                    . ' README which explains how to install an updated runkit'
                    . ' extension suitable for Mutagenesis and PHP 5.3.'
                );
            }
            $this->_runkit = new \Mutagenesis\Utility\Runkit;
        }
        return $this->_runkit;
    }

    /**
     * Set a generic option
     *
     * @param string $name
     * @param mixed $value
     */
    public function setOption($name, $value)
    {
        $this->_options[$name] = $value;
        return $this;
    }
    
    /**
     * Set generic options
     *
     * @param string $name
     * @param mixed $value
     */
    public function setOptions(array $options)
    {
        foreach ($options as $name=>$value) {
            $this->setOption($name, $value);
        }
        return $this;
    }

    /**
     * Compile all necessary options for the test framework adapter
     *
     * @return array
     */
    public function getOptions()
    {
        $options = array(
            'src' => $this->getSourceDirectory(),
            'tests' => $this->getTestDirectory(),
            'base' => $this->getBaseDirectory(),
            'cache' => $this->getCacheDirectory(),
            'clioptions' => $this->getAdapterOptions(),
            'constraint' => $this->getAdapterConstraint()
        );
        $options = $options + $this->_options;
        return $options;
    }

    /**
     * Generate Mutants!
     *
     * @return array
     */
    public function getMutables()
    {
        if (empty($this->_mutables)) {
            $generator = $this->getGenerator();
            $generator->generate();
            $this->_mutables = $generator->getMutables();
        }
        return $this->_mutables;
    }

    /**
     * Set a specific Generator of mutations (stuck with a subclass).
     * TODO Add interface
     *
     * @param \Mutagenesis\Generator
     */
    public function setGenerator(\Mutagenesis\Generator $generator)
    {
        $this->_generator = $generator;
        $this->_generator->setSourceDirectory($this->getSourceDirectory());
        return $this;
    }

    /**
     * Get a specific Generator of mutations.
     *
     * @return \Mutagenesis\Generator
     */
    public function getGenerator()
    {
        if (!isset($this->_generator)) {
            $this->_generator = new \Mutagenesis\Generator($this);
            $this->_generator->setSourceDirectory($this->getSourceDirectory());
        }
        return $this->_generator;
    }

    /**
     * Set timeout in seconds for each test run
     *
     * @param int $timeout
     * @return $this
     */
    public function setTimeout($timeout)
    {
        $this->_timeout = (int) $timeout;
        return $this;
    }

    /**
     * Get timeout in seconds for each test run
     *
     * @return null|int
     */
    public function getTimeout()
    {
        return $this->_timeout;
    }

    /**
     * Set a bootstrap file included before tests run (e.g. setup autoloading)
     *
     * @param string $file
     * @return $this
     */
    public function setBootstrap($file)
    {
        if (empty($file)) {
            return $this;
        }
        if (!file_exists($file) || !is_readable($file)) {
            throw new \Mutagenesis\FUTException('Invalid bootstrap file: "'.$file.'"');
        }
        $this->_bootstrap = $file;
        return $this;
    }

    /**
     * Get a bootstrap file included before tests run
     *
     * @return string
     */
    public function getBootstrap()
    {
        if (is_null($this->_bootstrap)) {
            if (file_exists($this->getTestDirectory() . '/TestHelper.php')) {
                return $this->getTestDirectory() . '/TestHelper.php';
            } elseif (file_exists($this->getTestDirectory() . '/Bootstrap.php')) {
                return $this->getTestDirectory() . '/Bootstrap.php';
            }
        }
        return $this->_bootstrap;
    }

    /**
     * Set flag to add detailed reports (including test results) about
     * the mutations which caused test failures (i.e. captured)
     *
     * @param bool $bool
     * @return $this
     */
    public function setDetailCaptures($bool)
    {
        $this->_detailCaptures = (bool) $bool;
        return $this;
    }

    /**
     * Get flag to add detailed reports (including test results) about
     * the mutations which caused test failures (i.e. captured)
     *
     * @return bool
     */
    public function getDetailCaptures()
    {
        return $this->_detailCaptures;
    }
}