const state ={
    modalShow: false,
    settingDate:'',
    settingTime:'',
    tweetText:'',
    scheduledTweetsId:'',
    uploadFileList:[],
    twitterScreenName:'',
    errorFlag:false,
    dataErrorFlag:false
}
const mutations = {
    setErrorFlag(state,bool){
        state.errorFlag = bool
    },
    setDataErrorFlag(state,bool){
        state.dataErrorFlag = bool
    },
    isModalShow(state){
        state.modalShow = !state.modalShow
    },
    setScreenName(state,data){
        state.twitterScreenName = data
    },
    setSettingDate(state,data){
        state.settingDate = data
    },
    setSettingTime(state,data){
        state.settingTime = data
    },
    setTweetText(state,data){
        state.tweetText = data
    },
    setScheduledTweetsId(state,data){
        state.scheduledTweetsId = data
    },
    setUploadFileList(state,data){
        state.uploadFileList.push(data)
    },
    resetUploadFileList(state){
        state.uploadFileList = []
    },
    deleteUploadFileList(state,index){
        state.uploadFileList.splice(index, 1);
    }
}

const actions = {
    //予約ツイートで使う。年月日を保存する。
    setDate(context,data){
        var day = data.day;
        var month = data.month-1;
        var year = data.year;
        var date = new Date(year,month,day)
        context.commit('setSettingDate',date)
    },
    //こっちは時分秒を保存する用
    setTime(context,data){
        var hour = data.hour
        var minute = data.minute
        var date = new Date(2000,1,1,hour,minute,0)
        context.commit('setSettingTime',date)
    },
    resetSetting(context,data){
        context.commit('setSettingTime','')
        context.commit('setSettingDate','')
        context.commit('setTweetText','')
        context.commit('setScheduledTweetsId','')
        context.commit('resetUploadFileList')
    }
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