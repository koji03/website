<template>
    <div action="" class="c-setting__form">
            <input v-model="list.or[0]" type="text" placeholder="255文字以内" class="c-setting__form__text "
            v-if="currentLikeForm === 'or'">
            <input v-model="list.ng[0]" type="text" placeholder="255文字以内" class="c-setting__form__text"
            v-else-if="currentLikeForm === 'ng'">
            <input v-model="list.normal[0]" type="text" placeholder="255文字以内" class="c-setting__form__text"
            v-else>
        <input type="button" value="追加" v-bind:disabled="disabledFlag" @click="setKeyWord" class="c-setting__form__button">
        {{maxNum-currentNum}}
        <div class="c-error u-margin--error">{{ LikeError }}</div>
    </div>
</template>
<script>
import { mapState } from 'vuex'
import { OK } from '../../../../../../util'

export default {
    data(){
        return{
            list:{
                or:[],
                ng:[],
                normal:[]
            },
            maxNum:255,
            currentNum:0,
            type1:'normal',
            type2:'or',
            type3:'ng',
            disabledFlag :false,
            message:{
                duplicate:'ワードで重複したため登録ができません。',
                length_limit:'255文字以内にしてください。',
                failed_register:'登録に失敗しました。再度お試しください。'
            }
        }
    },
    methods:{
        //配列同士で重複する値があるか確認する
        getIsDuplicate(arr1, arr2) {
            return [...arr1, ...arr2].filter(item => arr1.includes(item) && arr2.includes(item)).length > 0
        },
        //重複したワードにエラーを表示する。
        duplicateCheck(key,type){
            switch (key){
                case this.type1:
                    if(type === this.type1){
                        type = '必須' //normalワードは必須ワードと表現するため。
                    }
                    this.$store.commit('autoActionSetting/setLikeError',`${type}`+this.message.duplicate)
                    break;
                case this.type2:
                    if(type === this.type1){
                        type = '必須'
                    }
                    this.$store.commit('autoActionSetting/setLikeError',`${type}`+this.message.duplicate)
                    break;
                case this.type3:
                    if(type === this.type1){
                        type = '必須'
                    }
                    this.$store.commit('autoActionSetting/setLikeError',`${type}`+this.message.duplicate)
                    break;
            }
        },
        validation(){
            //keyにはor ng normal　がそれぞれ順番に入る
            for(let key in this.list){ 
                //配列が空の場合は次の配列をしらべる 
                if(this.list[key].length <=0){ 
                    continue;
                }
                //空白が入っている場合は削除する
                if(this.list[key][0].trim() ==''){ 
                    this.list[key] = []
                    continue;
                }
                //配列同士で重複する値がある場合エラーを入れる.
                if(this.getIsDuplicate(this.list[key],this.LikeWordList[this.type1])){
                    this.duplicateCheck(key,this.type1)
                    for(let key in this.list){
                    this.list[key] = []
                    return false;
                }
                }else if(this.getIsDuplicate(this.list[key],this.LikeWordList[this.type2])){
                    this.duplicateCheck(key,this.type2)
                    for(let key in this.list){
                    this.list[key] = []
                    return false;
                }
                }else if(this.getIsDuplicate(this.list[key],this.LikeWordList[this.type3])){
                    this.duplicateCheck(key,this.type3)
                    for(let key in this.list){
                    this.list[key] = []
                    return false;
                }
                }
            }
            return true;
        },

        //キーワードをDBとautoActionSettingに保存する。
        async setKeyWord(){
            this.$store.commit('autoActionSetting/setLikeLoad',true)
            this.$store.commit('autoActionSetting/setLikeError','')
            this.currentNum = 0
            if(this.list.or.length >this.maxNum || this.list.ng.length >this.maxNum || this.list.normal.length >this.maxNum){
                this.$store.commit('autoActionSetting/setLikeError',this.message.length_limit)
                return false
            }
            const bool= await this.validation()
            if(!bool){
                this.$store.commit('autoActionSetting/setLikeLoad',false)
                return false
            }
            const response = await axios.post('/api/saveLikeWordList',{
                word_list: this.list,
                twitter_screen_name: this.twitter_screen_name
            })
            if(response.status !== OK){
                for(let i in this.list){
                    this.list[i] = []
                }
                this.$store.commit('autoActionSetting/setLikeLoad',false)
                this.$store.commit('autoActionSetting/setLikeError',this.message.failed_register)
                return false
            }
            this.$store.dispatch('autoActionSetting/setLikeWordList',this.list)
            for(let i in this.list){
                    this.list[i] = []
            }
            this.$store.commit('autoActionSetting/setLikeLoad',false)
        },
        //入力された文字数を調べる。オーバーしていたらautoActionSettingにエラ〜メッセージを格納する。
        count(num){
            this.currentNum = num
            if(this.currentNum >this.maxNum){
                this.$store.commit('autoActionSetting/setLikeError',this.message.length_limit)
                this.disabledFlag = true
            }else{
                this.disabledFlag = false
            }
        },
        
    },
    watch:{
        'list.or': function(){
            if(this.list.or[0]){
                this.count(this.list.or[0].length)
            }else{
                this.count(0)
            }
        },
        'list.ng': function(){
            if(this.list.ng[0]){
                this.count(this.list.ng[0].length)
            }else{
                this.count(0)
            }
        },
        'list.normal': function(){
            if(this.list.normal[0]){
                this.count(this.list.normal[0].length)
            }else{
                this.count(0)
            }
        },
        currentLikeForm:function(){
            if(this.currentLikeForm === this.type1){
                this.list.or = []
                this.list.ng = []
                if(this.list.normal[0]){
                    this.count(this.list.normal[0].length)
                }else{
                    this.count(0)
                }
            }else if(this.currentLikeForm === this.type2){
                this.list.normal = []
                this.list.ng = []
                if(this.list.or[0]){
                    this.count(this.list.or[0].length)
                }else{
                    this.count(0)
                }
            }else if(this.currentLikeForm === this.type3){
                this.list.normal = []
                this.list.or = []
                if(this.list.or[0]){
                    this.count(this.list.or[0].length)
                }else{
                    this.count(0)
                }
            }
        }
    },
    computed:{
        ...mapState({
            twitter_screen_name: state => state.twitter.twitter_screen_name,
            currentLikeForm: state => state.autoActionSetting.currentLikeForm,
            LikeWordList: state => state.autoActionSetting.LikeWordList,
            LikeError: state => state.autoActionSetting.LikeError
        }),
    },
    
}
</script>