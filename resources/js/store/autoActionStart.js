const state ={
    errorMessage:[],
    startFlag:{
        start:0,
        update:1,
        restart:2,
        suspend:3
    },
    buttonFlag:'',//↑の0123の数字が入る。表示するボタンの判定
    startLoad:false
}
const mutations = {
    setErrorMessage(state,message){
        state.errorMessage = message
    },
    setButtnFlag(state,startFlag){
        state.buttonFlag = startFlag
    },
    setLoad(state,bool){
        state.startLoad = bool
    }
}

const actions = {
}

const getters = {
}

export default {
    namespaced: true,
    state,
    getters,
    mutations,
    actions
}