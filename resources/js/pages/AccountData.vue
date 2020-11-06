<template>
    <div class="p-data u-padding">
            <div class="p-data__top">
            <div class="p-data__top__title">{{this.$route.params.screen_name}}</div>
        </div>
        <Loading v-if="!parameCheckFlag"/>
        <template v-else>
            <LiveData/>
            <SettingData/>
        </template>
    </div>
</template>
<script>
import { mapState } from 'vuex'
import Loading from '../components/Loading.vue'
import { OK } from '../util'
import SettingData from '../components/AccountDataComponents/SettingData'
import LiveData from '../components/AccountDataComponents/LiveData'
export default {
    data(){
        return{
            parameCheckFlag:false
        }
    },
    components:{
        Loading,
        SettingData,
        LiveData
    },
    methods:{
        //各種データを取得する。
        async getData(){
        this.parameCheckFlag = false
            if(this.$route.params.screen_name){
                this.$store.commit('autoActionSetting/setTargetListFormLoad',true)
                this.$store.commit('autoActionSetting/setFollowerSearchLoad',true)
                this.$store.commit('autoActionSetting/setUnfollowLoad',true)
                this.$store.commit('autoActionSetting/setLikeLoad',true)
                await this.parameCheck(this.$route.params.screen_name)
            } 
        },
        //routeパラメータが変更され自分以外のツイッターアカウントにされていないかをチェックしている。
        //違う場合はエラーが起き、エラーページに遷移される。
        async parameCheck($twitter_screen_name){
            const response = await axios.post('/api/parameCheck',
            {
                'twitter_screen_name':$twitter_screen_name
            })
            if(response.status !== OK){
                this.$store.commit('error/setCode',response.status)
                exit;
            }
            this.parameCheckFlag = true
        },
        
    },
    created(){
        this.$store.commit('twitter/setAccount',this.$route.params.screen_name)
        this.getData()
        window.scroll(0, 0);
    },
    watch:{
        '$route':async function(){
            this.getData()
        }
    }
}
</script>