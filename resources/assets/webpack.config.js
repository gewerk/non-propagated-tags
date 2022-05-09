const { getConfig } = require('@craftcms/webpack');

module.exports = getConfig({
  type: null,
  context: __dirname,
  config: {
    entry: {
      'non-propagated-tag-select-input': './non-propagated-tag-select-input.js',
    },
  },
});
