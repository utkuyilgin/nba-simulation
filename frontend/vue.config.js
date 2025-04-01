const { defineConfig } = require('@vue/cli-service')
const {dotEnv} = require('dotenv')

module.exports = defineConfig({
  transpileDependencies: true,
  pluginOptions: {
    dotenv: {
      systemvars: true, // Sistem değişkenlerini oku
    },
  },
})
