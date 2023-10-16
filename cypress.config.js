const setupNodeEvents = require( './cypress/plugins/index.js' );
module.exports = {
  e2e: {
    baseUrl: "http://localhost/",
    specPattern: ["**/*.feature", "**/*.spec.js"],
    video: false,
    fixturesFolder: "cypress/fixtures",
    screenshotsFolder: "cypress/screenshots",
    videosFolder: "cypress/videos",
    supportFile: "cypress/support/index.js",
    viewportWidth: 1280,
    viewportHeight: 800,
    setupNodeEvents,
  },
  wp: {
    version: ["6.3"],
    phpVersion: 8.0,
    plugins: ["./"],
    themes: [],
    muPlugins: {
      vip: true
    },
    uploadsPath: "cypress/uploads",
    seedsPath: "cypress/seeds",
    configFile: "./wp-cypress-config.php",
    config: {
        WP_DEBUG: false
    }
  }
};

