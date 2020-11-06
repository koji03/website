<template>
        <div>
        <Loading v-if="load"/>
        <div class="p-scheduled__wrap u-margin__scheduled__wrap" v-if="!load && scheduledFlag">
            <div class="p-scheduled__list">
                <div v-for="i in tweetList" :key="i.id" class="p-scheduled__list__item" @click="selectTweet(i.tweet_text,i.scheduled_date,i.id)">
                    {{i.scheduled_date}}
                    {{i.tweet_text}}</div>
            </div>
        </div>
        <scheduledPost v-if="!load && !scheduledFlag"/>
        <div class="c-error" >{{error}}</div>
    </div>
</template>
<script>
import { mapState } from 'vuex'
import { UNPROCESSABLE_ENTITY, OK} from '../../../util'
import Loading from '../../Loading'
import scheduledPost from './scheduledPost'


export default {
    data(){
        return{
            error:'',
            load:false,
            tweetList:[],
            scheduledFlag:true,
            message:{
                error:'取得に失敗しました。ブラウザの更新をしてください。',
                unselected:'ツイッターアカウントが選択されていないので取得できません。',
                miss:'一覧の取得に失敗しました。取得に失敗しました。ブラウザの更新をしてください。'
            }
        }
    },
    components:{
        Loading,
        scheduledPost
    },
    methods:{
        //予約済みのツイート一覧から選択したツイートのデータをmodal.jsに格納する。
        async selectTweet(text,time,id){
            this.load = true
            await this.$store.dispatch('modal/resetSetting')
            await this.$store.commit('modal/setTweetText',text)
            await this.$store.commit('modal/setSettingTime',time)
            await this.$store.commit('modal/setSettingDate',time)
            await this.$store.commit('modal/setScheduledTweetsId',id)
            const response = await axios.post('/api/photos',{scheduled_tweet_id:id})
            this.load = false
            if(response.status !== OK){
                this.error = this.message.error
                return false
            }
            await response.data.forEach(element => {
                this.$store.commit('modal/setUploadFileList',element)
            });
            this.scheduledFlag = !this.scheduledFlag
    }
    },
    computed:{
         ...mapState({
            twitterScreenName: state => state.modal.twitterScreenName
        }),
    },
    async created(){
        this.error =""
        if(!this.twitterScreenName){
            this.error =this.message.unselected
            return false
        }
        this.load = true
        const response = await axios.post('/api/getScheduledList',{twitter_screen_name:this.twitterScreenName})
        this.load = false
        if(response.status !== OK){
            this.error = this.message.miss
        }
        this.tweetList = response.data
    },

}
</script>