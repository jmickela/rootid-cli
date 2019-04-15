'use strict';

const refUrl = process.env.BACKSTOP_REF_URL;
const testUrl = process.env.BACKSTOP_TEST_URL;
const misMatchThresh = 0.9;

const backstop_paths = JSON.parse(process.env.TEST_ELEMENTS);

const config = {
    'id': 'backstop_default',
    "viewports": [
        // {
        //     "name": "phone",
        //     "width": 320,
        //     "height": 480
        // },
        {
            "name": "tablet_v",
            "width": 568,
            "height": 1024
        },
        {
            "name": "tablet_h",
            "width": 1024,
            "height": 768
        },
        {
            "name": "desktop",
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
    "engine": "chrome",
    "report": [ "CLI" ],
    "casperFlags": [],
    "debug": false,
    "port": 3001,
    "asyncCaptureLimit": 5,
    "asyncCompareLimit": 5,

    "scenarios": []
};


config.scenarios = [];

backstop_paths.forEach(function(path) {
    if(path.selectors) {
        path.selectors.forEach(function(selectors) {
            config.scenarios.push({
                'label': path.label,
                'url': testUrl + path.path,
                'referenceUrl': refUrl + path.path,
                'misMatchThreshold': misMatchThresh,
                'selectors': selectors,
                'delay': 300
            });    
        });
    } else {
        config.scenarios.push({
            'label': path.label,
            'url': testUrl + path.path,
            'referenceUrl': refUrl + path.path,
            'misMatchThreshold': misMatchThresh,
            'delay': 300
        });
    }
});




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