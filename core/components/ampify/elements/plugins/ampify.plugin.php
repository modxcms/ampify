<?php
/**
 * AMPIFY
 * 
 * Plugin to handle AMP pages
 * @author @sepiariver
 * @package AMPIFY
 * 
 **/

// AMPIFY Context
$amp_context = $modx->getOption('amp_context', $scriptProperties, 'amp', true);
$countCtx = $modx->getCount('modContext', array('key' => $amp_context));
if ($countCtx !== 1) {
    $modx->log(modX::LOG_LEVEL_ERROR, 'AMPIFY could not find a Context with key: ' . $amp_context);
    return;
}

// AMPIFY Template
$amp_template = $modx->getOption('amp_template', $scriptProperties, '');
$countTpl = $modx->getCount('modTemplate', $amp_template);
if ($countTpl !== 1) {
    $modx->log(modX::LOG_LEVEL_ERROR, 'AMPIFY requires a valid, default Template ID');
    return;
}

// AMPIFY TV
$amp_tv = $modx->getOption('amp_tv', $scriptProperties, '');
$tvCol = (is_int($amp_tv)) ? 'id' : 'name';
$countTv = $modx->getCount('modTemplateVar', array($tvCol => $amp_tv));

$event = $modx->event->name;
switch ($event) {
    
    case 'OnLoadWebDocument':

        // Only fire on AMP Context
        if ($modx->context->get('key') !== $amp_context) {
            break;
        }

        // Check Resource
        if (!($modx->resource instanceof modResource)) {
            break;
        }
        
        // Check Resource AMP TV
        if ($countTv === 1) {
            $tvValue = $modx->resource->getTVValue($amp_tv);
            $countTvTpl = $modx->getCount('modTemplate', $tvValue);
            if ($countTvTpl === 1) $amp_template = $tvValue;
        }
        
        // Set runtime resource property
        $modx->resource->set('template', $amp_template);
        
        // Move on
        break;
        
    case 'OnDocFormSave':

        // Probably overly paranoid
        if ($modx->context->get('key') !== 'mgr') return;
        
        // Check Resource
        if (!($resource instanceof modResource)) return;
        
        // Check Resource AMP TV
        if ($countTv === 1) {
            $tvValue = $resource->getTVValue($amp_tv);
            if (!$tvValue) break;
        }
        
        // Set criteria for ContextResource object
        $criteria = array(
            'context_key' => $amp_context,
            'resource' => $resource->get('id'),
        );
        
        // Check ContextResource
        $countCtxRes = $modx->getCount('modContextResource', $criteria);
        
        // Create if it doesn't exist
        if ($countCtxRes < 1) {
            
            $rc = $modx->newObject('modContextResource');
            // Use set(). It's not an xPDOSimpleObject            
            $rc->set('context_key', $criteria['context_key']);
            $rc->set('resource', $criteria['resource']);
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