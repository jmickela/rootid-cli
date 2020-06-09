'use strict';

const refUrl = process.env.BACKSTOP_REF_URL;
const testUrl = process.env.BACKSTOP_TEST_URL;
const misMatchThresh = 10.0;

const backstop_paths = JSON.parse(process.env.TEST_ELEMENTS);

const config = {
    'id': 'backstop_default',
    "viewports": [
        {
            "label": "phone",
            "width": 320,
            "height": 480
        },
        {
            "label": "tablet_v",
            "width": 768,
            "height": 1024
        },
        {
            "label": "tablet_h",
            "width": 1024,
            "height": 768
        },
        {
            "label": "desktop",
            "width": 1920,
            "height": 1080
        }
    ],
    "paths": {
        "bitmaps_reference": "/tmp/artifacts/backstop_data/bitmaps_reference",
        "bitmaps_test": "/tmp/artifacts/backstop_data/bitmaps_test",
        "compare_data": "/tmp/artifacts/backstop_data/bitmaps_test/compare.json",
        "casper_scripts": "backstop_data/casper_scripts"
    },
    "engine": "puppeteer",
    "report": [ "CLI" ],
    //"casperFlags": [],
    "debug": false,
    "port": 3001,
    "asyncCaptureLimit": 3,
    "asyncCompareLimit": 3,

    "scenarios": []
};


config.scenarios = [];

backstop_paths.forEach(function(path) {
    var test = {
        'label': path.label,
        'url': testUrl + path.path,
        'referenceUrl': refUrl + path.path,
        'misMatchThreshold': misMatchThresh,
        'delay': 300,
    };

    if(path.selectors !== undefined) {
        test.selectors = path.selectors;
    } else {
        test.selectors = ["body"];
    }

    var otherScenarioProperties = [
      'onBeforeScript',
      'cookiePath',
      'readyEvent',
      'readySelector',
      'delay',
      'hideSelectors',
      'removeSelectors',
      'onReadyScript',
      'keyPressSelectors',
      'hoverSelector',
      'hoverSelectors',
      'clickSelector',
      'clickSelectors',
      'postInteractionWait',
      'scrollToSelector',
      'selectors',
      'selectorExpansion',
      'misMatchThreshold',
      'requireSameDimensions',
      'viewports',
    ];

    otherScenarioProperties.forEach(function(property) {
        if (path[property] !== undefined) {
            test[property] = path[property];
        }
    });

    config.scenarios.push(test);

    console.log(config.scenarios);
});





    // if(path.selectors) {
    //     path.selectors.forEach(function(selectors) {
    //         config.scenarios.push({
    //             'label': path.label,
    //             'url': testUrl + path.path,
    //             'referenceUrl': refUrl + path.path,
    //             'misMatchThreshold': misMatchThresh,
    //             'selectors': selectors,
    //             'delay': 300,
    //             'removeSelectors': path.removeSelectors
    //         });
    //     });
    // } else {
    //     config.scenarios.push({
    //         'label': path.label,
    //         'url': testUrl + path.path,
    //         'referenceUrl': refUrl + path.path,
    //         'misMatchThreshold': misMatchThresh,
    //         'delay': 300,
    //         'removeSelectors': path.removeSelectors
    //     });
    // }
//});




// const scenarios = backstop_paths.map(function(path) {
//    return {
//        'label': path.label,
//        'url': testUrl + path.path,
//        'referenceUrl': refUrl + path.path,
//        'misMatchThreshold': misMatchThresh,
//        'delay': 1000
//    }
// });

// config.scenarios = config.scenarios.concat(scenarios);

module.exports = config;
