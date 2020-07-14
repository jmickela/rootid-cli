## Rootid Backstop CLI Options
```
Options:
      --env[=ENV]              Specific site environment to sync from. [default: "live"]
      --generate[=GENERATE]    If set this will create references images as well as run a test.
      --test[=TEST]            If set this allows you to set a specific test url [default: local site]
      --reference[=REFERENCE]  If set this allows you to set a specific reference url [default: live site]
      --debug[=DEBUG]          If set this toggles the browser to run tests in non-headless, visible mode so you can watch
  -h, --help                   Display this help message
  -q, --quiet                  Do not output any message
  -V, --version                Display this application version
      --ansi                   Force ANSI output
      --no-ansi                Disable ANSI output
  -n, --no-interaction         Do not ask any interactive question
  -v|vv|vvv, --verbose         Increase the verbosity of messages: 1 for normal output, 2 for more verbose output and 3 for debug
```

## elements.json
Your elements json should be structured as an array of objects. Each object is a single "scenario". At a bare minimum, you should have at least one scenario (otherwise, what's the point?!)

Each scenario *must* have a label, a path, and a selector. (Other elements required by backstop are being calculated by the shiny rootid CLI :) The puppeteer browser will go to your test site and follow the path. It will load the page and then take a screenshot of the selector.

Here's an example of a bare minimum elements.json file:

```
[
  {
    "label": "Whatever",
    "path": "/",
    "selectors": ["document"]
  }
]

```

## Custom scenario settings in elements.json

`complexInteraction` is an array of objects. The interactions will be performed in the order they appear within the array.

```
"complexInteraction": [
      {
        "type": "click",
        "selector": "some_selector",
        "wait": integer    // how long to wait after clicking
      },
      {
        "type": "hover",
        "selector": "some_selector",
        "wait": integer    // how long to wait after hovering
      },
      {
        "type": "scroll",
        "selector": "some_selector",
        "wait": integer    // how long to wait after scrolling
      },
      {
        "type": "click",
        "selector": "some_selector",
        "wait": integer    // how long to wait after clicking
      },
      {
        "type": "keypress",
        "selector": "some_selector",
        "keyPress": "some_text",
        "wait": integer    // how long to wait after typing
      },
      {
        "type": "disableScrollReveal",
        "wait": integer    // how long to wait after disabling the scroll reveal
      },
      {
        "type": "disableLazyload",
        "wait": integer    // how long to wait after disabling the lazyload
      },
      {
        "type": "pauseSlick",
        "wait": integer    // how long to wait after pausing all sliders
      }
    ],
```

## Built-in commands

Advanced Scenarios:

These are the scenario properties built-in to BackstopJS. Note that **they are processed sequentially in the following order**:

```
label                    // [required] Tag saved with your reference images
onBeforeScript           // Used to set up browser state e.g. cookies.
cookiePath               // import cookies in JSON format (available with default onBeforeScript see setting cookies below)
url                      // [required] The url of your app state
referenceUrl             // Specify a different state or environment when creating reference.
readyEvent               // Wait until this string has been logged to the console.
readySelector            // Wait until this selector exists before continuing.
delay                    // Wait for x milliseconds
hideSelectors            // Array of selectors set to visibility: hidden
removeSelectors          // Array of selectors set to display: none
onReadyScript            // After the above conditions are met -- use this script to modify UI state prior to screen shots e.g. hovers, clicks etc.
keyPressSelectors        // Takes array of selector and string values -- simulates multiple sequential keypress interactions.
hoverSelector            // Move the pointer over the specified DOM element prior to screen shot.
hoverSelectors           // *Puppeteer only* takes array of selectors -- simulates multiple sequential hover interactions.
clickSelector            // Click the specified DOM element prior to screen shot.
clickSelectors           // *Puppeteer only* takes array of selectors -- simulates multiple sequential click interactions.
postInteractionWait      // Wait for a selector after interacting with hoverSelector or clickSelector (optionally accepts wait time in ms. Idea for use with a click or hover element transition. available with default onReadyScript)
scrollToSelector         // Scrolls the specified DOM element into view prior to screen shot (available with default onReadyScript)
selectors                // Array of selectors to capture. Defaults to document if omitted. Use "viewport" to capture the viewport size. See Targeting elements in the next section for more info...
selectorExpansion        // See Targeting elements in the next section for more info...
misMatchThreshold        // Percentage of different pixels allowed to pass test
requireSameDimensions    // If set to true -- any change in selector size will trigger a test failure.
viewports                // An array of screen size objects your DOM will be tested against. This configuration will override the viewports property assigned at the config root.
```

Also note that the cookie path is relative to the elements.json file, while the script paths (such as onBeforeScript) are relative to the rootid CLI tool.

See [BackstopJS Documentation: Using Backstop](https://github.com/garris/BackstopJS#using-backstopjs) for more details
