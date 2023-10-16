const cucumber = require('cypress-cucumber-preprocessor').default;
const wpCypressPlugin = require('@bigbite/wp-cypress/lib/cypress-plugin');

module.exports = (on, config) => {

    on('file:preprocessor', cucumber());
    on('task', {
        log (message) {
            console.log(message);
            return null
        }
    });

    return wpCypressPlugin(on, config);
}
