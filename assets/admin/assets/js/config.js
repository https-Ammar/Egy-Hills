(function () {
  var html = document.getElementsByTagName("html")[0];

  var config = {
    theme: "dark",
    topbar: {
      color: "dark",
    },
    menu: {
      size: "sm-hover-active",
      color: "dark",
    },
  };

  window.defaultConfig = JSON.parse(JSON.stringify(config));
  window.config = config;

  html.setAttribute("data-bs-theme", config.theme);
  html.setAttribute("data-topbar-color", config.topbar.color);
  html.setAttribute("data-menu-color", config.menu.color);

  if (window.innerWidth <= 1140) {
    html.setAttribute("data-menu-size", "hidden");
  } else {
    html.setAttribute("data-menu-size", config.menu.size);
  }
})();
