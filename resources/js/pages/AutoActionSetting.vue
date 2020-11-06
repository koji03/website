<template>
<div>
    <div class="c-setting">
    <Loading v-if="!createFlag"/>
    <template  v-if="createFlag">
        <span v-show="isActionState">
            <ActionState/>
        </span>
        <span v-show="!isActionState">
            <ActionStart/>
        </span>
    </template>
    </div>
</div>
</template>

<script>
import { mapState } from 'vuex'
import ActionState from '../components/AutoActionComponents/ActionState'
import ActionStart from '../components/AutoActionComponents/ActionStart'
import Loading from '../components/Loading'
import { OK } from '../util'
export default {
    data(){
        return{
            createFlag:false,
        }
    },
    components:{
        ActionState,
        ActionStart,
        Loading
    },
    methods:{
        //routeパラメータが改変され自分が登録していないツイッターアカウントにされていないかをチェック。
        //違う場合はエラーが起き、エラーページに遷移される。
        async checkParam(){
            const response = await axios.post('/api/parameCheck',
            {
                'twitter_screen_name':this.$route.params.screen_name
            })
            if(response.status !== OK){
                this.$store.commit('error/setCode',response.status)
                exit;
            }
            this.$store.commit('twitter/setAccount',this.$route.params.screen_name)
        },
        //全てのアクションフラグを取得し、trueになっているのを調べる。
        //trueになっているフラグで表示するボタンを変えるようにしている。
        async getActionFlags(){
            const response = await axios.post('/api/getActionFlag',{
                twitter_screen_name:this.twitter_screen_name
            })
            if(response.status !== OK){
                this.$store.commit('error/setCode',response.status);
                return false
            }
            let startFlag = true;
            for(let key in response.data){
                switch(key){
                    case 'auto_restart_flag':
                        if(response.data[key] === 1){
                        this.$store.commit('autoActionStart/setButtnFlag',this.startFlag.suspend)
                        startFlag = false
                        return false
                    }
                    case 'error_flag':
                        if(response.data[key] === 1){
                        this.$store.commit('autoActionStart/setButtnFlag',this.startFlag.restart)
                        startFlag = false
                        return false
                    }
                    break;
                    case 'follow_flag':
                    case 'follow_target_flag':
                    case 'unfollow_flag':
                    case 'unfollow_target_flag':
                    case 'like_flag':
                        if(response.data[key] === 1){
                        this.$store.commit('autoActionStart/setButtnFlag',this.startFlag.suspend)
                        startFlag = false
                    }
                    break;
                }
            }
            if(startFlag){
                this.$store.commit('autoActionStart/setButtnFlag',this.startFlag.start)
            }
        },
        
    },
    computed:{
        ...mapState({
            isActionState: state => state.twitter.isActionState,
            twitter_screen_name: state => state.twitter.twitter_screen_name,
            startFlag: state => state.autoActionStart.startFlag,
        }),
    },
    async created(){
        this.createFlag = false
        window.scroll(0, 0);
        await this.checkParam()
        await this.getActionFlags()
        this.$store.dispatch('error/errorCheck')
        this.createFlag = true
    },
    watch:{
        '$route':function(){
            this.$store.commit('twitter/setAccount',this.$route.params.screen_name)
        }
    },
}
</script>