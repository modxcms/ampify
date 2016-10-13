<?php

/**
 * Ampify class for MODX, includes methods to create OAuth2 server object.
 * @package Ampify
 *
 * @author @sepiariver <yj@modx.com>
 * Copyright 2015 by YJ Tso
 * 
 * This program is free software; you can redistribute it and/or modify it under
 * the terms of the GNU General Public License as published by the Free Software
 * Foundation; either version 2 of the License, or (at your option) any later
 * version.
 *
 * This program is distributed in the hope that it will be useful, but WITHOUT
 * ANY WARRANTY; without even the implied warranty of MERCHANTABILITY or FITNESS
 * FOR A PARTICULAR PURPOSE. See the GNU General Public License for more
 * details.
 *
 * You should have received a copy of the GNU General Public License along with
 * this program; if not, write to the Free Software Foundation, Inc., 59 Temple
 * Place, Suite 330, Boston, MA 02111-1307 USA
 **/

class Ampify
{
    public $modx = null;
    public $namespace = 'ampify';
    public $options = array();
    public $amp = null;
    
    public function __construct(modX &$modx, array $options = array())
    {
        $this->modx =& $modx;
        $this->namespace = $this->getOption('namespace', $options, 'ampify');

        $corePath = $this->getOption('core_path', $options, $this->modx->getOption('core_path', null, MODX_CORE_PATH) . 'components/ampify/');
        $assetsPath = $this->getOption('assets_path', $options, $this->modx->getOption('assets_path', null, MODX_ASSETS_PATH) . 'components/ampify/');
        $assetsUrl = $this->getOption('assets_url', $options, $this->modx->getOption('assets_url', null, MODX_ASSETS_URL) . 'components/ampify/');
        $dbPrefix = $this->getOption('table_prefix', $options, $this->modx->getOption('table_prefix', null, 'modx_'));
        
        /* load config defaults */
        $this->options = array_merge(array(
            'namespace' => $this->namespace,
            'corePath' => $corePath,
            'modelPath' => $corePath . 'model/',
            'ampLibPath' => $corePath . 'model/amp-library/',
            'ampLibPathSterc' => $corePath . 'model/modx-amp/',
            'chunksPath' => $corePath . 'elements/chunks/',
            'snippetsPath' => $corePath . 'elements/snippets/',
            'templatesPath' => $corePath . 'templates/',
            'assetsPath' => $assetsPath,
            'assetsUrl' => $assetsUrl,
            'jsUrl' => $assetsUrl . 'js/',
            'cssUrl' => $assetsUrl . 'css/',
            'connectorUrl' => $assetsUrl . 'connector.php',

        ), $options);
        
    }    
    
    public function getAmp() {
        // Load AMP Library
        require_once($this->options['ampLibPath'] . 'vendor/autoload.php');
        return new Lullabot\AMP\AMP();    
    }
    public function getAmpSterc($content, $embed_handler_classes, $sanitizers, $args = array()) {
        // Load AMP Library
        define('AMP__DIR__', rtrim($this->options['ampLibPathSterc'], '/'));
        require_once($this->options['ampLibPathSterc'] . 'class-amp-content.php');
        
        // Load sanitizers
        $sanitizer_classes = array();
        foreach ($sanitizers as $sanitizer => $args) {
            require_once($this->options['ampLibPathSterc'] . 'includes/sanitizers/class-amp-' . $sanitizer . '-sanitizer.php');
            $sanitizer_classes['AMP_' . ucfirst($sanitizer) . '_Sanitizer'] = $args;
        }
        
        return new AMP_Content($content, $embed_handler_classes, $sanitizer_classes, $args);
        
    }
    
    /* UTILITY METHODS (@theboxer) */
    /**
     * Get a local configuration option or a namespaced system setting by key.
     *
     * @param string $key The option key to search for.
     * @param array $options An array of options that override local options.
     * @param mixed $default The default value returned if the option is not found locally or as a
     * namespaced system setting; by default this value is null.
     * @return mixed The option value or the default value specified.
     */
    public function getOption($key, $options = array(), $default = null)
    {
        $option = $default;
        if (!empty($key) && is_string($key)) {
            if ($options != null && array_key_exists($key, $options)) {
                $option = $options[$key];
            } elseif (array_key_exists($key, $this->options)) {
                $option = $this->options[$key];
            } elseif (array_key_exists("{$this->namespace}.{$key}", $this->modx->config)) {
                $option = $this->modx->getOption("{$this->namespace}.{$key}");
            }
        }
        return $option;
    }

    public function explodeAndClean($array, $delimiter = ',')
    {
        $array = explode($delimiter, $array);     // Explode fields to array
        $array = array_map('trim', $array);       // Trim array's values
        $array = array_keys(array_flip($array));  // Remove duplicate fields
        $array = array_filter($array);            // Remove empty values from array

        return $array;
    }
    public function getChunk($tpl, $phs)
    {
        if (strpos($tpl, '@INLINE ') !== false) {
            $content = str_replace('@INLINE', '', $tpl);
            /** @var \modChunk $chunk */
            $chunk = $this->modx->newObject('modChunk', array('name' => 'inline-' . uniqid()));
            $chunk->setCacheable(false);
            
            return $chunk->process($phs, $content);
        }
        
        return $this->modx->getChunk($tpl, $phs);
    }
}