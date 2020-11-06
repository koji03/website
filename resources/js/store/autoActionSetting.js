const state ={
    /**
     * TargetList
     */
    targetList:[],
    isTargetListOpen:false,
    targetListError:'',
    /**
     * FollowerSearch
     */
    currentForm:null,
    WordList:{
        or:[],
        ng:[],
        normal:[]
    },
    isFollowSearchOpen:false,
    followerSearchError:'',
    /**
     * unfollow
     */
    settingUnfollowDays:null,
    settingUnfollowPeople:null,
    isUnfollowOpen:false,
    daysError:'',
    peopleError:'',
    /**
     * like
     */
    currentLikeForm:null,
    LikeWordList:{
        or:[],
        ng:[],
        normal:[]
    },
    isLikeOpen:false,
    LikeError:'',
    /**
     * loading
     */
    accountListLoad:false,
    targetListFormLoad:false,
    followerSearchLoad:false,
    unfollowLoad:false,
    likeLoad:false,
    errorLoad:false,
}
const mutations = {
    /**
     * targetlist
     */
    setIsTargetListOpen(state){
        state.isTargetListOpen = !state.isTargetListOpen
    },
    setTargetListFormLoad(state,bool){
        state.targetListFormLoad = bool
    },
    setTargetList(state,array){
        state.targetList = array
    },
    setTargetListError(state,message){
        state.targetListError = message
    },

    /**
     * followerSearch
     */
    setIsFollowSearchOpen(state){
        state.isFollowSearchOpen = !state.isFollowSearchOpen
    },
    setFollowerSearchError(state,message){
        state.followerSearchError = message
    },
    setFollowerSearchLoad(state,bool){
        state.followerSearchLoad = bool
    },
    currentForm(state,data){
        state.currentForm = data
    },
    orList(state,data){
        state.WordList.or = state.WordList.or.concat(data);
    },
    ngList(state,data){
        state.WordList.ng = state.WordList.ng.concat(data);
    },
    normalList(state,data){
        state.WordList.normal = state.WordList.normal.concat(data);
    },
    ResetWordList(state){
        for(let i in state.WordList){
            state.WordList[i] = []
        }
    },
    delWordList(state,list){
        if(list.type == 'or'){
            state.WordList.or = state.WordList.or
            .filter(function(word) {
                return word !== list.word;
                });;
        }
        if(list.type  == 'ng'){
            state.WordList.ng = state.WordList.ng
            .filter(function(word) {
                return word !== list.word;
                });;

        }
        if(list.type  == 'normal'){
            state.WordList.normal = state.WordList.normal
            .filter(function(word) {
                return word !== list.word;
                });;
        }
    },
    /**
     * unfollow
     */
    setUnfollowOpen(state,bool){
        state.isUnfollowOpen = !state.isUnfollowOpen
    },
    setSettingUnfollowDays(state,data){
        state.settingUnfollowDays = data
    },
    setSettingUnfollowPeople(state,data){
        state.settingUnfollowPeople = data
    },
    setDaysError(state,message){
        state.daysError = message
    },
    setPeopleError(state,message){
        state.peopleError = message
    },
    setUnfollowLoad(state,bool){
        state.unfollowLoad = bool
    },

    /**
     * like
     */
    currentLikeForm(state,data){
        state.currentLikeForm = data
    },
    setLikeOpen(state){
        state.isLikeOpen = !state.isLikeOpen
    },
    ResetLikeWordList(state){
        for(let i in state.LikeWordList){
            state.LikeWordList[i] = []
        }
    },
    setLikeError(state,message){
        state.LikeError = message
    },
    setLikeLoad(state,bool){
        state.likeLoad = bool
    },
    likeOrList(state,data){
        state.LikeWordList.or = state.LikeWordList.or.concat(data);
    },
    likeNgList(state,data){
        state.LikeWordList.ng = state.LikeWordList.ng.concat(data);
    },
    likeNormalList(state,data){
        state.LikeWordList.normal = state.LikeWordList.normal.concat(data);
    },
    
    delLikeWordList(state,list){
        if(list.type == 'or'){
            state.LikeWordList.or = state.LikeWordList.or
            .filter(function(word) {
                return word !== list.word;
                });;
        }
        if(list.type  == 'ng'){
            state.LikeWordList.ng = state.LikeWordList.ng
            .filter(function(word) {
                return word !== list.word;
                });;

        }
        if(list.type  == 'normal'){
            state.LikeWordList.normal = state.LikeWordList.normal
            .filter(function(word) {
                return word !== list.word;
                });;
        }
    },
    
}

const actions = {
    //followerSearch
    setWordList(context,list){
        for(let i in list){
            if(list[i].length <=0){
                continue;
            }
            if(i == 'or'){
                context.commit('orList',list[i])
            }
            if(i == 'ng'){
                context.commit('ngList',list[i])
            }
            if(i == 'normal'){
                context.commit('normalList',list[i])
            }
        }
    },
    //like
    setLikeWordList(context,list){
        for(let i in list){
            if(list[i].length <=0){
                continue;
            }
            if(i == 'or'){
                context.commit('likeOrList',list[i])
            }
            if(i == 'ng'){
                context.commit('likeNgList',list[i])
            }
            if(i == 'normal'){
                context.commit('likeNormalList',list[i])
            }
        }
    },
}

const getters = {
    /**
     * FollowSearch
     */
    getOr: state => {
        return state.WordList.or
    },
    getNg: state => {
        return state.WordList.ng
    },
    getNormal: state => {
        return state.WordList.normal
    },

    /**
     * like
     */
    getLikeOr: state => {
        return state.LikeWordList.or
    },
    getLikeNg: state => {
        return state.LikeWordList.ng
    },
    getLikeNormal: state => {
        return state.LikeWordList.normal
    },
}

export default {
    namespaced: true,
    state,
    getters,
    mutations,
    actions
}