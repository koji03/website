<template>
<div>
    <div class="p-scheduled__form--top">
        <div class="p-scheduled__form__textarea">
            <div class="p-scheduled__form__text">
                <span @click="changeFlag" class="p-scheduled__form__text--pointer">ツイート</span>
                <span @click="changeFlag" class="p-scheduled__form__text--pointer">画像</span>
            </div>
            <div v-if="flag">
                <div class="p-scheduled__form__tweet">
                    <textarea name="" id="" v-model="text" class="p-scheduled__form__tweet__textarea u-margin__textarea--top"></textarea>
                </div>
                <div>
                    {{maxNum-currentNum}}
                </div>
            </div>
            <div v-if="!flag">
                <ScheduledFile/>
            </div>
        </div>
    </div>
    <div class="c-error u-margin--error--scheduled">{{error}}</div>
</div>
</template>
<script>
import ScheduledFile from './scheduledFile'
export default {
    data(){
        return{
            error:'',
            text:'',
            maxNum:140,
            currentNum:0,
            flag:true,
            message:{
                num:'文字数がオーバーしています。'
            }
        }
    },
    components:{
        ScheduledFile,
    },
    methods:{

    },
    watch: {
        //バリデーションチェック
        text(text) {
            this.currentNum = text.length
            this.$store.commit('modal/setTweetText',this.text)
            if(this.currentNum >140){
                this.error = this.message.num
                this.$store.commit('modal/setErrorFlag',true)
            }else{
                this.error = ''
                this.$store.commit('modal/setErrorFlag',false)
            }
        }
    },
    methods: {
        changeFlag(){
            this.flag = !this.flag
        }
    },
    created(){
        if(this.$store.state.modal.tweetText.length){
            this.text = this.$store.state.modal.tweetText
        }
    },
}
</script>