<?php
/**
 * AMPIFY
 * 
 * Plugin to handle AMP pages
 * @author @sepiariver
 * @package AMPIFY
 * 
 **/

$amp_context = $modx->getOption('amp_context', $scriptProperties, 'amp', true);

$event = $modx->event->name;
switch ($event) {
    
    case 'OnLoadWebDocument':

        // Check Context TODO: make configurable
        if ($modx->context->get('key') !== $amp_context) return;

        // Check Resource
        if (!($modx->resource instanceof modResource)) return;
        
        // Check Template
        $amp_template = $modx->getOption('amp_template', $scriptProperties, 6);
        $count = $modx->getCount('modTemplate', $amp_template);
        if ($count !== 1) {
            $modx->log(modX::LOG_LEVEL_ERROR, 'AMPIFY was not provided a valid Template ID');
            return;
        }
        
        // Set runtime resource property
        $modx->resource->set('template', $amp_template);
        
        // Move on
        return true;
        break;
        
    case 'OnDocFormSave':

        // Probably overly paranoid
        if ($modx->context->get('key') !== 'mgr') return;
        
        // Check Resource
        if (!($resource instanceof modResource)) return;
        
        $criteria = array(
            'context_key' => $amp_context,
            'resource' => $resource->get('id'),
        );
        
        // Check Resource Context
        $rc = $modx->getObject('modContextResource', $criteria);
        
        // Create if it doesn't exist
        if (!$rc) {
            
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

        return true;
        break;
        
    default:
        break;
        
}

return;