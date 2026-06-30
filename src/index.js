import barba from "@barba/core";
import fullpage from "fullpage.js";
import Flickity from "flickity";

document.addEventListener("DOMContentLoaded", function () {
  const topCover = document.querySelector(".transition-cover.top");
  const bottomCover = document.querySelector(".transition-cover.bottom");
  const menuButton = document.getElementById("menu-button-mobile");

  if (menuButton) {
    menuButton.addEventListener("click", () => {
      const isOpen = document.body.classList.toggle("menu-out");
      menuButton.setAttribute("aria-expanded", isOpen ? "true" : "false");
      menuButton.setAttribute(
        "aria-label",
        isOpen ? "Menü schließen" : "Menü öffnen"
      );
    });
  }

  function setGAOptOut() {
    const disableStr = "ga-disable-G-7ZP13JV1C8";
    document.cookie =
      disableStr + "=true; expires=Thu, 31 Dec 2099 23:59:59 UTC; path=/";
    window[disableStr] = true;
    alert("Google Analytics tracking wurde für diesen Browser deaktiviert.");
  }

  function checkGAOptOut() {
    const disableStr = "ga-disable-G-7ZP13JV1C8";
    if (document.cookie.indexOf(disableStr + "=true") > -1) {
      window[disableStr] = true;
    }
  }

  function initializeOptOutButton() {
    const optOutButton = document.getElementById("ga-optout");
    if (!optOutButton || optOutButton.dataset.gaBound === "true") return;
    optOutButton.dataset.gaBound = "true";
    optOutButton.addEventListener("click", setGAOptOut);
    checkGAOptOut();
  }

  function parseNextDocument(nextHtml) {
    if (!nextHtml) return null;
    return new DOMParser().parseFromString(nextHtml, "text/html");
  }

  function updateBodyClasses(nextHtml) {
    const doc = parseNextDocument(nextHtml);
    if (!doc?.body.className) return;
    document.body.className = doc.body.className;
  }

  function syncWpLayoutStyles(nextHtml) {
    const doc = parseNextDocument(nextHtml);
    if (!doc) return;

    document.querySelectorAll("style[data-barba-wp]").forEach((el) => el.remove());

    doc.querySelectorAll("head style").forEach((style) => {
      const css = style.textContent;
      if (
        css.includes("wp-container-") ||
        css.includes(".wp-block-columns") ||
        css.includes("is-layout-")
      ) {
        const clone = style.cloneNode(true);
        clone.setAttribute("data-barba-wp", "");
        document.head.appendChild(clone);
      }
    });
  }

  function cleanupFullpage() {
    document.documentElement.classList.remove(
      "fp-enabled",
      "fp-scrollable",
      "fp-responsive",
      "fp-scroll-mac"
    );
    document.body.classList.remove("fp-enabled", "fp-scrollable", "fp-responsive");
    document.body.style.overflow = "";
    document.documentElement.style.overflow = "";

    try {
      if (typeof fullpage_api !== "undefined" && fullpage_api) {
        fullpage_api.destroy("all");
      }
    } catch (error) {
      console.error("Failed to destroy fullpage:", error);
    }
  }

  function initInstagramEmbeds() {
    if (typeof instgrm !== "undefined") {
      instgrm.Embeds.process();
      return;
    }

    if (document.getElementById("instagram-embed-script")) return;

    const script = document.createElement("script");
    script.id = "instagram-embed-script";
    script.src = "https://www.instagram.com/embed.js";
    script.async = true;
    script.onload = function () {
      if (typeof instgrm !== "undefined") {
        instgrm.Embeds.process();
      }
    };
    document.body.appendChild(script);
  }

  if (!topCover || !bottomCover) {
    initializeOptOutButton();
    return;
  }

  barba.init({
    sync: false,
    transitions: [
      {
        async once() {
          topCover.style.transform = "translateY(-100%)";
          bottomCover.style.transform = "translateY(100%)";
        },
        async enter() {
          window.scrollTo(0, 0);
          topCover.style.transform = "translateY(-100%)";
          bottomCover.style.transform = "translateY(100%)";
        },
        async leave(data) {
          const done = this.async();
          topCover.style.transform = "translateY(-50%)";
          bottomCover.style.transform = "translateY(50%)";
          setTimeout(() => {
            data.next.namespace === "home" ? window.location.reload() : done();
          }, 900);
        },
        async afterEnter(data) {
          updateMenu(data.next.url.path);
          if (data.next.namespace !== "home") {
            topCover.style.transform = "translateY(-100%)";
            bottomCover.style.transform = "translateY(100%)";
          }
          initializeOptOutButton();
        },
      },
    ],
    views: [
      {
        namespace: "home",
        beforeEnter() {
          import("./js/_service_bubbles.js");
          const headervideo = document.getElementById("headervideo");
          const footerWrapper = document.getElementById("footer-wrapper");
          const footer = document.querySelector("footer");

          if (footer && footerWrapper) {
            footerWrapper.appendChild(footer);
          }

          if (!document.querySelector("#fullpage")) return;

          new fullpage("#fullpage", {
            navigation: true,
            resize: true,
            recordHistory: false,
            scrollingSpeed: 500,
            scrollOverflow: true,
            credits: {
              enabled: false,
            },
            onLeave: async function (origin, destination) {
              if (headervideo) {
                destination.index === 0
                  ? headervideo.play()
                  : headervideo.pause();
              }

              if (destination.item.id === "fp-service_bubbles") {
                try {
                  const module = await import("./js/_service_bubbles.js");
                  module.updateCircles();
                } catch (error) {
                  console.error("Failed to load updateCircles:", error);
                }
              }
            },
            afterLoad: function (origin, destination, direction) {
              if (!footer) return;
              if (destination.index === 2 && direction === "down") {
                footer.classList.add("show-footer");
              } else if (direction === "up" && origin.index === 2) {
                footer.classList.remove("show-footer");
              }
            },
          });

          const slider = document.querySelector(".projects_slider");
          if (slider) {
            new Flickity(slider, {
              cellAlign: "center",
              draggable: false,
              contain: false,
              pageDots: false,
              adaptiveHeight: true,
              wrapAround: true,
              setGallerySize: false,
              selectedAttraction: 0.2,
              friction: 0.8,
            });
          }
        },
        beforeLeave() {
          const footer = document.querySelector("footer");
          if (footer) document.body.appendChild(footer);
          cleanupFullpage();
        },
      },
      {
        namespace: "page",
      },
    ],
  });

  barba.hooks.beforeEnter((data) => {
    updateBodyClasses(data.next.html);
    syncWpLayoutStyles(data.next.html);
    cleanupFullpage();
  });

  barba.hooks.afterEnter(() => {
    window.dispatchEvent(new Event("resize"));
    initializeOptOutButton();
    initInstagramEmbeds();
  });

  barba.hooks.afterLeave(() => {
    history.scrollRestoration = "manual";
    document.body.classList.remove("menu-out");
    if (menuButton) {
      menuButton.setAttribute("aria-expanded", "false");
      menuButton.setAttribute("aria-label", "Menü öffnen");
    }
  });

  function updateMenu() {
    document.querySelectorAll("#menu-main-menu .menu-item").forEach((item) => {
      const link = item.querySelector("a");
      if (!link) return;
      link.getAttribute("href") === window.location.href
        ? item.classList.add("current-menu-item", "current_page_item")
        : item.classList.remove("current-menu-item", "current_page_item");
    });
  }

  initializeOptOutButton();
  updateMenu();
});
