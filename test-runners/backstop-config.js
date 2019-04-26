'use strict';

const refUrl = process.env.BACKSTOP_REF_URL;
const testUrl = process.env.BACKSTOP_TEST_URL;
const misMatchThresh = 10.0;

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

    if(path.removeSelectors !== undefined) {
        test.removeSelectors = path.removeSelectors;
    }

    if(path.delay !== undefined) {
        test.delay = path.delay;
    }

    config.scenarios.push(test);
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