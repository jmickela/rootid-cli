"use strict";

const refUrl = process.env.BACKSTOP_REF_URL;
const testUrl = process.env.BACKSTOP_TEST_URL;
const testDir = process.env.PWD;
const misMatchThresh = 1.0;
const debug = process.env.BACKSTOP_DEBUG;

// getting the rootid test-runners directory
const rootidDir = __dirname;

const backstop_paths = JSON.parse(process.env.TEST_ELEMENTS);

const config = {
  id: "backstop_default",
  // mergeImgHack: true,
  viewports: [
    {
      label: "Phone",
      width: 320,
      height: 2000,
    },
    {
      label: "Vertical Tablet",
      width: 768,
      height: 2000,
    },
    {
      label: "Horizontal Tablet",
      width: 1024,
      height: 2000,
    },
    {
      label: "Desktop",
      width: 1920,
      height: 2000,
    },
  ],
  paths: {
    bitmaps_reference: "/tmp/artifacts/backstop_data/bitmaps_reference",
    bitmaps_test: "/tmp/artifacts/backstop_data/bitmaps_test",
    compare_data: "/tmp/artifacts/backstop_data/bitmaps_test/compare.json",
    // engine_scripts = the directory which backstop will use as the starting point for relative file paths to scripts
    engine_scripts: rootidDir + "/backstop_engine_scripts",
  },
  onBeforeScript: "puppet/onBefore.js",
  onReadyScript: "custom/radicatiOnReady.js",
  engine: "puppeteer",
  engineOptions: {
    // uncomment headless to see Chromium at work!
    // headless: false,
    // gotoTimeout: 30000,
    // devtools: true,
  },
  report: ["CLI"],
  debug: false,
  // uncomment debugWindow to see Chromium at work!
  // debugWindow: true,
  port: 3001,
  asyncCaptureLimit: 1,
  asyncCompareLimit: 8,
  scenarios: [],
};

if (debug == "debug") {
  config.debugWindow = true;
}

config.scenarios = [];

backstop_paths.forEach(function (path) {
  var test = {
    label: path.label,
    url: testUrl + path.path,
    referenceUrl: refUrl + path.path,
    misMatchThreshold: misMatchThresh,
    delay: 300,
  };

  if (path.selectors !== undefined) {
    test.selectors = path.selectors;
  } else {
    test.selectors = ["body"];
  }

  for (const property in path) {
    if (
      property !== "label" &&
      property !== "path" &&
      property !== "selectors"
    ) {
      test[property] = path[property];
    }
  }

  config.scenarios.push(test);
});

module.exports = config;
