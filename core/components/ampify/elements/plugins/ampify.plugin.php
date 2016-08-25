<?php
/**
 * AMPIFY
 * 
 * Plugin to handle AMP pages
 * @author @sepiariver
 * @package AMPIFY
 * 
 **/

// AMPIFY Url Param
$ampUrlParam = $modx->getOption('amp_url_param', $scriptProperties, '');

// AMPIFY Context
$ampContext = $modx->getOption('amp_context', $scriptProperties, '');

// AMPIFY Mode
$mode = 0;
if (!empty($ampUrlParam)) {
    $mode = 'param';
} elseif (!empty($ampContext)) {
    $countCtx = $modx->getCount('modContext', array('key' => $ampContext));
    if ($countCtx === 1) {
        $mode = 'context';
    }
}
if ($mode === 0) {
    $modx->log(modX::LOG_LEVEL_WARN, 'AMPIFY requires at least a valid amp_context or an amp_url_param specified.');
    return;
}

// AMPIFY Template
$ampTemplate = $modx->getOption('amp_template', $scriptProperties, '');
$countTpl = $modx->getCount('modTemplate', $ampTemplate);
if ($countTpl !== 1) {
    $modx->log(modX::LOG_LEVEL_ERROR, 'AMPIFY requires a valid, default Template ID');
    return;
}

// AMPIFY TV
$ampTv = $modx->getOption('amp_tv', $scriptProperties, '');
$countTv = $modx->getCount('modTemplateVar', array('name' => $ampTv));

$event = $modx->event->name;
switch ($event) {
    
    case 'OnLoadWebDocument':

        // Escape conditions
        if ($mode === 'param' && !isset($_GET[$ampUrlParam])) {
            break;
        }
        if ($mode === 'context' && ($modx->context->get('key') !== $ampContext)) {
            break;
        }
        if (!($modx->resource instanceof modResource)) {
            break;
        }
        
        // Check Resource AMP TV
        if ($countTv === 1) {
            $tvValue = $modx->resource->getTVValue($ampTv);
            $countTvTpl = $modx->getCount('modTemplate', $tvValue);
            if ($countTvTpl === 1) $ampTemplate = $tvValue;
        }
        
        // Don't cache 'param' mode result
        if ($mode === 'param') $modx->resource->set('cacheable', 0);
        
        // Set runtime resource property
        $modx->resource->set('template', $ampTemplate);
        
        // Move on
        break;
        
    case 'OnDocFormSave':

        // Probably overly paranoid
        if ($modx->context->get('key') !== 'mgr') {
            break;
        }
        
        // If $mode isn't 'context', stop doing ContextResource actions
        if ($mode !== 'context') {
            break;
        }
        
        // Check Resource
        if (!($resource instanceof modResource)) {
            break;
        }
        
        // Check Resource AMP TV
        if ($countTv === 1) {
            $tvValue = $resource->getTVValue($ampTv);
        }
        
        // Set criteria for ContextResource object
        $criteria = array(
            'context_key' => $ampContext,
            'resource' => $resource->get('id'),
        );
        
        // Check ContextResource
        $ctxRes = $modx->getObject('modContextResource', $criteria);
        
        // Remove if using the AMP TV and there' no tvValue
        if ($countTv === 1 && !$tvValue && $ctxRes instanceof modContextResource) {
            $ctxRes->remove();
            break;
        }
        
        // Create if it doesn't exist
        if ($ctxRes === null) {
            
            $rc = $modx->newObject('modContextResource');
            $rc->fromArray($criteria, '', true); //set pk
            // Save
            if (!$rc->save()) {
                $modx->log(modX::LOG_LEVEL_ERROR, 'AMPIFY could not save modContextResource: ' . print_r($criteria, true));
            }
            // Trigger the Context Gateway
            $modx->cacheManager->refresh();
        }
        
        // We're done
        break;
    
    // Don't do anything on other events
    default:
        break;
        
}

// The Plugin returns nothing
return;