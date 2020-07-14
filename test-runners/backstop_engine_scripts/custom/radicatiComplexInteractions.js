module.exports = async (page, scenario) => {

  var rainbow = String.fromCodePoint(0x1f308);
  var complexInteraction = scenario.complexInteraction;

  if (
    complexInteraction !== null &&
    complexInteraction !== "" &&
    typeof complexInteraction == "object" &&
    complexInteraction.length > 0
  ) {
    for (interaction of complexInteraction) {
      if (interaction.type == "hover") {
        console.log("  Hovering over " + interaction.selector);
        await page.waitFor(interaction.selector);
        await page.hover(interaction.selector);
        if (interaction.wait) {
          await page.waitFor(interaction.wait);
        }
      }

      if (interaction.type == "click") {
        await page.evaluate((interaction) => {
          document.querySelector(interaction.selector).click();
        }, interaction);
        console.log("Clicked on " + interaction.selector);
        if (interaction.wait) {
          await page.waitFor(interaction.wait);
        }
      }

      if (interaction.type == "keypress") {
        await page.waitFor(interaction.selector);
        console.log(
          "  Typing " + interaction.keyPress + " in " + interaction.selector
        );
        await page.type(interaction.selector, interaction.keyPress);
        if (interaction.wait) {
          await page.waitFor(interaction.wait);
        }
      }

      if (interaction.type == "scroll") {
        var selector = interaction.selector;
        await page.waitFor(selector);
        console.log("  Scrolling to " + selector);
        await page.evaluate((selector) => {
          document.querySelector(selector).scrollIntoView();
        }, selector);
        if (interaction.wait) {
          await page.waitFor(interaction.wait);
        }
      }

      if (interaction.type == "disableScrollReveal") {
        await page.evaluate(() => {
          if (typeof ScrollReveal !== "undefined") {
            ScrollReveal().destroy();
          }
        });
        if (interaction.wait) {
          await page.waitFor(interaction.wait);
        }
      }

      if (interaction.type == "disableLazyload") {
        await page.evaluate(() => {
          if (
            typeof Drupal !== "undefined" &&
            typeof Drupal.blazy !== "undefined"
          ) {
            // Force immediate load of Drupal blazy lazyload images
            Drupal.blazy.init.load(
              document.getElementsByClassName("b-lazy", true)
            );
          }
          // Force immediate load of WP Autoptimize lazyload images
          if (
            typeof lazySizes !== "undefined" &&
            typeof lazySizes.loader !== "undefined" &&
            typeof lazySizes.loader.unveil !== undefined
          ) {
            var imageArray = document.querySelectorAll(".lazyload");
            imageArray.forEach((image) => lazySizes.loader.unveil(image));
          }
        });
        if (interaction.wait) {
          await page.waitFor(interaction.wait);
        }
      }

      if (interaction.type == "pauseSlick") {
        await page.evaluate(() => {
          if (
            typeof jQuery !== "undefined" &&
            typeof jQuery.fn.slick !== "undefined"
          ) {
            var sliders = jQuery(".slick-slider");
            sliders.slick("slickPause");
          }
        });
        if (interaction.wait) {
          await page.waitFor(interaction.wait);
        }
      }
    }
  }
};
