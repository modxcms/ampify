<?php
/**
 * ampfilter
 * 
 * @description Output filter to convert HTML to AMP-compliant markup
 * @package Ampify
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
$startTime = microtime();
// Paths
$ampifyPath = $modx->getOption('ampify.core_path', null, $modx->getOption('core_path') . 'components/ampify/');
$ampifyPath .= 'model/ampify/';

// Get Class
if (file_exists($ampifyPath . 'ampify.class.php')) $ampify = $modx->getService('ampify', 'Ampify', $ampifyPath, $scriptProperties);
if (!($ampify instanceof Ampify)) {
    $modx->log(modX::LOG_LEVEL_ERROR, '[ampfilter] could not load the required class!');
    return;
}

$ampLibrary = 'sterc';
// Fetch AMP object

if ($ampLibrary === 'sterc') {
    $amp = $ampify->getAmpSterc($input, array(), array('img' => array()), array());
    return '<pre>' . (microtime() - $startTime) . '</pre>' . $amp->get_amp_content();
} else {
    $amp = $ampify->getAmp();
    $amp->loadHtml($input);
    return '<pre>' . (microtime() - $startTime) . '</pre>' . $amp->convertToAmpHtml();  
}