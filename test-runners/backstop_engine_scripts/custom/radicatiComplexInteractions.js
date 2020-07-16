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
        await page.waitFor(interaction.selector);
        await page.hover(interaction.selector);
        console.log("  Hovering over " + interaction.selector);
        if (interaction.wait) {
          await page.waitFor(interaction.wait);
        }
      }

      if (interaction.type == "click") {
        await page.waitFor(interaction.selector);
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
        await page.type(interaction.selector, interaction.keyPress);
        console.log(
          "  Typing " + interaction.keyPress + " in " + interaction.selector
        );
        if (interaction.wait) {
          await page.waitFor(interaction.wait);
        }
      }

      if (interaction.type == "scroll") {
        var selector = interaction.selector;
        await page.waitFor(selector);
        await page.evaluate((selector) => {
          document.querySelector(selector).scrollIntoView();
        }, selector);
        console.log("  Scrolled to " + selector);
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
            // start would be sliders.slick("slickPlay");
          }
        });
        if (interaction.wait) {
          await page.waitFor(interaction.wait);
        }
      }

      if (interaction.type == "pauseOwl") {
        await page.evaluate(() => {
          if (
            typeof jQuery !== "undefined" &&
            typeof jQuery.fn.owlCarousel !== "undefined"
          ) {
            var sliders = jQuery(".owl-carousel");
            sliders.trigger("stop.owl.autoplay");
            // start would be sliders.trigger('play.owl.autoplay');
          }
        });
        if (interaction.wait) {
          await page.waitFor(interaction.wait);
        }
      }

      if (interaction.type == "applyJQueryCode") {
        await page.evaluate((interaction) => {
          if (typeof jQuery !== "undefined") {
            var elements = jQuery(interaction.selector);
            if (interaction.expand == true) {
              eval("elements." + interaction.code);
            } else {
              var element = jQuery(elements[0]);
              // The internet agrees -- using eval is "killing kittens" level bad :P
              // But... it works :shrug:
              eval("element." + interaction.code);
            }
          }
        }, interaction);
        if (interaction.wait) {
          await page.waitFor(interaction.wait);
        }
      }

      if (interaction.type == "applyVanillaCode") {
        await page.evaluate((interaction) => {
          if (interaction.expand == true) {
            var elements = document.querySelectorAll(interaction.selector);
            elements.forEach(function (element) {
              // The internet agrees -- using eval is "killing kittens" level bad :P
              // But... it works :shrug:
              eval("element." + interaction.code);
            }, interaction);
          } else {
            element = document.querySelector(interaction.selector);
            eval("element." + interaction.code);
          }
        }, interaction);
        if (interaction.wait) {
          await page.waitFor(interaction.wait);
        }
      }
    }
  }
};
