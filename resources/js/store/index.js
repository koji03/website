import Vue from 'vue'
import Vuex from 'vuex'

import message from './message'
import auth from './auth'
import twitter from './twitter'
import error from './error'
import modal from './modal'
import autoActionStart from './autoActionStart'
import autoActionSetting from './autoActionSetting'
import form from './form'
Vue.use(Vuex)

const store = new Vuex.Store({
    modules:{
        auth,
        twitter,
        error,
        modal,
        message,
        autoActionStart,
        autoActionSetting,
        form
    }
})
export default store