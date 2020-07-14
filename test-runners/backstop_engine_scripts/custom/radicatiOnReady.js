module.exports = async (page, scenario, vp) => {

  var $target = String.fromCodePoint(0x1f3af);

  console.log($target + " SCENARIO > " + scenario.label);

  /*
   * Output console message from puppeteer browser to terminal
   */
  // page
  //   .on("console", (message) =>
  //     console.log(
  //       `${message.type().substr(0, 3).toUpperCase()} ${message.text()}`
  //     )
  //   )
  //   .on("pageerror", ({ message }) => console.log(message))
  //   .on("response", (response) =>
  //     console.log(`${response.status()} ${response.url()}`)
  //   )
  //   .on("requestfailed", (request) =>
  //     console.log(`${request.failure().errorText} ${request.url()}`)
  //   );


  /*
   * Run a command in the puppeteer browser's terminal
   * (These specific commands are to force immediate loading of lazyload images that are screwing up my diffs!)
   */
  // await page.evaluate(() => {
  //   if (
  //     typeof Drupal !== "undefined" &&
  //     typeof Drupal.blazy !== "undefined"
  //   ) {
  //     // Force immediate load of Drupal blazy lazyload images
  //     Drupal.blazy.init.load(document.getElementsByClassName("b-lazy", true));
  //     setTimeout(function () {
  //       console.log("Forcing lazyload image to load...");
  //     }, 2000);
  //   }
  //   // Force immediate load of WP Autoptimize lazyload images
  //   if (typeof lazySizes !== "undefined" && typeof lazySizes.loader !== "undefined" && typeof lazySizes.loader.unveil !== undefined) {
  //     var imageArray = document.querySelectorAll(".lazyload");
  //     imageArray.forEach((image) => lazySizes.loader.unveil(image));
  //     setTimeout(function () {
  //       console.log("Forcing lazyload image to load...");
  //     }, 2000);
  //   }
  //   // Pause any slick sliders
  //   if (typeof jQuery !== "undefined" && typeof jQuery.fn.slick !== "undefined") {
  //     var autoplaySliders = jQuery(".slick-slider.autoplay");
  //     autoplaySliders.slick("slickPause");
  //   }

  // });

  // await page.waitFor(1000);

  /*
   * Load default interaction helper
   */
  await require("../puppet/clickAndHoverHelper")(page, scenario);

  /*
   * Load my more complex interaction helper
   */
  await require("./radicatiComplexInteractions")(page, scenario);

  // add more ready handlers here...
};
