import { OK } from '../util'
const state = {
    code: null, //App.vueで使う
    errorAccountList:[],
    errorAccountListFlag:false,
    autoActionAccountList:[],
    autoActionFlag:false
  }
  
  const mutations = {
    setCode (state, code) {
      state.code = code
    },
    setErrorAccount(state, array){
      state.errorAccountList = array
    },
    setErrorAccountListFlag(state, bool){
      state.errorAccountListFlag = bool
    },
    setAutoActionAccount(state, array){
      state.autoActionAccountList = array
    },
    setAutoActionFlag(state, bool){
      state.autoActionFlag = bool
    },
  }

  const actions = {
    //エラーフラグがtrueかfalseを調べ、
    //trueのアカウントをerrorAccountListに格納する。
    //自動再開されるアカウントをautoActionAccountListに格納する。
    async errorCheck(context,data){
      context.commit('twitter/setErrorLoad', true, { root: true })
      const array = []
      const autoArray = []
      context.commit('setErrorAccountListFlag',false)
      context.commit('setAutoActionFlag',false)
      const response = await axios.get('/api/getErrorFlag')
      if(response.status !== OK){
          context.commit('twitter/setErrorLoad', false, { root: true })
          return false
      }

      const errors = response.data.filter(item => item.error_flag === 1)
      errors.forEach(element => {
          array[element.twitter_screen_name] = true
          context.commit('setErrorAccountListFlag',true)
      });
      const autoFlag = response.data.filter(item => item.auto_restart_flag === 1)
      autoFlag.forEach(element => {
          autoArray[element.twitter_screen_name] = true
          context.commit('setAutoActionFlag',true)
      });
      context.commit('setErrorAccount',array)
      context.commit('setAutoActionAccount',autoArray)
      context.commit('twitter/setErrorLoad', false, { root: true })
      }
      
  }
  
  export default {
    namespaced: true,
    state,
    actions,
    mutations
  }