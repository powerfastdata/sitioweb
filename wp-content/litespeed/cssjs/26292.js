function get_Dyncontel_ElementSettings($element){var elementSettings=[];var modelCID=$element.data('model-cid');if(elementorFrontend.isEditMode()&&modelCID){var settings=elementorFrontend.config.elements.data[modelCID];var type=settings.attributes.widgetType||settings.attributes.elType;var settingsKeys=elementorFrontend.config.elements.keys[type];if(!settingsKeys){settingsKeys=elementorFrontend.config.elements.keys[type]=[];jQuery.each(settings.controls,function(name,control){if(control.frontend_available){settingsKeys.push(name)}})}
jQuery.each(settings.getActiveControls(),function(controlKey){if(-1!==settingsKeys.indexOf(controlKey)){elementSettings[controlKey]=settings.attributes[controlKey]}})}else{elementSettings=$element.data('settings')||{}}
return elementSettings}
function observe_Dyncontel_element($target,$function_callback){if(elementorFrontend.isEditMode()){var elemToObserve=$target;var config={attributes:!0,childList:!1,characterData:!0};var MutationObserver=window.MutationObserver||window.WebKitMutationObserver||window.MozMutationObserver;var observer=new MutationObserver($function_callback);observer.observe(elemToObserve,config)}}(function($){var get_Dyncontel_ElementSettings=function($element){var elementSettings={};var modelCID=$element.data('model-cid');if(elementorFrontend.isEditMode()&&modelCID){var settings=elementorFrontend.config.elements.data[modelCID];var settingsKeys=elementorFrontend.config.elements.keys[settings.attributes.widgetType||settings.attributes.elType];var type=settings.attributes.widgetType||settings.attributes.elType;if(!settingsKeys){settingsKeys=elementorFrontend.config.elements.keys[type]=[];jQuery.each(settings.controls,function(name,control){if(control.frontend_available){settingsKeys.push(name)}})}
jQuery.each(settings.getActiveControls(),function(controlKey){if(-1!==settingsKeys.indexOf(controlKey)){elementSettings[controlKey]=settings.attributes[controlKey]}})}else{elementSettings=$element.data('settings')||[]}
return elementSettings}})(jQuery)
;