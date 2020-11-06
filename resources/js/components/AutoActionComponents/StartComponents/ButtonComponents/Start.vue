<template>
    <div class="p-start" @click="Start()" >
        <i class="fas fa-circle-notch"></i>
        <template v-if="buttonFlag === startFlag.start">
            開始する
        </template>
        <template v-else-if="buttonFlag === startFlag.update">
            更新する
        </template>
    </div>
</template>
<script>
import { mapState } from 'vuex'
import { OK } from '../../../../util'
export default {
    data(){
        return{
            message:{
                targetList:"ターゲットリストが登録されていません。",
                unfollow:"アンフォローの設定が登録されていません。",
                offAll:"全てOFFのため実行できません。",
                error:'エラーが発生しました。再度お試しください。'
            }
        }
    },
    props:["checkbox"],
    methods:{
        async Start(){
            this.$store.commit('autoActionStart/setLoad',true)
            this.$store.commit('autoActionStart/setErrorMessage',[])
            let errors = []
            if(this.targetList.length === 0 && this.checkbox.follow === true){
                errors.push(this.message.targetList);
            }
            if(this.checkbox.unfollow === true && (this.unfollowDays == null || this.unfollowPeople  == null)){
                errors.push(this.message.unfollow);
            }
            if(this.checkbox.like === false && this.checkbox.unfollow === false && this.checkbox.follow === false){
                errors.push(this.message.offAll);
            }
            if(errors.length > 0){
                this.$store.commit('autoActionStart/setErrorMessage',errors)
                this.$store.commit('autoActionStart/setLoad',false)
                return false
            }
            const response = await axios.post('/api/setStartFlag',{
                flags:this.checkbox,
                twitter_screen_name:this.twitter_screen_name
            })
            this.$store.commit('autoActionStart/setLoad',false)
            if(response.status !== OK){
                errors.push(this.message.error);
                this.$store.commit('autoActionStart/setErrorMessage',errors)
                this.$store.commit('error/setCode',response.status)
            }else{
                //ActionState.vueに切り替える
                window.scroll(0, 0);
                this.$store.commit('twitter/isActionState',true)
            }
        }
    },
     computed:{
        ...mapState({
            twitter_screen_name: state => state.twitter.twitter_screen_name,
            targetList: state => state.autoActionSetting.targetList,
            unfollowPeople: state => state.twitter.unfollowPeople,
            unfollowDays: state => state.twitter.unfollowDays,
            buttonFlag: state => state.autoActionStart.buttonFlag,
            startFlag: state => state.autoActionStart.startFlag,
        }),
    },
}
</script>