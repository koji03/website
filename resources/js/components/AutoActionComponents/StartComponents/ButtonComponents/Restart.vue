<template>
    <div class="p-start u-margin__restart--top" @click="Start()" >
        <i class="fas fa-circle-notch"></i>
        <template>
            再開する
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
            this.$store.commit('autoActionStart/setErrorMessage',[])
            this.$store.commit('autoActionStart/setLoad',true)
            let errors = []
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
            //エラーで処理が止まっているのでerror_flagをfalseにして再開できるようにする。
            const response = await axios.put('/api/Restart',{
                twitter_screen_name:this.twitter_screen_name,
            })
            if(response.status !== OK){
                errors.push(this.message.error);
                this.$store.commit('autoActionStart/setErrorMessage',errors)
                this.$store.commit('error/setCode',response.status)
            }else{
                //ActionState.vueに切り替える
                this.$store.commit('autoActionStart/setLoad',false)
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
        }),
    },
}
</script>