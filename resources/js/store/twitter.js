const state = {
    siteTitle:'KamiTTer',
    twitter_screen_name:null,
    activeAccountList:true,
    isActionState:false,
    unfollowPeople:null,
    unfollowDays:null,
    /**
     * other
     */
    accountList:[],
    startAction:false,
    errorLoad:false,
}

const getters = {
    accountName: state =>  state.twitter_screen_name,
    /**
     * ohter
     */
    getIsActionState:state =>{
        return state.isActionState
    },
}

const mutations = {
    setUnfollowPeople(state,data){
        state.unfollowPeople = data
    },
    setUnfollowDays(state,data){
        state.unfollowDays = data
    },
    setStartAction(state,data){
        state.startAction = data
    },
    isActionState(state,bool){
        state.isActionState = bool
    },
    
    /**
     * AccountList
     */
    isList(state,bool){
        state.activeAccountList = !state.activeAccountList
    },

    resetAccountList(state){
        state.accountList = []
    },
    setAccountList(state,data){
        state.accountList.push(data)
    },
    setAccount (state,data){
        state.twitter_screen_name = data
    },
    /**
     * Loading
     */
    setErrorLoad(state,bool){
        state.errorLoad = bool
    }
}

const actions = {
    async getTwitterAccountList(){
        //ロード画面を出す
        this.load = true
        const response = await axios.get('/api/getTwitterIdList');
        this.load = false
        if(response.status !== OK){
            this.$store.commit('error/setCode',response.status)
            return false
        }
        //アカウントリストの配列をリセット後、１つずつpushで入れる。
        this.$store.commit('twitter/resetAccountList')
        for(let item in response.data){
            this.$store.commit('twitter/setAccountList',response.data[item].twitter_screen_name)
        }
    },
}

export default {
    namespaced: true,
    state,
    getters,
    mutations,
    actions
}