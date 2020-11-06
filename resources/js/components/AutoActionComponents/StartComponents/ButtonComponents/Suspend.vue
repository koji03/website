<template>
<div>
    <div class="spinner">
        <div class="cube1"></div>
        <div class="cube2"></div>
    </div>
    <div class="p-following__message">
        <div>
            自動機能を終了することができます。<br>
        </div>
    </div>
    <div class="p-start"  @click="suspend()">
        <i class="fas fa-circle-notch"></i>
        終了する。
    </div>
</div>

</template>

<script>
import { OK } from '../../../../util'
import { mapState } from 'vuex'
export default {
    data(){
        return{
            message:{
                confirm:'自動機能を終了させますか？',
                miss:'自動機能の終了に失敗しました。　ブラウザを更新後、再度試してください。',
                end:'自動機能が終了しました。'
            }
        }
    },
    methods:{
        //自動機能を終了させる
        async suspend(){
            if (!confirm(this.message.confirm)){
                return
            }
            this.$store.commit('autoActionStart/setLoad',true)
            const response = await axios.put('/api/stopAction',{
                twitter_screen_name:this.twitter_screen_name
            })
            this.$store.commit('autoActionStart/setLoad',false)
            if(response.status !== OK){
                this.$store.commit('error/setCode',response.status)
                this.$store.commit('message/setContent', {
                    content: this.message.miss,
                    timeout: 8000
                    })
            }else{
                this.$store.commit('message/setContent', {
                    content: this.message.end,
                    timeout: 5000
                    })
                    this.$router.push('/kamitter/home')
            }
        }
    },
    computed:{
        ...mapState({
            startFlag: state => state.autoActionStart.startFlag,
            twitter_screen_name: state => state.twitter.twitter_screen_name,
        }),
    },
}
</script>