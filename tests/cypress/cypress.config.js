const { defineConfig } = require("cypress");

module.exports = defineConfig({
    env: {
        type: "local",
    },
    viewportWidth: 1920,
    viewportHeight: 1080,
    video: false,
      e2e: {
        setupNodeEvents(on, config) {
          // implement node event listeners here
        },
          baseUrl: "http://127.0.0.1:8000/",
      },
});
