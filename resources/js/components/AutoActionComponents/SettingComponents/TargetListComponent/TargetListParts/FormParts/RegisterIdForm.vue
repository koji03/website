<template>
    <div>
        <form v-if="!targetListFormLoad" action="" class="c-setting__form" @submit.prevent novalidate>
            <input type="text" placeholder="TwitterJP" class="c-setting__form__text" v-model="targetId" >
            <input type="button" value="登録" @click="addId" class="c-setting__form__button">
        </form>
        <div v-if="!targetListFormLoad" class="u-margin--error c-error">{{ targetListError }}</div>
    </div>
</template>

<script>
import { mapState } from 'vuex'
import {OK}  from '../../../../../../util'
export default {
    data(){
        return{
            targetId:'',
            errorMessage:{
                halfwidth_alphanumeric:'@マークを含めずにツイッターIDを入力してください',
                same_id:'同じツイッターIDが含まれています。',
                failed_register:'登録に失敗しました。ブラウザを更新して再度登録をしてください。'
            }
        }
    },
    methods:{
        //入力したツイッターIDをDBとautoActionSettingに保存する。そのときにバリデーションもしている。
        async addId(){
            this.$store.commit('autoActionSetting/setTargetListFormLoad',true)
            this.$store.commit('autoActionSetting/setTargetListError','')
            //バリデーション @マークを含めずにローマ字と数字のみ 15文字以内
            if(this.targetId.match(/^([a-zA-Z0-9_]{1,15})$/) === null){
                this.$store.commit('autoActionSetting/setTargetListFormLoad',false)
                this.$store.commit('autoActionSetting/setTargetListError',this.errorMessage.halfwidth_alphanumeric)
                return false
            }
            if(this.targetList.indexOf(this.targetId) >= 0){
                this.$store.commit('autoActionSetting/setTargetListFormLoad',false)
                this.$store.commit('autoActionSetting/setTargetListError',this.errorMessage.same_id)
                return false
            }
            const response = await axios.post('/api/registerUserTargetList',{
                target_id:this.targetId,
                twitter_screen_name:this.twitter_screen_name
            })
            this.$store.commit('autoActionSetting/setTargetListFormLoad',false)
            this.$store.commit('autoActionSetting/setTargetListError','')
            if(response.status !== OK){
                this.$store.commit('autoActionSetting/setTargetListError',this.errorMessage.failed_register)
                this.targetId=''
                return false
            }
            const array = this.targetList;
            array.push(this.targetId)
            this.$store.commit('autoActionSetting/setTargetList',array)
            this.targetId=''

        },
    },
    computed:{
        ...mapState({
            twitter_screen_name: state => state.twitter.twitter_screen_name,
            targetList: state => state.autoActionSetting.targetList,
            targetListError: state => state.autoActionSetting.targetListError,
            targetListFormLoad: state => state.autoActionSetting.targetListFormLoad,

        })
    }
}
</script>